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
use App\Models\Prepare;
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
        do{
            $requests = new Requests;
            $requests->request_number = $request->request_number;
            $requests->requested_by = auth()->user()->id;
            $requests->needdate = $request->needdate;
            $requests->request_type = $request->request_type;
            $requests->status = '1';
            $requests->item_id = $request->item_id;
            $requests->qty = $request->qty;
            $sql = $requests->save();
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function saveRequest(Request $request){
        do{
            $stockRequest = new StockRequest;
            $stockRequest->request_number = $request->request_number;
            $stockRequest->category = $request->category;
            $stockRequest->item = $request->item;
            $stockRequest->quantity = $request->quantity * $request->qty;
            $stockRequest->served = '0';
            $stockRequest->pending = $request->quantity * $request->qty;
            $sql = $stockRequest->save();
        }
        while(!$sql);

        return response('true');
    }

    public function logSave(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW ASSEMBLY STOCK REQUEST: User successfully saved Assembly Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function request_data(){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, needdate, requests.item_id AS item_id, items.item AS item_desc, qty, assembly_reqnum')
        ->where('requests.requested_by', auth()->user()->id)
        ->whereIn('requests.request_type', ['4','5'])
        ->whereNotIn('requests.status', ['7','8','10','11','14'])
        ->join('users', 'users.id', '=', 'requests.requested_by')
        ->join('request_type', 'request_type.id', '=', 'requests.request_type')
        ->join('status', 'status.id', '=', 'requests.status')
        ->join('items', 'items.id', '=', 'requests.item_id')
        ->orderBy('requests.created_at', 'DESC')
        ->get();

        return DataTables::of($list)
        ->addColumn('prep_by', function (Requests $list){
            $users = User::query()
                ->select('name')
                ->where('id', $list->prepared_by)
                ->get();
            $users = str_replace("[{\"name\":\"","",$users);
            $users = str_replace("\"}]","",$users);
            
            return $users;
        })
        ->make(true);
    }

    public function receiveRequest(Request $request){
        if($request->inc == 'true'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '15']);
            }
            while(!$sql);
        }
        else{
            if($request->request_type == '4'){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '19']);
                }
                while(!$sql);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '12']);
                }
                while(!$sql);
            }
        }
                
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
            
            if($request->request_type == '4'){
                do{
                    $sql = Requests::where('request_number', $request->assembly_reqnum)
                        ->update(['status' => '20']);
                }
                while(!$sql);
            }
        }

        return response($result);
    }

    public function receiveItems(Request $request){
        if($request->status == '3'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'received']);
            }
            while(!$sql);
        }
        if($request->status == '17'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'assembly']);
            }
            while(!$sql);
        }
        
        return response('true');
    }

    public function logReceive(Request $request){
        if($request->status == '3'){
            Stock::where('request_number', $request->request_number)
                ->where('status', '!=', 'received')
                ->update(['status' => 'incomplete']);
            
            Stock::where('request_number', $request->request_number)
                ->where('status', '=', 'received')
                ->update(['status' => 'assembly']);
        }

        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE ASSEMBLY STOCK REQUEST: User successfully received incomplete needed parts of Assembly Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE ASSEMBLY STOCK REQUEST: User successfully received complete needed parts of Assembly Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
    }

    public function defectiveRequest(Request $request){
        do{
            $status = Requests::where('request_number', $request->request_number)
                ->update(['status' => '18']);
        }
        while(!$status);
        
        do{
            $list = Requests::selectRaw('needdate')
                ->where('request_number', $request->request_number)
                ->get();
        }
        while(!$list);
        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);
        
        do{
            $requests = new Requests;
            $requests->request_number = $request->generatedReqNum;
            $requests->assembly_reqnum = $request->request_number;
            $requests->requested_by = auth()->user()->id;
            $requests->needdate = $list->needdate;
            $requests->request_type = '4';
            $requests->status = '1';
            $sql = $requests->save();
        }
        while(!$sql);
                
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function defectiveItems(Request $request){
        do{
            $list = Stock::selectRaw('category_id, item_id')
                ->where('id', $request->id)
                ->get();
        }
        while(!$list);
        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        $count = StockRequest::query()->select()
            ->where('request_number',$request->generatedReqNum)
            ->where('item',$list->item_id)
            ->count();

        if($count == '0'){
            do{
                $stockRequest = new StockRequest;
                $stockRequest->request_number = $request->generatedReqNum;
                $stockRequest->category = $list->category_id;
                $stockRequest->item = $list->item_id;
                $stockRequest->quantity = '1';
                $stockRequest->served = '0';
                $stockRequest->pending = '1';
                $dump = $stockRequest->save();
            }
            while(!$dump);
        }
        else{
            do{
                $qty = StockRequest::where('request_number', $request->generatedReqNum)
                    ->where('item',$list->item_id)
                    ->increment('quantity');
            }
            while(!$qty);
            do{
                $pend = StockRequest::where('request_number', $request->generatedReqNum)
                    ->where('item',$list->item_id)
                    ->increment('pending');
            }
            while(!$pend);
        }

        do{
            $sql = Stock::where('id', $request->id)
                ->update(['status' => 'defective']);
        }
        while(!$sql);
        
        return response('true');
    }

    public function logDefective(Request $request){

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "REQUESTED DEFECTIVE REPLACEMENTS: User successfully requested replacements for defective parts of Assembly Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function assembleRequest(Request $request){
        do{
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '13']);
        }
        while(!$sql);
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "ASSEMBLED FOR RECEIVING: User successfully Assembled Item/s for receiving Assembly Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function receiveAssembled(Request $request){
        do{
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '14']);
        }
        while(!$sql);
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            Stock::where('request_number', $request->request_number)
                ->update(['status' => 'out']);
        }

        return response($result);
    }

    public function addAssembled(Request $request){
        do{
            $stocks = new Stock;
            $stocks->request_number = $request->request_number;
            $stocks->item_id = $request->item_id;
            $stocks->category_id = '58';
            $stocks->user_id =auth()->user()->id;
            $stocks->location_id =$request->location_id;
            $stocks->status = 'in';
            $stocks->serial = $request->serial;
            $stocks->rack = 'N/A';
            $stocks->row = 'N/A';
            $sql = $stocks->save();
        }
        while(!$sql);

        return response('true');
    }

    public function logAssembled(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "RECEIVED ASSEMBLED ITEM: User successfully received Assembled Item/s with Assembly Stock Request No. $request->request_number into warehouse stocks.";
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