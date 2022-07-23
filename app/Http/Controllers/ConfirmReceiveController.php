<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\receivedRequest;
use App\Models\Item;
use App\Models\Warranty;
use App\Models\Stock;
use App\Models\Requests;
use App\Models\StockRequest;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class ConfirmReceiveController extends Controller
{
    public function gitpull(){
        $output = shell_exec('cd /var/www/html/main-warehouse && git pull');
        return $output;
    }

    public function confirm(Request $request){
        $confirmed = false;
        if(Requests::where('request_number', $request->request_number)->where('token', $request->token)->count() == 0){
            return redirect()->to('/');
        }
        if(Requests::where('request_number', $request->request_number)->first()->verify == ''){
            if(Stock::where('request_number', $request->request_number)->where('status','incomplete')->count() > 0 || StockRequest::where('request_number', $request->request_number)->sum('pending') > 0){
                Requests::where('request_number', $request->request_number)
                    ->update(['verify' => 'Incomplete Confirmed']);
            }
            else if(Requests::where('request_number', $request->request_number)->first()->status == 9){
                Requests::where('request_number', $request->request_number)
                    ->update(['verify' => 'Demo Confirmed']);
            }
            else{
                Requests::where('request_number', $request->request_number)
                    ->update(['verify' => 'Confirmed']);
            }
        }
        else{
            $confirmed = true;
        }

        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS req_date, requests.updated_at AS confirmdate, requests.request_number AS req_num, requests.requested_by AS user_id, users.name AS req_by, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, needdate, prepdate, requests.item_id AS item_id, qty, assembly_reqnum, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->first();

        if(!$list){
            return redirect()->to('/');
        }

        $list1 = Item::selectRaw('items.item AS item_desc, items.prodcode AS item_code')
            ->where('id', '=', $list->item_id)
            ->get();

        $list2 = Requests::selectRaw('users.name AS prepby')
            ->where('requests.request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.prepared_by')
            ->first();
        
        if(!$list2){
            return redirect()->to('/');
        }
        
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list3 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
            ->whereIn('request_number', $include)
            ->whereIn('stocks.batch', ['new',''])
            ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
            ->join('items','items.id','stocks.item_id')
            ->groupBy('prodcode','item','uom','serial','qty','item_id')
            ->orderBy('item', 'ASC')
            ->get();
        if(Stock::where('request_number', $request->request_number)->where('batch','old')->count() != 0 && Stock::where('request_number', $request->request_number)->where('batch','new')->count() == 0){
            $list3 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.batch', ['old'])
                ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
            $getList3 = true;
        }
        else{
            $getList3 = false;
        }

        if(!$list3){
            return redirect()->to('/');
        }

        if(Stock::where('request_number', $request->request_number)->where('status','incomplete')->count() != 0){
            $list4 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['incomplete'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        else{
            $list4 = array();
        }

        if(Stock::where('request_number', $request->request_number)->where('batch','old')->count() != 0){
            $list5 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.batch', ['old'])
                ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        else{
            $list5 = array();
        }

        if(StockRequest::where('request_number', $request->request_number)->sum('pending') > 0){
            do{
                $list0 = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','pending')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->where('pending', '>', '0')
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$list0);
        }
        else{
            $list0 = array();
        }

        if(Stock::where('request_number', $request->request_number)->where('status','prep')->count() != 0){
            $listX = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['prep'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        else{
            $listX = array();
        }
        
        return view('/pages/stockRequest/confirmStockRequest', compact('list','listX','list0','list1','list2','list3','list4','list5','getList3','confirmed'));
    }

    public function logConfirm(Request $request){
        if(Requests::where('request_number', $request->request_number)->where('token', $request->token)->count() == 0){
            return redirect()->to('/');
        }
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, request_type.id AS req_type_id, status.id AS status_id, client_name, location, contact, remarks, reference, orderID, schedule, needdate, prepdate, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->join('status', 'status.id', '=', 'requests.status')
                ->first();
        }
        while(!$request_details);

        do{
            $prep = Requests::selectRaw('users.name AS prepby')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.prepared_by')
                ->first();
        }
        while(!$prep);

        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        if($request_details->req_type_id == 2 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
            do{
                $items = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                    ->whereIn('request_number', $include)
                    ->whereIn('stocks.batch', ['new'])
                    ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                    ->join('items','items.id','stocks.item_id')
                    ->groupBy('prodcode','item','uom','serial','qty','item_id','warranty_id')
                    ->orderBy('item', 'ASC')
                    ->get()
                    ->toArray();
                foreach($items as $key => $value){
                    if($value['warranty_id'] == '0' || $value['warranty_id'] == ''){
                        $items[$key]['Warranty_Name'] = 'NO WARRANTY';
                    }
                    else{
                        $items[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty_id'])->first()->Warranty_Name;
                    }
                }
            }
            while(!$items);
        }
        else{
            do{
                $items = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                    ->whereIn('request_number', $include)
                    ->whereIn('stocks.batch', ['new'])
                    ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                    ->join('items','items.id','stocks.item_id')
                    ->groupBy('prodcode','item','uom','serial','qty','item_id')
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$items);
        }
        if(Stock::where('request_number', $request->request_number)->where('batch','old')->count() != 0 && Stock::where('request_number', $request->request_number)->where('batch','new')->count() == 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
                do{
                    $items = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id','warranty_id')
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($items as $key => $value){
                        if($value['warranty_id'] == '0' || $value['warranty_id'] == ''){
                            $items[$key]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $items[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty_id'])->first()->Warranty_Name;
                        }
                    }
                }
                while(!$items);
            }
            else{
                do{
                    $items = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id')
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                while(!$items);
            }
        }

        if(Stock::where('request_number', $request->request_number)->where('batch','old')->count() != 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
                do{
                    $olditems = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id','warranty_id')
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($olditems as $key => $value){
                        if($value['warranty_id'] == '0' || $value['warranty_id'] == ''){
                            $olditems[$key]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $olditems[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty_id'])->first()->Warranty_Name;
                        }
                    }
                }
                while(!$olditems);
            }
            else{
                do{
                    $olditems = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','asset','demo','assembly','assembled'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id')
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                while(!$olditems);
            }
        }
        else{
            $olditems = array();
        }

        if(Stock::where('request_number', $request->request_number)->where('status','incomplete')->count() != 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
                do{
                    $incitems = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.status', ['incomplete'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id','warranty_id')
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($incitems as $key => $value){
                        if($value['warranty_id'] == '0' || $value['warranty_id'] == ''){
                            $incitems[$key]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $incitems[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty_id'])->first()->Warranty_Name;
                        }
                    }
                }
                while(!$incitems);
            }
            else{
                do{
                    $incitems = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.status', ['incomplete'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id')
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                while(!$incitems);
            }
        }
        else{
            $incitems = array();
        }

        if(StockRequest::where('request_number', $request->request_number)->sum('pending') > 0){
            do{
                $penditems = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','pending')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->where('pending', '>', '0')
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$penditems);
        }
        else{
            $penditems = array();
        }

        $attachments = [];
        $files = Requests::where('request_number', $request->request_number)->first()->reference_upload;
        if($files != NULL){
            $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
            foreach($files as $file){
                $file = str_replace('"','',$file);
                if(file_exists(public_path('uploads/'.$file))){
                    array_push($attachments, public_path('uploads/'.$file));
                }
            }
        }

        if($request_details->req_type_id == '7'){
            $subject = '[RECEIVED] STOCK REQUEST NO. '.$request->request_number;
            $details = [
                'name' => $request_details->reqby,
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'needdate' => $request_details->needdate,
                'prepdate' => $request_details->prepdate,
                'scheddate' => $request_details->schedule,
                'reqtype' => $request_details->reqtype,
                'submitted_by' => $request_details->reqby,
                'requested_by' => $request_details->asset_reqby,
                'approved_by' => $request_details->asset_apvby,
                'prepared_by' => $prep->prepby,
                'received_by' => $request_details->asset_reqby,
                'role' => 'Admin / Encoder',
                'items' => $items,
                'olditems' => $olditems,
                'incitems' => $incitems,
                'files' => $attachments,
                'token' => ''
            ];
            Mail::to($request_details->email)->send(new receivedRequest($details, $subject));
            
            $details = [
                'name' => $request_details->asset_apvby,
                'request_number' => $request->request_number,
                'reqdate' => $request_details->reqdate,
                'needdate' => $request_details->needdate,
                'prepdate' => $request_details->prepdate,
                'scheddate' => $request_details->schedule,
                'reqtype' => $request_details->reqtype,
                'submitted_by' => $request_details->reqby,
                'requested_by' => $request_details->asset_reqby,
                'approved_by' => $request_details->asset_apvby,
                'prepared_by' => $prep->prepby,
                'received_by' => $request_details->asset_reqby,
                'role' => '',
                'items' => $items,
                'olditems' => $olditems,
                'incitems' => $incitems,
                'files' => $attachments,
                'token' => ''
            ];
            Mail::to($request_details->asset_apvby_email)->send(new receivedRequest($details, $subject));
        }
        else{
            $subject = '[RECEIVED] STOCK REQUEST NO. '.$request->request_number;
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 3 || $request_details->req_type_id == 8){
                $emails = User::role('admin')->where('status','ACTIVE')->get('email')->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'STOCK REQUEST',
                    'verb' => 'RECEIVED',
                    'request_number' => $request->request_number,
                    'reqdate' => $request_details->reqdate,
                    'requested_by' => $request_details->reqby,
                    'needdate' => $request_details->needdate,
                    'reqtype' => $request_details->reqtype,
                    'client_name' => $request_details->client_name,
                    'location' => $request_details->location,
                    'contact' => $request_details->contact,
                    'remarks' => $request_details->remarks,
                    'reference' => $request_details->reference,
                    'orderID' => $request_details->orderID,
                    'prepared_by' => $prep->prepby,
                    'prepdate' => $request_details->prepdate,
                    'scheddate' => $request_details->schedule,
                    'receivedby' => $request_details->client_name,
                    'role' => 'Admin',
                    'items' => $items,
                    'olditems' => $olditems,
                    'incitems' => $incitems,
                    'penditems' => $penditems,
                    'files' => array(),
                    'req_type_id' => $request_details->req_type_id,
                    'status_id' => $request_details->status_id,
                    'token' => ''
                ];
                Mail::to($sendTo)->send(new receivedRequest($details, $subject));
                unset($sendTo);
                if($request_details->req_type_id == 2 || $request_details->req_type_id == 3 || $request_details->req_type_id == 8){
                    $emails = User::role('accounting')->where('status','ACTIVE')->get('email')->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'ACCOUNTING',
                        'action' => 'STOCK REQUEST',
                        'verb' => 'RECEIVED',
                        'request_number' => $request->request_number,
                        'reqdate' => $request_details->reqdate,
                        'requested_by' => $request_details->reqby,
                        'needdate' => $request_details->needdate,
                        'reqtype' => $request_details->reqtype,
                        'client_name' => $request_details->client_name,
                        'location' => $request_details->location,
                        'contact' => $request_details->contact,
                        'remarks' => $request_details->remarks,
                        'reference' => $request_details->reference,
                        'orderID' => $request_details->orderID,
                        'prepared_by' => $prep->prepby,
                        'prepdate' => $request_details->prepdate,
                        'scheddate' => $request_details->schedule,
                        'receivedby' => $request_details->client_name,
                        'role' => 'Accounting',
                        'items' => $items,
                        'olditems' => $olditems,
                        'incitems' => $incitems,
                        'penditems' => $penditems,
                        'files' => $attachments,
                        'req_type_id' => $request_details->req_type_id,
                        'status_id' => $request_details->status_id,
                        'token' => ''
                    ];
                    Mail::to($sendTo)->send(new receivedRequest($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $request_details->reqby,
                    'action' => 'STOCK REQUEST',
                    'verb' => 'RECEIVED',
                    'request_number' => $request->request_number,
                    'reqdate' => $request_details->reqdate,
                    'requested_by' => $request_details->reqby,
                    'needdate' => $request_details->needdate,
                    'reqtype' => $request_details->reqtype,
                    'client_name' => $request_details->client_name,
                    'location' => $request_details->location,
                    'contact' => $request_details->contact,
                    'remarks' => $request_details->remarks,
                    'reference' => $request_details->reference,
                    'orderID' => $request_details->orderID,
                    'prepared_by' => $prep->prepby,
                    'prepdate' => $request_details->prepdate,
                    'scheddate' => $request_details->schedule,
                    'receivedby' => $request_details->client_name,
                    'role' => 'Sales/Merchant',
                    'items' => $items,
                    'olditems' => $olditems,
                    'incitems' => $incitems,
                    'penditems' => $penditems,
                    'files' => $attachments,
                    'req_type_id' => $request_details->req_type_id,
                    'status_id' => $request_details->status_id,
                    'token' => ''
                ];
                Mail::to($request_details->email)->send(new receivedRequest($details, $subject));
            }
        }

        return response('true');
    }
}