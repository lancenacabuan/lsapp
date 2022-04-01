<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Category;
use App\Models\Item;
use App\Models\Part;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\RequestTransfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class AssemblyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function assembly(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder')) //---ROLES---//
        {
            return redirect('/');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');
        $items = Item::select('id','item')->where('assemble','YES')->get()->sortBy('item');
        return view('/pages/assembly', compact('categories','items'));
    }

    public function generateReqNum(Request $request){
        $reqnumR = Requests::query()->select()->where('request_number',$request->request_number)->count();
        $reqnumT = RequestTransfer::query()->select()->where('request_number',$request->request_number)->count();
        $reqnum = $reqnumR + $reqnumT;
        if($reqnum == 0){
            return response('unique');
        }
        return response('duplicate');
    }

    public function itemsAssembly(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }

    public function uomAssembly(Request $request){       
        $uom = Item::query()->select('UOM as uom')
            ->where('id',$request->item_id)
            ->get();
        $uom = str_replace('[{"uom":"','',$uom);
        $uom = str_replace('"}]','',$uom);
        
        return response($uom);
    }

    public function saveReqNum(Request $request){
        $requests = new Requests;
        $requests->request_number = $request->request_number;
        $requests->requested_by = auth()->user()->id;
        $requests->needdate = $request->needdate;
        $requests->request_type = $request->request_type;
        $requests->status = '1';
        $requests->item_id = $request->item_id;
        $sql = $requests->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function saveRequest(Request $request){
        $stockRequest = new StockRequest;
        $stockRequest->request_number = $request->request_number;
        $stockRequest->category = $request->category;
        $stockRequest->item = $request->item;
        $stockRequest->quantity = $request->quantity;
        $stockRequest->served = '0';
        $stockRequest->pending = $request->quantity;
        $sql = $stockRequest->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function logSave(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW STOCK REQUEST: User successfully saved Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function createItem(Request $request){
        if(trim($request->item) != ''){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item))
                ->count();
        }
        else{
            $item = 0;
        }
        if($item != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $items = new Item;
            $items->created_by = auth()->user()->id;
            $items->item = ucwords($request->item);
            $items->category_id = '58';
            $items->UOM = 'Unit';
            $items->assemble = 'YES';
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
            $data = array('result' => $result, 'id' => $id);
            return response()->json($data);
        }
    }

    public function saveParts(Request $request){
        $items = Item::query()->select('id','category_id')
                ->where('item',htmlspecialchars_decode($request->item))
                ->first();

        $parts = new Part;
        $parts->item_id = $request->item_id;
        $parts->part_id = $items->id;
        $parts->quantity = $request->quantity;
        $sql = $parts->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function logItem(Request $request){
        $item = ucwords($request->item);

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "ASSEMBLED ITEM ADDED: User successfully saved new Assembled Item '$item' with ItemID#$request->item_id.";
        $userlogs->save();

        return response('true');
    }

    public function itemDetails(Request $request){
        $itemDetails = Part::query()->select('categories.category','items.item','items.UOM AS uom','quantity')
            ->join('items', 'items.id', 'parts.part_id')
            ->join('categories', 'categories.id', 'items.category_id')
            ->where('parts.item_id',$request->item_id)
            ->orderBy('category','ASC')
            ->orderBy('item','ASC')
            ->get();
        
        return DataTables::of($itemDetails)->make(true);
    }

    public function changeItem(Request $request){
        if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
                ->count();
        }
        else{
            $item = 0;
        }
        if($item != 0){
            $result = 'duplicate';
        }
        else {
            $item_name = ucwords($request->item_name);
                
            $items = Item::find($request->item_id);
            $items->created_by = auth()->user()->id;
            $items->item = $item_name;
            $items->category_id = '58';
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
                
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "ASSEMBLED ITEM UPDATED: User successfully updated Assembled Item Description from '$request->item_name_original' into '$item_name' with ItemID#$id.";
                $userlogs->save();
            }
        }

        return response($result);
    }

    public function partsDetails(Request $request){
        $partsDetails = Part::query()->select('categories.category','items.item','items.UOM AS uom','quantity','items.id AS item_id','items.category_id AS category_id')
            ->join('items', 'items.id', 'parts.part_id')
            ->join('categories', 'categories.id', 'items.category_id')
            ->where('parts.item_id',$request->item_id)
            ->orderBy('category','ASC')
            ->orderBy('item','ASC')
            ->get();
        
        return DataTables::of($partsDetails)
        ->addColumn('main', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('balintawak', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['5'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('malabon', function(Part $partsDetails){
            $stocks = Stock::query()
                ->where('item_id', $partsDetails->item_id)
                ->whereIn('location_id', ['6'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->make(true);
    }
}