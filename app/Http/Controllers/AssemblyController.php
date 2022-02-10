<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Stock;
use App\Models\Item;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class AssemblyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function assembly(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('warehouse approver'))
        {
            return redirect('/stockrequest');
        }
        $categories= Category::select('id','category')->get()->sortBy('category');
        return view('/pages/assembly', compact('categories'));
    }

    public function itemsItm(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }
}