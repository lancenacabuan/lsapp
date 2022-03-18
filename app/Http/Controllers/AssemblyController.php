<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Item;
use App\Models\Part;
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
        $categories = Category::select('id','category')->get()->sortBy('category');
        $items = Item::select('id','item')->take(100)->get()->sortBy('item');
        return view('/pages/assembly', compact('categories','items'));
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
            $items->item = ucwords($request->item);
            $items->category_id = $request->category_id;
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
        $userlogs->activity = "ASSEMBLY ITEM ADDED: User successfully saved new Assembly Item '$item' with ItemID#$request->item_id under Category '$request->category'.";
        $userlogs->save();

        return response('true');
    }
}