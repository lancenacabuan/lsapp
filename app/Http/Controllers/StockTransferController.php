<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\RequestTransfer;
use App\Models\StockTransfer;
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

    public function saveTransReqNum(Request $request){
        $requests = new RequestTransfer;
        $requests->request_number = $request->request_number;
        $requests->requested_by = auth()->user()->id;
        $requests->needdate = $request->needdate;
        $requests->locfrom = $request->locfrom;
        $requests->locto = $request->locto;
        $requests->status = '6';
        $sql = $requests->save();
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        return response($result);
    }
    
    public function saveTransRequest(Request $request){
        $items = Item::query()->select('id','category_id')
                ->where('item',htmlspecialchars_decode($request->item))
                ->first();

        $stockTransfer = new StockTransfer;
        $stockTransfer->request_number = $request->request_number;
        $stockTransfer->category = $items->category_id;
        $stockTransfer->item = $items->id;
        $stockTransfer->quantity = $request->quantity;
        $stockTransfer->served = '0';
        $stockTransfer->pending = $request->quantity;
        $sql = $stockTransfer->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function logTransSave(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW STOCK TRANSFER REQUEST: User successfully saved Stock Transfer Request No. $request->request_number.";
        $userlogs->save();
        
        return true;
    }

    public function transfer_data()
    {
        if(auth()->user()->hasanyRole('approver - warehouse')){ //---ROLES---//
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, locations.location AS location, prepared_by, reason, needdate, locfrom, locto')
            ->whereIn('request_transfer.status', ['6','7'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->join('locations', 'locations.id', '=', 'request_transfer.locto')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        }
        else if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('viewer')){ //---ROLES---//
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, locations.location AS location, prepared_by, reason, needdate, locfrom, locto')
            ->whereNotIn('request_transfer.status', ['7','8'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->join('locations', 'locations.id', '=', 'request_transfer.locto')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        }
        else{
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, locations.location AS location, prepared_by, reason, needdate, locfrom, locto')
            ->where('request_transfer.requested_by', auth()->user()->id)
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->join('locations', 'locations.id', '=', 'request_transfer.locto')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        }

        return DataTables::of($list)
        ->addColumn('prep_by', function (RequestTransfer $list){
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

    public function transferDetails(Request $request){
        $stockreq = StockTransfer::query()->select('categories.category','items.item','items.id as item_id','quantity','served','pending')
            ->join('categories', 'categories.id', 'stock_transfer.category')
            ->join('items', 'items.id', 'stock_transfer.item')
            ->where('request_number',$request->reqnum)
            ->groupBy('category','items.item','quantity','served','pending','item_id')
            ->get();        
        
        return DataTables::of($stockreq)
        ->addColumn('qtystock', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->whereNotIn('location_id', ['5','6'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya1', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '1')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya2', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '2')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya3', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '3')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya4', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '4')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtybal', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '5')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtymal', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '6')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->make(true);
    }
}