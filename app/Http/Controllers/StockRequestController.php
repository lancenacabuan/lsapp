<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailForRequest;
use App\Mail\disapprovedRequest;
use App\Mail\receivedRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\RequestType;
use App\Models\Status;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\RequestTransfer;
use App\Models\Prepare;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

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
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/assembly');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');
        $items = Item::select('id','item')->get()->sortBy('item');
        $req_types = RequestType::select('id','name')->whereIn('id',['2','3'])->get();            
        
        return view('/pages/stockrequest', compact('categories','items','req_types'));
    }

    public function generatedr(Request $request){
        $reqnumR = Requests::query()->select()->where('request_number',$request->request_number)->count();
        $reqnumT = RequestTransfer::query()->select()->where('request_number',$request->request_number)->count();
        $reqnum = $reqnumR + $reqnumT;
        if($reqnum == 0){
            return response('unique');
        }
        return response('duplicate');
    }

    public function itemsreq(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        
        return response()->json($list);
    }

    public function setuom(Request $request){       
        $uom = Item::query()->select('UOM as uom')
            ->where('id',$request->item_id)
            ->get();
        $uom = str_replace('[{"uom":"','',$uom);
        $uom = str_replace('"}]','',$uom);
        
        return response($uom);
    }

    public function saveReqNum(Request $request){
        if(trim($request->reference) != ''){
            $reference = Requests::query()->select()
                ->whereRaw('LOWER(reference) = ?',strtolower($request->reference))
                ->count();
        }
        else{
            $reference = 0;
        }
        if($reference != 0){
            $result = 'duplicate';
        }
        else {
            $requests = new Requests;
            $requests->request_number = $request->request_number;
            $requests->requested_by = auth()->user()->id;
            $requests->needdate = $request->needdate;
            $requests->request_type = $request->request_type;
            $requests->status = '6';
            $requests->client_name = ucwords($request->client_name);
            $requests->location = ucwords($request->location);
            $requests->reference = strtoupper($request->reference);
            $sql = $requests->save();

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
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
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, request_type.name AS reqtype, client_name, location, reference, needdate')
                ->where('requests.request_number', $request->request_number)
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->get();

                $request_details = str_replace('[','',$request_details);
                $request_details = str_replace(']','',$request_details);
                $request_details = json_decode($request_details);
        }
        while(!$request_details);

        do{
            $items = StockRequest::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity')
                ->join('categories', 'categories.id', 'stock_request.category')
                ->join('items', 'items.id', 'stock_request.item')
                ->where('request_number', $request->request_number)
                ->get();
        }
        while(!$items);
        
        $subject = 'STOCK REQUEST NO. '.$request->request_number;
        $user = User::role('approver - sales')->get();
        foreach($user as $key){
            $details = [
                'name' => ucwords($key->name),
                'action' => 'STOCK REQUEST',
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'requested_by' => auth()->user()->name,
                'needdate' => $request_details->needdate,
                'reqtype' => $request_details->reqtype,
                'client_name' => $request_details->client_name,
                'location' => $request_details->location,
                'reference' => $request_details->reference,
                'role' => 'Approver - Sales',
                'items' => $items
            ];
            Mail::to($key->email)->send(new emailForRequest($details, $subject));
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW STOCK REQUEST: User successfully saved Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function request_data(){
        if(auth()->user()->hasanyRole('approver - sales')){ //---ROLES---//
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason, needdate, requests.item_id AS item_id, items.item AS item_desc, qty')
            ->whereIn('requests.status', ['6'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->join('items', 'items.id', '=', 'requests.item_id')
            ->orderBy('requests.created_at', 'DESC')
            ->get();
        }
        else if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')){ //---ROLES---//
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason, needdate, requests.item_id AS item_id, items.item AS item_desc, qty')
            ->whereNotIn('requests.status', ['7','8','10','11'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->join('items', 'items.id', '=', 'requests.item_id')
            ->orderBy('requests.needdate', 'ASC')
            ->orderBy('requests.created_at', 'ASC')
            ->get();
        }
        else{
            $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason, needdate, requests.item_id AS item_id, items.item AS item_desc, qty')
            ->where('requests.requested_by', auth()->user()->id)
            ->whereNotIn('requests.status', ['7','8','10','11'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->join('items', 'items.id', '=', 'requests.item_id')
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

    public function reqModal(Request $request){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, reason, needdate, requests.item_id AS item_id, items.item AS item_desc, qty')
            ->where('requests.request_number', $request->request_number)
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
        ->toJson();
    }

    public function requestDetails(Request $request){
        $stockreq = StockRequest::query()->select('categories.category','items.item','items.UOM AS uom','items.id AS item_id','quantity','served','pending')
            ->join('categories', 'categories.id', 'stock_request.category')
            ->join('items', 'items.id', 'stock_request.item')
            ->where('request_number',$request->reqnum)
            ->groupBy('category','items.item','uom','quantity','served','pending','item_id')
            ->get();        
        
        return DataTables::of($stockreq)
        ->addColumn('qtystock', function (StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
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

    public function schedItems(Request $request){
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

    public function deleteRequest(Request $request){
        do{
            $sqlquery = Requests::where('request_number', $request->request_number)->delete();
        }
        while(!$sqlquery);
        
        $sql = StockRequest::where('request_number', $request->request_number)->delete();
        
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
        
        return response($result);
    }

    public function logDisapprove(Request $request){
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, client_name, location, reference, reason, needdate')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->get();

                $request_details = str_replace('[','',$request_details);
                $request_details = str_replace(']','',$request_details);
                $request_details = json_decode($request_details);
        }
        while(!$request_details);

        do{
            $items = StockRequest::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity')
                ->join('categories', 'categories.id', 'stock_request.category')
                ->join('items', 'items.id', 'stock_request.item')
                ->where('request_number', $request->request_number)
                ->get();
        }
        while(!$items);
        
        $subject = 'STOCK REQUEST NO. '.$request->request_number;
        $details = [
            'name' => $request_details->reqby,
            'action' => 'STOCK REQUEST',
            'request_number' => $request->request_number,
            'reqdate' => $request_details->reqdate,
            'requested_by' => $request_details->reqby,
            'needdate' => $request_details->needdate,
            'reqtype' => $request_details->reqtype,
            'client_name' => $request_details->client_name,
            'location' => $request_details->location,
            'reference' => $request_details->reference,
            'reason' => $request_details->reason,
            'disapprovedby' => auth()->user()->name,
            'role' => 'Sales',
            'items' => $items
        ];
        Mail::to($request_details->email)->send(new disapprovedRequest($details, $subject));

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "DISAPPROVED STOCK REQUEST: User successfully disapproved Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function inTransit(Request $request){
        Requests::where('request_number', $request->request_number)
            ->where('status','2')
            ->update(['status' => '3']);

        Requests::where('request_number', $request->request_number)
            ->where('status','5')
            ->update(['status' => '4']);

        Prepare::where('request_number', $request->request_number)
            ->update(['status' => 'FOR RECEIVING']);
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "FOR RECEIVING STOCK REQUEST: User successfully processed for receiving Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function receiveRequest(Request $request){
        if($request->request_type == '3'){
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '9']);

            Prepare::where('request_number', $request->request_number)
                ->update(['status' => 'RECEIVED DEMO']);
            
            $list = Prepare::select('stock_id','items_id','request_number','location','serial','qty')
                ->where('request_number', $request->request_number)
                ->get();
            foreach($list as $key){
                if($key->serial == ''){
                    for($x = 0; $x < $key->qty; $x++){
                        Stock::where('item_id',$key->items_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'in', 'location_id' => '9']);
                    }
                }
                else{
                    if(str_contains($key->serial, 'N/A')){
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial','N/A')
                        ->where('location_id',$key->location)
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'in', 'location_id' => '9']);
                    }
                    else{
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial',$key->serial)
                        ->where('location_id',$key->location)
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'in', 'location_id' => '9']);
                    }
                }
            }
        }
        else{
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '8']);

            Prepare::where('request_number', $request->request_number)
                ->update(['status' => 'RECEIVED']);
            
            $list = Prepare::select('stock_id','items_id','request_number','location','serial','qty')
                ->where('request_number', $request->request_number)
                ->get();
            foreach($list as $key){
                if($key->serial == ''){
                    for($x = 0; $x < $key->qty; $x++){
                        Stock::where('item_id',$key->items_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out']);
                    }
                }
                else{
                    if(str_contains($key->serial, 'N/A')){
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial','N/A')
                        ->where('location_id',$key->location)
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out']);
                    }
                    else{
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial',$key->serial)
                        ->where('location_id',$key->location)
                        ->where('status', 'prep')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out']);
                    }
                }
            }
        }

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function logReceive(Request $request){
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, client_name, location, reference, schedule, needdate')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->get();

                $request_details = str_replace('[','',$request_details);
                $request_details = str_replace(']','',$request_details);
                $request_details = json_decode($request_details);
        }
        while(!$request_details);

        do{
            $prep = Prepare::selectRaw('users.name AS prep_by, prepared_items.updated_at AS prep_date')
                ->where('request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'prepared_items.user_id')
                ->orderBy('prepared_items.id','DESC')
                ->first();
        }
        while(!$prep);
        
        do{
            $items = Prepare::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, prepared_items.serial AS serial, prepared_items.qty AS qty, locations.location AS location')
                ->where('request_number', $request->request_number)
                ->join('items','items.id','prepared_items.items_id')
                ->join('categories','categories.id','items.category_id')
                ->join('locations','locations.id','prepared_items.location')
                ->get()
                ->sortBy('item')
                ->sortBy('category');
        }
        while(!$items);
        
        $subject = 'STOCK REQUEST NO. '.$request->request_number;
        $user = User::role('admin')->get();
        foreach($user as $key){
            $details = [
                'name' => ucwords($key->name),
                'action' => 'STOCK REQUEST',
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'requested_by' => $request_details->reqby,
                'needdate' => $request_details->needdate,
                'reqtype' => $request_details->reqtype,
                'client_name' => $request_details->client_name,
                'location' => $request_details->location,
                'reference' => $request_details->reference,
                'prepared_by' => $prep->prep_by,
                'prepdate' => $prep->prep_date,
                'scheddate' => $request_details->schedule,
                'receivedby' => auth()->user()->name,
                'role' => 'Admin',
                'items' => $items
            ];
            Mail::to($key->email)->send(new receivedRequest($details, $subject));
        }

        $details = [
            'name' => $request_details->reqby,
            'action' => 'STOCK REQUEST',
            'request_number' => $request->request_number,
            'reqdate' => $request_details->reqdate,
            'requested_by' => $request_details->reqby,
            'needdate' => $request_details->needdate,
            'reqtype' => $request_details->reqtype,
            'client_name' => $request_details->client_name,
            'location' => $request_details->location,
            'reference' => $request_details->reference,
            'prepared_by' => $prep->prep_by,
            'prepdate' => $prep->prep_date,
            'scheddate' => $request_details->schedule,
            'receivedby' => auth()->user()->name,
            'role' => 'Sales',
            'items' => $items
        ];
        Mail::to(auth()->user()->email)->send(new receivedRequest($details, $subject));

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "RECEIVED STOCK REQUEST: User successfully received Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function saleRequest(Request $request){
        if(trim($request->reference) != ''){
            $reference = Requests::query()->select()
                ->whereRaw('LOWER(reference) = ?',strtolower($request->reference))
                ->count();
        }
        else{
            $reference = 0;
        }
        if($reference != 0){
            $result = 'duplicate';
        }
        else {
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '10', 'reference' => strtoupper($request->reference)]);

            Prepare::where('request_number', $request->request_number)
                ->update(['status' => 'SOLD']);
            
            $list = Prepare::select('stock_id','items_id','request_number','location','serial','qty')
                ->where('request_number', $request->request_number)
                ->get();
            foreach($list as $key){
                if($key->serial == ''){
                    for($x = 0; $x < $key->qty; $x++){
                        Stock::where('item_id',$key->items_id)
                        ->whereIn('location_id',['9'])
                        ->where('status', 'in')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out', 'location_id' => '1']);
                    }
                }
                else{
                    if(str_contains($key->serial, 'N/A')){
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial','N/A')
                        ->where('location_id',['9'])
                        ->where('status', 'in')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out', 'location_id' => $key->location]);
                    }
                    else{
                        Stock::where('item_id',$key->items_id)
                        ->where('stock_id',$key->stock_id)
                        ->where('serial',$key->serial)
                        ->where('location_id',['9'])
                        ->where('status', 'in')
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'out', 'location_id' => $key->location]);
                    }
                }
            }

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
                
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "SOLD STOCK REQUEST: User successfully sold Stock Request No. $request->request_number.";
                $userlogs->save();
            }
        }

        return response($result);
    }

    public function returnRequest(Request $request){
        $sql = Requests::where('request_number', $request->request_number)
            ->update(['status' => '11']);

        Prepare::where('request_number', $request->request_number)
            ->update(['status' => 'RETURNED']);
        
        $list = Prepare::select('stock_id','items_id','request_number','location','serial','qty')
            ->where('request_number', $request->request_number)
            ->get();
        foreach($list as $key){
            if($key->serial == ''){
                for($x = 0; $x < $key->qty; $x++){
                    Stock::where('item_id',$key->items_id)
                    ->whereIn('location_id',['9'])
                    ->where('status', 'in')
                    ->orderBy('id')->limit(1)
                    ->update(['location_id' => '1']);
                }
            }
            else{
                if(str_contains($key->serial, 'N/A')){
                    Stock::where('item_id',$key->items_id)
                    ->where('stock_id',$key->stock_id)
                    ->where('serial','N/A')
                    ->where('location_id',['9'])
                    ->where('status', 'in')
                    ->orderBy('id')->limit(1)
                    ->update(['location_id' => $key->location]);
                }
                else{
                    Stock::where('item_id',$key->items_id)
                    ->where('stock_id',$key->stock_id)
                    ->where('serial',$key->serial)
                    ->where('location_id',['9'])
                    ->where('status', 'in')
                    ->orderBy('id')->limit(1)
                    ->update(['location_id' => $key->location]);
                }
            }
        }
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "RETURNED STOCK REQUEST: User successfully returned Stock Request No. $request->request_number.";
        $userlogs->save();

        return response($result);
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
        $list = Stock::select('stocks.id AS id','serial','location_id','location')
            ->where('stocks.item_id', $request->item_id)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',['1','2','3','4'])
            ->join('locations','locations.id','stocks.location_id')
            ->get();
        
        return response()->json($list);
    }

    public function setlocation(Request $request){       
        $list = Stock::query()->select('stocks.location_id AS location_id','locations.location AS location')
            ->join('locations','locations.id','stocks.location_id')
            ->where('stocks.id',$request->serial_id)
            ->get();

        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        return ($list);
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
            $prepare->stock_id = $request->stock_id;
            $prepare->user_id = auth()->user()->id;
            $prepare->items_id = $request->item_id;
            $prepare->location = $request->location;
            $prepare->serial = $request->serial;
            $prepare->qty = $request->qty;
            $prepare->schedule = $request->schedOn;
            $prepare->status = 'SCHEDULED';
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
            if($request->req_type_id != '5'){
                if($request->serial == ''){
                    Stock::where('item_id',$request->item_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status','in')
                        ->orderBy('id')->limit($request->qty)
                        ->update(['status' => 'prep']);
                }
                else{
                    Stock::where('item_id',$request->item_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status','in')
                        ->where('id',$request->stock_id)
                        ->orderBy('id')->limit(1)
                        ->update(['status' => 'prep']);
                }
            }
            else{
                if($request->serial == ''){
                    Stock::where('item_id',$request->item_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status','in')
                        ->orderBy('id')->limit($request->qty)
                        ->update(['location_id' => '7']);
                }
                else{
                    Stock::where('item_id',$request->item_id)
                        ->whereIn('location_id',['1','2','3','4'])
                        ->where('status','in')
                        ->where('id',$request->stock_id)
                        ->orderBy('id')->limit(1)
                        ->update(['location_id' => '7']);
                }
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
        
        return response('true');
    }

    public function printRequest(Request $request){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS req_date, requests.request_number AS req_num, requests.requested_by AS user_id, users.name AS req_by, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, reference, needdate, requests.item_id AS item_id, items.item AS item_desc, qty')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->join('items', 'items.id', '=', 'requests.item_id')
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
        
        if(!$list || !$list2 || !$list3){
            return redirect()->to('/stockrequest');
        }

        return view('/pages/stockRequest/printStockRequest', compact('list','list2','list3'));
    }
}