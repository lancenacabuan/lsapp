<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\RequestTransfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stocktransfer(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        return view('/pages/stocktransfer');
    }
    
    public function generateReqNum(Request $request){
        $reqnum = RequestTransfer::query()->select()
            ->where('request_number',$request->request_number)
            ->count();
            if($reqnum == 0){
                return response('unique');
            }
            return response('duplicate');
    }

    public function setcategory(Request $request){
        $list = Category::query()->select('categories.id AS category_id','categories.category AS category')
            ->join('stocks','category_id','categories.id')
            ->where('stocks.location_id',$request->location_id)
            ->where('stocks.status','in')
            ->groupBy('category_id')
            ->orderBy('category','ASC')
            ->get();
        
        return response()->json($list);
    }

    public function setitems(Request $request){
        $list = Item::query()->select('items.id AS item_id','items.item AS item')
            ->join('stocks','item_id','items.id')
            ->where('items.category_id',$request->category_id)
            ->where('stocks.location_id',$request->location_id)
            ->where('stocks.status','in')
            ->groupBy('item_id')
            ->orderBy('item','ASC')
            ->get();
        
        return response()->json($list);
    }

    public function qtystock(Request $request){       
        $list = Stock::query()->select('items.id')
            ->join('items','items.id','item_id')
            ->where('stocks.status','in')
            ->where('stocks.location_id',$request->location_id)
            ->where('stocks.item_id',$request->item_id)
            ->count();

        return response($list);
    }

    public function qtystockless(Request $request){       
        $list = Stock::query()->select('items.id')
            ->join('items','items.id','item_id')
            ->where('stocks.status','in')
            ->where('stocks.location_id',$request->location_id)
            ->where('stocks.item_id',$request->item_id)
            ->count();
        $list=$list-$request->qty;
        
        return response($list);
    }
}