<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\RequestTransfer;
use App\Models\StockTransfer;
use App\Models\Transfer;
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

    public function approveTransfer(Request $request){
        $sql = RequestTransfer::where('request_number', $request->request_number)
            ->update(['status' => '1', 'reason' => '']);
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "APPROVED STOCK TRANSFER REQUEST: User successfully approved Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function disapproveTransfer(Request $request){
        $sql = RequestTransfer::where('request_number', $request->request_number)
            ->update(['status' => '7', 'reason' => ucfirst($request->reason)]);
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DISAPPROVED STOCK TRANSFER REQUEST: User successfully disapproved Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function receiveTransfer(Request $request){
        $sql = RequestTransfer::where('request_number', $request->request_number)
            ->update(['status' => '8']);
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED STOCK TRANSFER REQUEST: User successfully received Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function deleteTransfer(Request $request){
        do{
            $sqlquery = StockTransfer::where('request_number', $request->request_number)->delete();
        }
        while(!$sqlquery);
        
        $sql = RequestTransfer::where('request_number', $request->request_number)->delete();
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DELETED STOCK TRANSFER REQUEST: User successfully deleted Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function delTransItem(Request $request){
        $transitems = StockTransfer::where('request_number', $request->req_num)
            ->where('item', $request->item_id)
            ->delete();
        
        if(!$transitems){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        $count = StockTransfer::where('request_number', $request->req_num)->count();
        if($count == 0){
            RequestTransfer::where('request_number', $request->req_num)->delete();
        }

        $data = array('result' => $result, 'count' => $count);
        return response()->json($data);
    }

    public function stocktrans(Request $request){       
        $list = StockTransfer::select('categories.category AS category', 'items.item AS item', 'items.id AS item_id', 'stock_transfer.quantity AS qty', 'stock_transfer.served AS served', 'stock_transfer.pending AS pending', 'items.UOM AS uom')
            ->where('stock_transfer.item', $request->item_id)
            ->where('stock_transfer.request_number', $request->reqnum)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',[$request->location])
            ->join('categories','categories.id','=','stock_transfer.category')
            ->join('items','items.id','=','stock_transfer.item')
            ->join('stocks','stocks.item_id','stock_transfer.item')
            ->join('locations','locations.id','stocks.location_id')
            ->groupBy('category','item','item_id','qty','served','pending', 'uom')
            ->get();

        return DataTables::of($list)
        ->addColumn('qtybal', function (StockTransfer $list){
            $stocks = Stock::query()
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['5'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtymal', function (StockTransfer $list){
            $stocks = Stock::query()
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['6'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('serialbal', function (StockTransfer $list){
            $stocks = Stock::query()->select('serial')
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['5'])
                ->where('status', 'in')
                ->first();
            $stocks = str_replace('{"serial":"','',$stocks);
            $stocks = str_replace('"}','',$stocks);
            $stocks = str_replace('{"serial":null}','',$stocks);
            return $stocks;
        })
        ->addColumn('serialmal', function (StockTransfer $list){
            $stocks = Stock::query()->select('serial')
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['6'])
                ->where('status', 'in')
                ->first();
            $stocks = str_replace('{"serial":"','',$stocks);
            $stocks = str_replace('"}','',$stocks);
            $stocks = str_replace('{"serial":null}','',$stocks);
            return $stocks;
        })
        ->toJson();
    }

    public function settransserials(Request $request){
        $list = Stock::select('serial','location_id')
            ->where('stocks.item_id', $request->item_id)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',[$request->location])
            ->get();
        
        return response()->json($list);
    }

    public function transferItems(Request $request){
        if($request->serial == ''){
            $count = Transfer::query()
                ->where('request_number', $request->request_number)
                ->where('items_id',$request->item_id)
                ->count();
        }
        else{
            $count = 0;
        }
        if($count == 0){
            $transfer = new Transfer;
            $transfer->request_number = $request->request_number;
            $transfer->user_id = auth()->user()->id;
            $transfer->items_id = $request->item_id;
            $transfer->locfrom = $request->locfrom;
            $transfer->locto = $request->locto;
            $transfer->serial = $request->serial;
            $transfer->qty = $request->qty;
            $transfer->intransit = 'no';
            $transfer->schedule = $request->schedOn;
            $sql = $transfer->save();
        }
        else{
            $sql = Transfer::where('request_number', $request->request_number)
                ->where('items_id',$request->item_id)
                ->increment('qty', $request->qty);
        }
        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }
        if($result == 'true'){
            if($request->serial != ''){
                Stock::where('item_id',$request->item_id)
                    ->whereIn('location_id',[$request->locfrom])
                    ->where('status','in')
                    ->where('serial',$request->serial)
                    ->orderBy('id')->limit(1)
                    ->update(['status' => 'trans', 'location_id' => $request->locto]);
            }
            else{
                Stock::where('item_id',$request->item_id)
                    ->whereIn('location_id',[$request->locfrom])
                    ->where('status','in')
                    ->orderBy('id')->limit($request->qty)
                    ->update(['status' => 'trans', 'location_id' => $request->locto]);
            }
            
            StockTransfer::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->increment('served', $request->qty);

            StockTransfer::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->decrement('pending', $request->qty);

            RequestTransfer::where('request_number', $request->request_number)
                ->update(['prepared_by' => auth()->user()->id, 'schedule' => $request->schedOn]);

            $total = StockTransfer::where('request_number', $request->request_number)->sum('pending');
            if($total == 0){
                RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '2']);
            }
            else{
                RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '5']);
            }
        }
        
        return response($result);
    }

    public function logTransSched(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "SCHEDULED STOCK TRANSFER REQUEST: User successfully scheduled on $request->schedOn Stock Transfer Request No. $request->request_number.";
        $userlogs->save();
        
        return true;
    }

    public function transItems(Request $request)
    {
        $list = Transfer::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, transferred_items.serial AS serial, transferred_items.qty AS qty, transferred_items.items_id AS item_id, transferred_items.id AS id, locations.location AS location')
            ->where('request_number', $request->request_number)
            ->join('items','items.id','transferred_items.items_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','transferred_items.locfrom')
            ->get()
            ->sortBy('item')
            ->sortBy('category');

        return DataTables::of($list)->make(true);
    }

    public function printTransferRequest(Request $request){
        $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS req_date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, users.name AS req_by, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, prepared_by, needdate, locfrom, locto')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        $list2 = Transfer::selectRaw('users.name AS prep_by, transferred_items.updated_at AS prep_date')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'transferred_items.user_id')
            ->orderBy('transferred_items.id','DESC')
            ->first();
        
        $list3 = Transfer::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, transferred_items.serial AS serial, transferred_items.qty AS qty, transferred_items.items_id AS item_id, transferred_items.id AS id, locations.location AS location')
            ->where('request_number', $request->request_number)
            ->join('items','items.id','transferred_items.items_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','transferred_items.locfrom')
            ->get()
            ->sortBy('item')
            ->sortBy('category');
            
        return view('/pages/stockTransfer/printStockTransfer', compact('list','list2','list3'));
    }
}