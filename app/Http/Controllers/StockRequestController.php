<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\RequestType;
use App\Models\Status;
use App\Models\Prepare;
use App\Models\Item;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stockrequest(){
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }  
        $categories= Category::select('id','category')->get()->sortBy('category');
        if(auth()->user()->hasanyRole('sales')){
            $req_types= RequestType::select('id','name')
            ->whereIn('id',['2','3','4'])
            ->get();            
        }
        else{
            $req_types= RequestType::select('id','name')->get()->sortBy('name');
        }
        $items= Item::select('id','item')->get()->sortBy('item');
        // $categories= Category::select('categories.id','category')
        //     ->join('stocks','category_id','categories.id')
        //     ->where('status','in')
        //     ->groupBy('categories.id')
        //     ->get()->sortBy('category');

        return view('/pages/stockrequest', compact('categories','req_types','items'));
    }

    public function stockreq(Request $request){       
        $list = StockRequest::select('categories.category AS category', 'items.item AS item', 'items.id AS item_id', 'stock_request.quantity AS qty', 'stock_request.served AS served', 'stock_request.pending AS pending', 'items.UOM AS uom')
            ->where('stock_request.item', $request->item_id)
            ->where('stock_request.request_number', $request->reqnum)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',['1','2','3','4'])
            ->join('categories','categories.id','=','stock_request.category')
            ->join('items','items.id','=','stock_request.item')
            ->join('stocks','stocks.item_id','stock_request.item')
            ->join('locations','locations.id','stocks.location_id')
            ->groupBy('category','item','item_id','qty','served','pending', 'uom')
            ->get();

        return DataTables::of($list)
        ->addColumn('qtystock', function (StockRequest $list){
            $stocks = Stock::query()
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('serial', function (StockRequest $list){
            $stocks = Stock::query()->select('serial')
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->first();
            $stocks = str_replace('{"serial":"','',$stocks);
            $stocks = str_replace('"}','',$stocks);
            $stocks = str_replace('{"serial":null}','',$stocks);
            return $stocks;
        })
        ->toJson();
    }

    public function setserials(Request $request){
        $list = Stock::select('serial','location_id')
            ->where('stocks.item_id', $request->item_id)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',['1','2','3','4'])
            ->get();
        
        return response()->json($list);
    }
    
    public function setlocation(Request $request){       
        $list = Stock::query()->select('stocks.location_id AS location_id','locations.location AS location')
            ->join('locations','locations.id','stocks.location_id')
            ->where('stocks.serial',$request->serial_id)
            ->get();

        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        return ($list);
    }

    public function request_data()
    {
        if(auth()->user()->hasanyRole('approver - sales')){ //---ROLES---//
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.request_type AS req_type, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason')
            ->whereIn('requests.status', ['6','7'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get();
        }
        else if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')){ //---ROLES---//
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.request_type AS req_type, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason')
            ->whereNotIn('requests.status', ['7','8'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get();
        }
        else{
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.request_type AS req_type, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason')
            ->where('requests.requested_by', auth()->user()->id)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get();
        }

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

    public function schedItems(Request $request)
    {
        $list = Prepare::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, prepared_items.serial AS serial, prepared_items.qty AS qty, prepared_items.items_id AS item_id, prepared_items.id AS id, locations.location AS location')
            ->where('request_number', $request->request_number)
            ->join('items','items.id','prepared_items.items_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','prepared_items.location')
            ->get()
            ->sortBy('item')
            ->sortBy('category');

        return DataTables::of($list)->make(true);
    }

    public function itemsreq(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            // ->join('stocks','stocks.item_id','items.id')
            ->where('items.category_id',$request->category_id)
            // ->where('stocks.status','in')
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }

    // public function itemsqty(Request $request){       
    //     $list = Stock::query()->select('items.id')
    //         ->join('items','items.id','item_id')
    //         ->where('stocks.status','in')
    //         ->where('stocks.item_id',$request->item_id)
    //         ->count();

    //         return response($list);
    // }

    // public function itemsstock(Request $request){       
    //     $list = Stock::query()->select('items.id')
    //         ->where('stocks.status','in')
    //         ->where('stocks.item_id',$request->item_id)
    //         ->count();
    //     $list=$list-$request->qty;
        
    //     return response($list);
    // }

    public function generatedr(Request $request){
        $dr = Requests::query()->select()
            ->where('request_number',$request->request_number)
            ->count();
            if($dr == 0){
                return response('unique');
            }
            return response('duplicate');
    }

    public function prepareItems(Request $request){
        if($request->serial == ''){
            $count = Prepare::query()
                ->where('request_number', $request->request_number)
                ->where('items_id',$request->item_id)
                ->count();
        }
        else{
            $count = 0;
        }
        if($count == 0){
            $prepare = new Prepare;
            $prepare->request_number = $request->request_number;
            $prepare->user_id = auth()->user()->id;
            $prepare->items_id = $request->item_id;
            $prepare->location = $request->location;
            $prepare->serial = $request->serial;
            $prepare->qty = $request->qty;
            $prepare->intransit = 'no';
            $prepare->schedule = $request->schedOn;
            $sql = $prepare->save();
        }
        else{
            $sql = Prepare::where('request_number', $request->request_number)
                ->where('items_id',$request->item_id)
                ->increment('qty', $request->qty);
        }
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        if($result == 'true'){
            if($request->serial != ''){
                Stock::where('item_id',$request->item_id)
                    ->whereIn('location_id',['1','2','3','4'])
                    ->where('status','in')
                    ->where('serial',$request->serial)
                    ->orderBy('id')->limit(1)
                    ->update(['status' => 'out']);
            }
            else{
                Stock::where('item_id',$request->item_id)
                    ->whereIn('location_id',['1','2','3','4'])
                    ->where('status','in')
                    ->orderBy('id')->limit($request->qty)
                    ->update(['status' => 'out']);
            }
            
            StockRequest::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->increment('served', $request->qty);

            StockRequest::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->decrement('pending', $request->qty);

            Requests::where('request_number', $request->request_number)
                ->update(['prepared_by' => auth()->user()->id, 'schedule' => $request->schedOn]);

            $total = StockRequest::where('request_number', $request->request_number)->sum('pending');
            if($total == 0){
                Requests::where('request_number', $request->request_number)
                    ->update(['status' => '2']);
            }
            else{
                Requests::where('request_number', $request->request_number)
                    ->update(['status' => '5']);
            }
        }
        
        return response($result);
    }

    public function logSched(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "SCHEDULED STOCK REQUEST: User successfully scheduled on $request->schedOn Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return true;
    }

    public function approveRequest(Request $request){
        $sql = Requests::where('request_number', $request->request_number)
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
            $userlogs->activity = "APPROVED STOCK REQUEST: User successfully approved Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function disapproveRequest(Request $request){
        $sql = Requests::where('request_number', $request->request_number)
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
            $userlogs->activity = "DISAPPROVED STOCK REQUEST: User successfully disapproved Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function receiveRequest(Request $request){
        $sql = Requests::where('request_number', $request->request_number)
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
            $userlogs->activity = "RECEIVED STOCK REQUEST: User successfully received Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function saveReqNum(Request $request){
        $requests = new Requests;
        $requests->request_number = $request->request_number;
        $requests->requested_by = auth()->user()->id;
        $requests->request_type = $request->request_type;
        $requests->status = '6';
        $requests->client_name = ucwords($request->client_name);
        $requests->location = ucwords($request->location);
        $requests->reference = strtoupper($request->reference);
        $saved = $requests->save();
        if(!$saved){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        return response($result);
    }
    
    public function saveRequest(Request $request){
        $items = Item::query()->select('id','category_id')
                ->where('item',htmlspecialchars_decode($request->item))
                ->first();

        $stockRequest = new StockRequest;
        $stockRequest->request_number = $request->request_number;
        $stockRequest->category = $items->category_id;
        $stockRequest->item = $items->id;
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
        
        return true;
    }

    public function requestDetails(Request $request){
        $stockreq = StockRequest::query()->select('categories.category','items.item','items.id as item_id','quantity','served','pending')
            ->join('categories', 'categories.id', 'stock_request.category')
            ->join('items', 'items.id', 'stock_request.item')
            ->where('request_number',$request->reqnum)
            ->groupBy('category','items.item','quantity','served','pending','item_id')
            ->get();        
        
        return DataTables::of($stockreq)
        ->addColumn('qtystock', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->whereNotIn('location_id', ['5','6'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya1', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '1')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya2', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '2')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya3', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '3')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya4', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '4')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtybal', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '5')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtymal', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '6')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->make(true);
    }
    
    public function deleteRequest(Request $request){
        do{
            $sqlquery = StockRequest::where('request_number', $request->request_number)->delete();
        }
        while(!$sqlquery);
        
        $sql = Requests::where('request_number', $request->request_number)->delete();
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DELETED STOCK REQUEST: User successfully deleted Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function delReqItem(Request $request){
        $reqitems = StockRequest::where('request_number', $request->req_num)
            ->where('item', $request->item_id)
            ->delete();
        
        if(!$reqitems){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        $count = StockRequest::where('request_number', $request->req_num)->count();
        if($count == 0){
            Requests::where('request_number', $request->req_num)->delete();
        }

        $data = array('result' => $result, 'count' => $count);
        return response()->json($data);
    }

    public function editSerial(Request $request){
        $prepitems = Prepare::where('id', $request->id)
            ->update(['serial' => $request->serial]);
        
        if(!$prepitems){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function inTransit(Request $request){
        Requests::where('request_number', $request->request_number)
            ->where('status','2')
            ->update(['status' => '3']);

        Requests::where('request_number', $request->request_number)
            ->where('status','5')
            ->update(['status' => '4']);
        
        Prepare::where('request_number', $request->request_number)
            ->update(['intransit' => 'yes']);

        // $return = StockRequest::select('item','served')
        //     ->where('request_number', $request->request_number)
        //     ->where('served','>',0)
        //     ->get();

        // foreach($return as $val){
        //     $item = $val->item;
        //     $served = $val->served;
        //     for($i = 0; $i < $served; $i++){
        //         Stock::where('item_id',$item)
        //             ->where('status','prep')
        //             ->orderBy('id')->limit(1)
        //             ->update(['status' => 'out']);
        //     }
        // }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "FOR RECEIVING STOCK REQUEST: User successfully processed for receiving Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function printRequest(Request $request){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS req_date, requests.request_number AS req_num, requests.request_type AS req_type, requests.requested_by AS user_id, users.name AS req_by, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get();
        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        $list2 = Prepare::selectRaw('users.name AS prep_by, prepared_items.updated_at AS prep_date')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'prepared_items.user_id')
            ->orderBy('prepared_items.id','DESC')
            ->first();
        
        $list3 = Prepare::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, prepared_items.serial AS serial, prepared_items.qty AS qty, prepared_items.items_id AS item_id, prepared_items.id AS id, locations.location AS location')
            ->where('request_number', $request->request_number)
            ->join('items','items.id','prepared_items.items_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','prepared_items.location')
            ->get()
            ->sortBy('item')
            ->sortBy('category');
            
        return view('/pages/stockRequest/printStockRequest', compact('list','list2','list3'));
    }
}
