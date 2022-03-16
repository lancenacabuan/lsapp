<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Item;
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
}