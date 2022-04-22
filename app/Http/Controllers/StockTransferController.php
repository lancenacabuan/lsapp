<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailForTransfer;
use App\Mail\disapprovedTransfer;
use App\Mail\receivedTransfer;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Requests;
use App\Models\RequestTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

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
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/assembly');
        }
        $locations = Location::select('id','location')->whereNotIn('id',['7','8'])->get();

        return view('/pages/stocktransfer', compact('locations'));
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

    public function settransuom(Request $request){       
        $uom = Item::query()->select('UOM as uom')
            ->where('id',$request->item_id)
            ->get();
        $uom = str_replace('[{"uom":"','',$uom);
        $uom = str_replace('"}]','',$uom);
        
        return response($uom);
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
        do{
            $request_details = RequestTransfer::selectRaw('request_transfer.created_at AS reqdate, needdate, locfrom, locto')
                ->where('request_transfer.request_number', $request->request_number)
                ->get();

                $request_details = str_replace('[','',$request_details);
                $request_details = str_replace(']','',$request_details);
                $request_details = json_decode($request_details);
        }
        while(!$request_details);
        
        do{
            $items = StockTransfer::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity')
                ->join('categories', 'categories.id', 'stock_transfer.category')
                ->join('items', 'items.id', 'stock_transfer.item')
                ->where('request_number', $request->request_number)
                ->get();
        }
        while(!$items);
        
        do{
            $locfrom = Location::query()->select('location')->where('id',$request_details->locfrom)->get();
        }
        while(!$locfrom);
        $locfrom = str_replace('[{"location":"','', $locfrom);
        $locfrom = str_replace('"}]','', $locfrom);

        do{
            $locto = Location::query()->select('location')->where('id',$request_details->locto)->get();
        }
        while(!$locto);
        $locto = str_replace('[{"location":"','', $locto);
        $locto = str_replace('"}]','', $locto);

        $subject = 'STOCK TRANSFER REQUEST NO. '.$request->request_number;
        $user = User::role('approver - warehouse')->get();
        foreach($user as $key){
            $details = [
                'name' => ucwords($key->name),
                'action' => 'STOCK TRANSFER REQUEST',
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'requested_by' => auth()->user()->name,
                'needdate' => $request_details->needdate,
                'locfrom' => $locfrom,
                'locto' => $locto,
                'role' => 'Approver - Warehouse',
                'items' => $items
            ];
            Mail::to($key->email)->send(new emailForTransfer($details, $subject));
        }
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW STOCK TRANSFER REQUEST: User successfully submitted Stock Transfer Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function transfer_data(){
        if(auth()->user()->hasanyRole('approver - warehouse')){ //---ROLES---//
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, prepared_by, reason, needdate, locfrom, locto')
            ->whereIn('request_transfer.status', ['6'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        }
        else if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')){ //---ROLES---//
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, prepared_by, reason, needdate, locfrom, locto')
            ->whereNotIn('request_transfer.status', ['7','8'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->orderBy('request_transfer.needdate', 'ASC')
            ->orderBy('request_transfer.created_at', 'ASC')
            ->get();
        }
        else{
            $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, prepared_by, reason, needdate, locfrom, locto')
            ->where('request_transfer.requested_by', auth()->user()->id)
            ->whereNotIn('request_transfer.status', ['7','8'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
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
        ->addColumn('location_from', function (RequestTransfer $list){
            $locfrom = Location::query()
                ->select('location')
                ->where('id', $list->locfrom)
                ->get();
            $locfrom = str_replace("[{\"location\":\"","",$locfrom);
            $locfrom = str_replace("\"}]","",$locfrom);
            
            return $locfrom;
        })
        ->addColumn('location_to', function (RequestTransfer $list){
            $locto = Location::query()
                ->select('location')
                ->where('id', $list->locto)
                ->get();
            $locto = str_replace("[{\"location\":\"","",$locto);
            $locto = str_replace("\"}]","",$locto);
            
            return $locto;
        })
        ->make(true);
    }

    public function transModal(Request $request){
        $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, locations.location AS location, prepared_by, reason, needdate, locfrom, locto')
            ->where('request_transfer.request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->join('locations', 'locations.id', '=', 'request_transfer.locto')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();

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
        ->toJson();
    }

    public function transferDetails(Request $request){
        $stockreq = StockTransfer::query()->select('categories.category','items.item','items.UOM AS uom','items.id AS item_id','quantity','served','pending')
            ->join('categories', 'categories.id', 'stock_transfer.category')
            ->join('items', 'items.id', 'stock_transfer.item')
            ->where('request_number',$request->reqnum)
            ->groupBy('category','items.item','uom','quantity','served','pending','item_id')
            ->get();        
        
        return DataTables::of($stockreq)
        ->addColumn('qtystock', function (StockTransfer $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
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

    public function transItems(Request $request){
        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id, locations.location AS location')
            ->where('stocks.request_number', $request->request_number)
            ->whereIn('stocks.status', ['in','trans'])
            ->join('request_transfer','request_transfer.request_number','stocks.request_number')
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','request_transfer.locfrom')
            ->get()
            ->sortBy('item')
            ->sortBy('category');

        return DataTables::of($list)->make(true);
    }

    public function incTransItems(Request $request){
        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id, locations.location AS location')
            ->where('stocks.request_number', $request->request_number)
            ->where('stocks.status', 'incomplete')
            ->join('request_transfer','request_transfer.request_number','stocks.request_number')
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','request_transfer.locfrom')
            ->get()
            ->sortBy('item')
            ->sortBy('category');

        return DataTables::of($list)->make(true);
    }

    public function editTransSerial(Request $request){
        do{
            $sql = Stock::where('id', $request->id)
                ->update(['serial' => $request->newserial, 'user_id' => auth()->user()->id]);
        }
        while(!$sql);
        
        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "EDITED ITEM SERIAL: User successfully edited Serial from '$request->origserial' to '$request->newserial' of Item '$request->item' with Category '$request->category'.";
            $userlogs->save();
        }

        return response($result);
    }

    public function delTransItem(Request $request){
        do{
            $reqitems = StockTransfer::where('request_number', $request->req_num)
                ->where('item', $request->item_id)
                ->delete();
        }
        while(!$reqitems);
        
        if(!$reqitems){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "REMOVED STOCK TRANSFER REQUEST ITEM: User successfully removed from transfer request list $request->quantity-$request->uom/s of '$request->item' from Stock Transfer Request No. $request->req_num.";
            $userlogs->save();
        }

        $count = StockTransfer::where('request_number', $request->req_num)->count();
        if($count == 0){
            do{
                $sql = RequestTransfer::where('request_number', $request->req_num)->delete();
            }
            while(!$sql);

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DELETED STOCK TRANSFER REQUEST: User successfully deleted Stock Transfer Request No. $request->req_num.";            
            $userlogs->save();
        }

        $data = array('result' => $result, 'count' => $count);
        return response()->json($data);
    }

    public function deleteTransfer(Request $request){
        do{
            $sqlquery = RequestTransfer::where('request_number', $request->request_number)->delete();
        }
        while(!$sqlquery);
        
        $sql = StockTransfer::where('request_number', $request->request_number)->delete();
        
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
        
        return response($result);
    }

    public function logTransDisapprove(Request $request){
        do{
            $request_details = RequestTransfer::selectRaw('request_transfer.created_at AS reqdate, users.name AS reqby, users.email AS email, needdate, locfrom, locto, reason')
                ->where('request_transfer.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'request_transfer.requested_by')
                ->get();

                $request_details = str_replace('[','',$request_details);
                $request_details = str_replace(']','',$request_details);
                $request_details = json_decode($request_details);
        }
        while(!$request_details);
        
        do{
            $items = StockTransfer::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity')
                ->join('categories', 'categories.id', 'stock_transfer.category')
                ->join('items', 'items.id', 'stock_transfer.item')
                ->where('request_number', $request->request_number)
                ->get();
        }
        while(!$items);
        
        do{
            $locfrom = Location::query()->select('location')->where('id',$request_details->locfrom)->get();
        }
        while(!$locfrom);
        $locfrom = str_replace('[{"location":"','', $locfrom);
        $locfrom = str_replace('"}]','', $locfrom);

        do{
            $locto = Location::query()->select('location')->where('id',$request_details->locto)->get();
        }
        while(!$locto);
        $locto = str_replace('[{"location":"','', $locto);
        $locto = str_replace('"}]','', $locto);
        
        $subject = 'STOCK TRANSFER REQUEST NO. '.$request->request_number;
        $details = [
            'name' => $request_details->reqby,
            'action' => 'STOCK TRANSFER REQUEST',
            'request_number' => $request->request_number,
            'reqdate' => $request_details->reqdate,
            'requested_by' => $request_details->reqby,
            'needdate' => $request_details->needdate,
            'locfrom' => $locfrom,
            'locto' => $locto,
            'reason' => $request_details->reason,
            'disapprovedby' => auth()->user()->name,
            'role' => 'Admin / Encoder',
            'items' => $items
        ];
        Mail::to($request_details->email)->send(new disapprovedTransfer($details, $subject));

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "DISAPPROVED STOCK TRANSFER REQUEST: User successfully disapproved Stock Transfer Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function forReceiving(Request $request){
        RequestTransfer::where('request_number', $request->request_number)
            ->where('status','2')
            ->update(['status' => '3']);

        RequestTransfer::where('request_number', $request->request_number)
            ->where('status','5')
            ->update(['status' => '4']);
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "FOR RECEIVING STOCK TRANSFER REQUEST: User successfully processed for receiving Stock Transfer Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function receiveTransfer(Request $request){
        if($request->inc == 'true'){
            do{
                $sql = RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '15']);
            }
            while(!$sql);
        }
        else{
            do{
                $sql = RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '8']);
            }
            while(!$sql);
        }
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        return response($result);
    }

    public function receiveTransItems(Request $request){
        if($request->status == '3' || $request->status == '4'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'received']);
            }
            while(!$sql);
        }
        if($request->status == '17'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'in']);
            }
            while(!$sql);
        }
        
        return response('true');
    }

    public function logTransReceive(Request $request){
        if($request->status == '3' || $request->status == '4'){
            Stock::where('request_number', $request->request_number)
                ->where('status', '=', 'trans')
                ->update(['status' => 'incomplete']);
            
            Stock::where('request_number', $request->request_number)
                ->where('status', '=', 'received')
                ->update(['status' => 'in']);
        }

        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE STOCK TRANSFER REQUEST: User successfully received incomplete requested transfer items of Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            do{
                $request_details = RequestTransfer::selectRaw('request_transfer.created_at AS reqdate, users.name AS reqby, users.email AS email, needdate, prepdate, locfrom, locto, schedule')
                    ->where('request_transfer.request_number', $request->request_number)
                    ->join('users', 'users.id', '=', 'request_transfer.requested_by')
                    ->get();
    
                    $request_details = str_replace('[','',$request_details);
                    $request_details = str_replace(']','',$request_details);
                    $request_details = json_decode($request_details);
            }
            while(!$request_details);
    
            do{
                $trans = RequestTransfer::selectRaw('users.name AS prepby')
                    ->where('request_transfer.request_number', $request->request_number)
                    ->join('users', 'users.id', '=', 'request_transfer.prepared_by')
                    ->get();
                
                    $trans = str_replace('[','',$trans);
                    $trans = str_replace(']','',$trans);
                    $trans = json_decode($trans);
            }
            while(!$trans);
            
            do{
                $items = Transfer::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, transferred_items.serial AS serial, transferred_items.qty AS qty')
                    ->where('request_number', $request->request_number)
                    ->join('items','items.id','transferred_items.items_id')
                    ->join('categories','categories.id','items.category_id')
                    ->get()
                    ->sortBy('item')
                    ->sortBy('category');
            }
            while(!$items);
            
            do{
                $locfrom = Location::query()->select('location')->where('id',$request_details->locfrom)->get();
            }
            while(!$locfrom);
            $locfrom = str_replace('[{"location":"','', $locfrom);
            $locfrom = str_replace('"}]','', $locfrom);
    
            do{
                $locto = Location::query()->select('location')->where('id',$request_details->locto)->get();
            }
            while(!$locto);
            $locto = str_replace('[{"location":"','', $locto);
            $locto = str_replace('"}]','', $locto);
    
            $subject = 'STOCK TRANSFER REQUEST NO. '.$request->request_number;
            $user = User::role('admin')->get();
            foreach($user as $key){
                if($key->email != $request_details->email){
                    $details = [
                        'name' => ucwords($key->name),
                        'action' => 'STOCK TRANSFER REQUEST',
                        'request_number' => $request->request_number,
                        'reqdate' => $request_details->reqdate,
                        'requested_by' => $request_details->reqby,
                        'needdate' => $request_details->needdate,
                        'locfrom' => $locfrom,
                        'locto' => $locto,
                        'prepared_by' => $trans->prepby,
                        'prepdate' => $request_details->prepdate,
                        'scheddate' => $request_details->schedule,
                        'receivedby' => auth()->user()->name,
                        'role' => 'Admin',
                        'items' => $items
                    ];
                    Mail::to($key->email)->send(new receivedTransfer($details, $subject));
                }
            }
    
            $details = [
                'name' => $request_details->reqby,
                'action' => 'STOCK TRANSFER REQUEST',
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'requested_by' => $request_details->reqby,
                'needdate' => $request_details->needdate,
                'locfrom' => $locfrom,
                'locto' => $locto,
                'prepared_by' => $trans->prepby,
                'prepdate' => $request_details->prepdate,
                'scheddate' => $request_details->schedule,
                'receivedby' => auth()->user()->name,
                'role' => 'Admin / Encoder',
                'items' => $items
            ];
            Mail::to($request_details->email)->send(new receivedTransfer($details, $subject));
    
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE STOCK TRANSFER REQUEST: User successfully received complete requested transfer items of Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
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
            ->limit(1)
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
        ->toJson();
    }

    public function settransserials(Request $request){
        $list = Stock::select('stocks.id AS id','serial','location_id','location')
            ->where('stocks.item_id', $request->item_id)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',[$request->location])
            ->join('locations','locations.id','stocks.location_id')
            ->get();
        
        return response()->json($list);
    }

    public function transferItems(Request $request){
        $transfer = new Transfer;
        $transfer->request_number = $request->request_number;
        $transfer->stock_id = $request->stock_id;
        $sql = $transfer->save();

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';
        }
        if($result == 'true'){
            do{
                $sql = Stock::where('id',$request->stock_id)
                    ->update(['request_number' => $request->request_number, 'status' => 'trans', 'location_id' => $request->locto]);
            }
            while(!$sql);
            
            StockTransfer::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->increment('served', $request->qty);

            StockTransfer::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->decrement('pending', $request->qty);
        }
        
        return response($result);
    }

    public function logTransSched(Request $request){
        $total = StockTransfer::where('request_number', $request->request_number)->sum('pending');
        if($total == 0){
            do{
                $sql = RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '2']);
            }
            while(!$sql);
            $sched = 'SCHEDULED';
        }
        else{
            do{
                $sql = RequestTransfer::where('request_number', $request->request_number)
                    ->update(['status' => '5']);
            }
            while(!$sql);
            $sched = 'PARTIAL SCHEDULED';
        }
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            RequestTransfer::where('request_number', $request->request_number)
                ->update(['prepared_by' => auth()->user()->id, 'schedule' => $request->schedOn, 'prepdate' => date('Y-m-d')]);

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = $sched." STOCK TRANSFER REQUEST: User successfully scheduled on $request->schedOn Stock Transfer Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function printTransferRequest(Request $request){
        $list = RequestTransfer::selectRaw('request_transfer.id AS req_id, request_transfer.created_at AS req_date, request_transfer.request_number AS req_num, request_transfer.requested_by AS user_id, users.name AS req_by, status.status AS status, users.name AS req_by, status.id AS status_id, request_transfer.schedule AS sched, prepared_by, needdate, prepdate, locfrom, locto')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get();
        $list = str_replace('[','',$list);
        $list = str_replace(']','',$list);
        $list = json_decode($list);

        $list2 = RequestTransfer::selectRaw('users.name AS prepby')
            ->where('request_transfer.request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'request_transfer.prepared_by')
            ->get();
        
        $list2 = str_replace('[','',$list2);
        $list2 = str_replace(']','',$list2);
        $list2 = json_decode($list2);
        
        $list3 = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.UOM AS uom, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, locations.location AS location')
            ->where('stocks.request_number', $request->request_number)
            ->join('request_transfer','request_transfer.request_number','stocks.request_number')
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','request_transfer.locfrom')
            ->get()
            ->sortBy('item')
            ->sortBy('category');
        
        if(!$list || !$list2 || !$list3){
            return redirect()->to('/stocktransfer');
        }

        return view('/pages/stockTransfer/printStockTransfer', compact('list','list2','list3'));
    }
}