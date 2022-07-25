<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\notifRequest;
use App\Mail\notifTransfer;
use App\Mail\emailForRequest;
use App\Mail\approvedRequest;
use App\Mail\disapprovedRequest;
use App\Mail\receivedRequest;
use App\Mail\editSerial;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\RequestType;
use App\Models\Warranty;
use App\Models\Status;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\StockTransfer;
use App\Models\Requests;
use App\Models\Transfer;
use App\Models\RequestTransfer;
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
            return redirect('/');
        }
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/');
        }
        if(auth()->user()->hasanyRole('merchant')) //---ROLES---//
        {
            return redirect('/');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');
        $req_types = RequestType::select('id','name')->whereIn('id',['2','3','8'])->get();
        $warranty = Warranty::select('id','Warranty_Name')->get()->sortBy('Warranty_Name');
        
        return view('/pages/stockrequest', compact('categories','req_types','warranty'));
    }

    public function checkURL(Request $request){
        if($request->check == 'beta'){
            $returnURL = env('APP_URL_BETA').'uploads/'.$request->reference;
            $checkURL = Http::get($returnURL);
        }
        else{
            $returnURL = env('APP_URL_LIVE').'uploads/'.$request->reference;
            $checkURL = Http::get($returnURL);
        }
        if($checkURL->successful()){
            $data = array('result' => 'true', 'returnURL' => $returnURL);
        }
        else{
            $data = array('result' => 'false', 'returnURL' => $returnURL);
        }
        return response()->json($data);
    }

    public function generateReqNum(Request $request){
        $reqnumR1 = Requests::query()->select()->where('request_number', $request->request_number)->count();
        $reqnumR2 = Requests::query()->select()->where('reference_upload','LIKE','%'.$request->request_number.'%')->count();
        $reqnumT = RequestTransfer::query()->select()->where('request_number', $request->request_number)->count();
        $reqnum = $reqnumR1 + $reqnumR2 + $reqnumT;
        if($reqnum == 0){
            return response('unique');
        }
        return response('duplicate');
    }

    public function getInclusive(Request $request){       
        $list = Warranty::query()->select()
            ->where('id',$request->id)
            ->get();
        
        return response()->json($list);
    }

    public function itemsreq(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item', 'ASC')
            ->get();
        
        return response()->json($list);
    }

    public function setuom(Request $request){       
        $data = Item::selectRaw('UOM as uom, prodcode')
            ->where('id',$request->item_id)
            ->get();
        
        return response($data);
    }

    public function saveReqNum(Request $request){
        if($request->request_type == '7'){
            if((!filter_var($request->asset_reqby_email, FILTER_VALIDATE_EMAIL)) && (!filter_var($request->asset_apvby_email, FILTER_VALIDATE_EMAIL))){
                return ('xemail');
            }
            if(!filter_var($request->asset_reqby_email, FILTER_VALIDATE_EMAIL)){
                return ('xemail1');
            }
            if(!filter_var($request->asset_apvby_email, FILTER_VALIDATE_EMAIL)){
                return ('xemail2');
            }
            do{
                $requests = new Requests;
                $requests->request_number = $request->request_number;
                $requests->requested_by = auth()->user()->id;
                $requests->needdate = $request->needdate;
                $requests->request_type = $request->request_type;
                $requests->status = '1';
                $requests->asset_reqby = ucwords($request->asset_reqby);
                $requests->asset_apvby = ucwords($request->asset_apvby);
                $requests->asset_reqby_email = strtolower($request->asset_reqby_email);
                $requests->asset_apvby_email = strtolower($request->asset_apvby_email);
                $sql = $requests->save();
            }
            while(!$sql);
        }
        else{
            do{
                $requests = new Requests;
                $requests->request_number = $request->request_number;
                $requests->requested_by = auth()->user()->id;
                $requests->needdate = $request->needdate;
                $requests->request_type = $request->request_type;
                $requests->status = '6';
                $requests->client_name = ucwords($request->client_name);
                $requests->location = ucwords($request->location);
                $requests->contact = ucwords($request->contact);
                $requests->remarks = ucfirst($request->remarks);
                $requests->reference = strtoupper($request->reference);
                $requests->asset_reqby_email = strtolower($request->asset_reqby_email);
                $sql = $requests->save();
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

    public function editRequest(Request $request){
        do{
            $sql = Requests::where('request_number', '=', $request->request_number)
                ->update([
                    'needdate' => $request->needdate,
                    'client_name' => ucwords($request->client_name),
                    'location' => ucwords($request->location),
                    'contact' => ucwords($request->contact),
                    'remarks' => ucfirst($request->remarks),
                    'reference' => strtoupper($request->reference)
                ]);
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
            if($request->reference_upload){
                $files = Requests::where('request_number', $request->request_number)->first()->reference_upload;
                if($files != NULL){
                    $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                    foreach($files as $file){
                        $file = str_replace('"','',$file);
                        if(file_exists(public_path('uploads/'.$file))){
                            unlink(public_path('uploads/'.$file));
                        }
                    }
                }
                $reference_upload = '[ATTACHMENT SO/PO: Upload Image/s have been changed.]';
            }
            else{
                $reference_upload = NULL;
            }

            if($request->needdate != $request->needdate_orig){
                $needdate = "[Date Needed: FROM '$request->needdate_orig' TO '$request->needdate']";
            }
            else{
                $needdate = NULL;
            }
            if($request->client_name != $request->client_name_orig){
                $client_name = "[Client Name: FROM '$request->client_name_orig' TO '$request->client_name']";
            }
            else{
                $client_name = NULL;
            }
            if($request->location != $request->location_orig){
                $location_name = "[Address / Branch: FROM '$request->location_orig' TO '$request->location']";
            }
            else{
                $location_name = NULL;
            }
            if($request->contact != $request->contact_orig){
                $contact = "[Contact Person: FROM '$request->contact_orig' TO '$request->contact']";
            }
            else{
                $contact = NULL;
            }
            if($request->remarks != $request->remarks_orig){
                $remarks = "[Remarks: FROM '$request->remarks_orig' TO '$request->remarks']";
            }
            else{
                $remarks = NULL;
            }
            if($request->reference != $request->reference_orig){
                $reference = "[Reference SO/PO No.: FROM '$request->reference_orig' TO '$request->reference']";
            }
            else{
                $reference = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "EDITED STOCK REQUEST: User successfully edited details of Stock Request No. $request->request_number with the following CHANGES: $needdate $client_name $location_name $contact $remarks $reference $reference_upload.";
            $userlogs->save();
        }

        return response($result);
    }

    public function reissueRequest(Request $request){
        do{
            $list = Stock::selectRaw('item_id, warranty_id, SUM(stocks.qty) AS quantity')
                ->whereIn('stocks.id', $request->items)
                ->groupby('item_id','warranty_id')
                ->get();
        }
        while(!$list);
        
        foreach($list as $key){
            do{
                $stockRequest = new StockRequest;
                $stockRequest->request_number = $request->request_number;
                $stockRequest->item = $key->item_id;
                $stockRequest->warranty = $key->warranty_id;
                $stockRequest->quantity = $key->quantity;
                $stockRequest->served = $key->quantity;
                $stockRequest->pending = '0';
                $dump = $stockRequest->save();
            }
            while(!$dump);
            StockRequest::where('request_number', $request->request_number_prev)
                ->where('item', $key->item_id)
                ->decrement('served', $key->quantity);
            StockRequest::where('request_number', $request->request_number_prev)
                ->where('item', $key->item_id)
                ->increment('pending', $key->quantity);
        }

        if(!$dump){
            return response('false');
        }
        else{
            Stock::whereIn('stocks.id', $request->items)->update(['request_number' => $request->request_number]);
            Requests::where('request_number', $request->request_number_prev)
                ->update(['status' => '31']);
            Requests::where('request_number', $request->request_number)
                ->update(['orderID' => $request->request_number_prev, 'prepared_by' => auth()->user()->id, 'schedule' => $request->reissueSched, 'prepdate' => date('Y-m-d')]);
        }

        return response('true');
    }

    public function uploadFile(Request $request){
        $x = 1;
        $reference_upload = array();
        foreach($request->reference_upload as $upload){
            $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
            $extension = $upload->getClientOriginalExtension();
            $filename = $datetime.'_'.$request->reqnum.'-'.$x.'.'.$extension;
            array_push($reference_upload, $filename);
            $x++;
        }

        Requests::where('request_number', $request->reqnum)
            ->update(['reference_upload' => $reference_upload]);

        for($i=0; $i < count($reference_upload); $i++){
            $request->reference_upload[$i]->move(public_path('/uploads'), $reference_upload[$i]);
        }

        $reference_delete = array();
        for($c=0; $c < count($reference_upload); $c++){
            if(str_contains($reference_upload[$c], '.pdf') == true){
                $pdf = new Pdf(public_path('uploads/'.$reference_upload[$c]));
                $pdfcount = $pdf->getNumberOfPages();
                $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                for($a=1; $a < $pdfcount+1; $a++){
                    $filename = $datetime.'_'.$request->reqnum.'-'.$a.'.jpg';
                    $pdf->setPage($a)
                        ->setOutputFormat('jpg')
                        ->saveImage(public_path('uploads/'.$filename));
                    array_push($reference_upload, $filename);
                }
                unlink(public_path('uploads/'.$reference_upload[$c]));
                array_push($reference_delete, $reference_upload[$c]);
            }
        }
        $reference_upload = json_encode($reference_upload);
        for($d=0; $d < count($reference_delete); $d++){
            $reference_upload = str_replace('"'.$reference_delete[$d].'",', "", $reference_upload);
            $reference_upload = str_replace('"'.$reference_delete[$d].'"', "", $reference_upload);
            $reference_upload = str_replace($reference_delete[$d], "", $reference_upload);
        }

        Requests::where('request_number', $request->reqnum)
            ->update(['reference_upload' => $reference_upload]);

        if($request->action == 'SUBMIT'){
            return redirect()->to('/stockrequest?submit='.$request->reqnum);
        }
        else if($request->action == 'ASSET'){
            return redirect()->to('/stockrequest?asset='.$request->reqnum);
        }
        else if($request->action == 'EDIT'){
            if(Requests::where('request_number', $request->reqnum)->first()->status == 7){
                return redirect()->to('/stockrequest?status=7&edit='.$request->reqnum);
            }
            else{
                return redirect()->to('/stockrequest?edit=success');
            }
        }
        else{
            return redirect()->to('/stockrequest?sale='.$request->reqnum);
        }
    }
    
    public function saveRequest(Request $request){
        do{
            $stockRequest = new StockRequest;
            $stockRequest->request_number = $request->request_number;
            $stockRequest->item = $request->item;
            $stockRequest->quantity = $request->quantity;
            $stockRequest->served = '0';
            $stockRequest->pending = $request->quantity;
            $stockRequest->warranty = $request->warranty;
            $sql = $stockRequest->save();
        }
        while(!$sql);

        return response('true');
    }

    public function logSave(Request $request){
        if(Requests::where('request_number', $request->request_number)->count() == 0){
            return response('false');
        }

        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, request_type.name AS reqtype, request_type.id AS req_type_id, client_name, location, contact, remarks, reference, needdate, asset_reqby_email')
                ->where('requests.request_number', $request->request_number)
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->first();
        }
        while(!$request_details);

        if($request_details->reqtype == 'SALES' || $request_details->reqtype == 'FOR STAGING'){
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get()
                    ->toArray();
                foreach($items as $key => $value){
                    if($value['warranty'] == '0' || $value['warranty'] == ''){
                        $items[$key]['Warranty_Name'] = 'NO WARRANTY';
                    }
                    else{
                        $items[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty'])->first()->Warranty_Name;
                    }
                }
            }
            while(!$items);
        }
        else{
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$items);
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

        if($request->reqstatus != 7){
            $action = 'A new STOCK REQUEST';
        }
        else{
            $action = 'A revised STOCK REQUEST';
            Requests::where('request_number', $request->request_number)
                ->update(['status' => 6]);
        }

        $subject = '[FOR APPROVAL] STOCK REQUEST NO. '.$request->request_number;
        $emails = User::role('approver - sales')
            ->where('status','ACTIVE')
            ->where('company',auth()->user()->company)
            ->get('email')
            ->toArray();
        foreach($emails as $email){
            $sendTo[] = $email['email'];
        }
        $details = [
            'name' => 'APPROVER - SALES',
            'action' => $action,
            'request_number' => $request->request_number,
            'reqdate' => $request_details->reqdate,
            'requested_by' => auth()->user()->name,
            'needdate' => $request_details->needdate,
            'reqtype' => $request_details->reqtype,
            'client_name' => $request_details->client_name,
            'location' => $request_details->location,
            'contact' => $request_details->contact,
            'remarks' => $request_details->remarks,
            'reference' => $request_details->reference,
            'role' => 'Approver - Sales',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($sendTo)->send(new emailForRequest($details, $subject));

        switch($request_details->req_type_id){
            case 1: $reqtype = 'Service Unit'; break;
            case 2: $reqtype = 'Sales'; break;
            case 3: $reqtype = 'Demo Unit'; break;
            case 4: $reqtype = 'Replacement'; break;
            case 5: $reqtype = 'Assembly'; break;
            case 6: $reqtype = 'Merchant'; break;
            case 7: $reqtype = 'Fixed Asset'; break;
            case 8: $reqtype = 'For Staging'; break;
            default: $reqtype = NULL;
        }

        if($request->reqstatus != 7){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "NEW $request_details->reqtype STOCK REQUEST: User successfully submitted $reqtype Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response('true');
    }

    public function asset_logSave(Request $request){
        if(Requests::where('request_number', $request->request_number)->count() == 0){
            return response('false');
        }

        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, request_type.name AS reqtype, needdate, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email')
                ->where('requests.request_number', $request->request_number)
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->first();
        }
        while(!$request_details);

        do{
            $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                ->join('items', 'items.id', 'stock_request.item')
                ->where('request_number', $request->request_number)
                ->orderBy('item', 'ASC')
                ->get();
        }
        while(!$items);

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

        $details = [
            'name' => $request_details->asset_reqby,
            'request_number' => $request->request_number,
            'reqtype' => $request_details->reqtype,
            'reqdate' => $request_details->reqdate,
            'needdate' => $request_details->needdate,
            'submitted_by' => auth()->user()->name,
            'requested_by' => $request_details->asset_reqby,
            'approved_by' => $request_details->asset_apvby,
            'role' => '',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($request_details->asset_reqby_email)->send(new emailForRequest($details, $subject));
        $details = [
            'name' => $request_details->asset_apvby,
            'request_number' => $request->request_number,
            'reqtype' => $request_details->reqtype,
            'reqdate' => $request_details->reqdate,
            'needdate' => $request_details->needdate,
            'submitted_by' => auth()->user()->name,
            'requested_by' => $request_details->asset_reqby,
            'approved_by' => $request_details->asset_apvby,
            'role' => '',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($request_details->asset_apvby_email)->send(new emailForRequest($details, $subject));
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW FIXED ASSET STOCK REQUEST: User successfully submitted Fixed Asset Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }

    public function request_data(){
        if(auth()->user()->hasanyRole('approver - sales')){ //---ROLES---//
            $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%b. %d, %Y") AS reqdatetime, DATE_FORMAT(requests.needdate, "%b. %d, %Y") AS needdatetime, DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, reason, needdate, requests.item_id AS item_id, qty, assembly_reqnum, reference_upload, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email, notify, verify')
                ->selectRaw('(CASE
                    WHEN requests.verify LIKE "%Confirmed%"
                        THEN CONCAT("CONFIRMED ", status.status)
                    WHEN ((requests.request_type = "2" OR requests.request_type = "7" OR requests.request_type = "8")
                        AND requests.status = "8" AND requests.verify != "Confirmed")
                        OR (requests.request_type = "3" AND requests.status = "9" AND requests.verify != "Demo Confirmed")
                        THEN "FOR CONFIRMATION"
                    ELSE
                        status.status
                    END
                ) AS status')
                ->where('users.company', auth()->user()->company)
                ->whereIn('request_type.id', ['2','3','8'])
                ->whereNotIn('requests.verify', ['Confirmed'])
                ->whereNotIn('requests.status', ['7','26'])
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->join('status', 'status.id', '=', 'requests.status')
                ->orderBy('reqdate', 'ASC')
                ->orderBy('requests.needdate', 'ASC')
                ->orderBy('requests.id', 'ASC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('accounting')){ //---ROLES---//
            $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%b. %d, %Y") AS reqdatetime, DATE_FORMAT(requests.needdate, "%b. %d, %Y") AS needdatetime, DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, reason, needdate, requests.item_id AS item_id, qty, assembly_reqnum, reference_upload, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email, notify, verify')
                ->selectRaw('(CASE
                    WHEN requests.verify LIKE "%Confirmed%"
                        THEN CONCAT("CONFIRMED ", status.status)
                    WHEN ((requests.request_type = "2" OR requests.request_type = "7" OR requests.request_type = "8")
                        AND requests.status = "8" AND requests.verify != "Confirmed")
                        OR (requests.request_type = "3" AND requests.status = "9" AND requests.verify != "Demo Confirmed")
                        THEN "FOR CONFIRMATION"
                    ELSE
                        status.status
                    END
                ) AS status')
                ->whereIn('request_type.id', ['2','3','6','8'])
                ->whereNotIn('requests.verify', ['Confirmed'])
                ->whereNotIn('requests.status', ['7','26'])
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->join('status', 'status.id', '=', 'requests.status')
                ->orderBy('reqdate', 'ASC')
                ->orderBy('requests.needdate', 'ASC')
                ->orderBy('requests.id', 'ASC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')){ //---ROLES---//
            $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%b. %d, %Y") AS reqdatetime, DATE_FORMAT(requests.needdate, "%b. %d, %Y") AS needdatetime, DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, reason, needdate, requests.item_id AS item_id, qty, assembly_reqnum, reference_upload, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email, notify, verify')
                ->selectRaw('(CASE
                    WHEN requests.verify LIKE "%Confirmed%"
                        THEN CONCAT("CONFIRMED ", status.status)
                    WHEN ((requests.request_type = "2" OR requests.request_type = "7" OR requests.request_type = "8")
                        AND requests.status = "8" AND requests.verify != "Confirmed")
                        OR (requests.request_type = "3" AND requests.status = "9" AND requests.verify != "Demo Confirmed")
                        THEN "FOR CONFIRMATION"
                    ELSE
                        status.status
                    END
                ) AS status')
                ->whereNotIn('requests.verify', ['Confirmed'])
                ->whereNotIn('requests.status', ['7','14','19','26'])
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->join('status', 'status.id', '=', 'requests.status')
                ->orderBy('reqdate', 'ASC')
                ->orderBy('requests.needdate', 'ASC')
                ->orderBy('requests.id', 'ASC')
                ->get();
        }
        else{
            $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%b. %d, %Y") AS reqdatetime, DATE_FORMAT(requests.needdate, "%b. %d, %Y") AS needdatetime, DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, reason, needdate, requests.item_id AS item_id, qty, assembly_reqnum, reference_upload, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email, notify, verify')
                ->selectRaw('(CASE
                    WHEN requests.verify LIKE "%Confirmed%"
                        THEN CONCAT("CONFIRMED ", status.status)
                    WHEN ((requests.request_type = "2" OR requests.request_type = "7" OR requests.request_type = "8")
                        AND requests.status = "8" AND requests.verify != "Confirmed")
                        OR (requests.request_type = "3" AND requests.status = "9" AND requests.verify != "Demo Confirmed")
                        THEN "FOR CONFIRMATION"
                    ELSE
                        status.status
                    END
                ) AS status')
                ->where('requests.requested_by', auth()->user()->id)
                ->whereNotIn('requests.verify', ['Confirmed'])
                ->whereNotIn('requests.status', ['14','19','26'])
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->join('status', 'status.id', '=', 'requests.status')
                ->orderBy('reqdate', 'ASC')
                ->orderBy('requests.needdate', 'ASC')
                ->orderBy('requests.id', 'ASC')
                ->get();
        }
        return DataTables::of($list)
        ->addColumn('item_desc', function(Requests $list){
            if($list->item_id > 0){
                $items = Item::where('id', $list->item_id)->first()->item;
            }
            else{
                $items = NULL;
            }
            return $items;
        })
        ->addColumn('prep_by', function(Requests $list){
            if($list->prepared_by > 0){
                $users = User::where('id', $list->prepared_by)->first()->name;            
            }
            else{
                $users = NULL;
            }
            return $users;
        })
        ->make(true);
    }

    public function reload(){
        $data_update = Requests::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function reqModal(Request $request){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, reason, needdate, requests.item_id AS item_id, qty, assembly_reqnum, reference_upload, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email, notify, verify')
            ->selectRaw('(CASE
                WHEN requests.verify LIKE "%Confirmed%"
                    THEN CONCAT("CONFIRMED ", status.status)
                WHEN ((requests.request_type = "2" OR requests.request_type = "7" OR requests.request_type = "8")
                    AND requests.status = "8" AND requests.verify != "Confirmed")
                    OR (requests.request_type = "3" AND requests.status = "9" AND requests.verify != "Demo Confirmed")
                    THEN "FOR CONFIRMATION"
                ELSE
                    status.status
                END
            ) AS status')
            ->where('requests.request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get();

        return DataTables::of($list)
        ->addColumn('item_desc', function(Requests $list){
            if($list->item_id > 0){
                $items = Item::where('id', $list->item_id)->first()->item;
            }
            else{
                $items = NULL;
            }
            return $items;
        })
        ->addColumn('prep_by', function(Requests $list){
            if($list->prepared_by > 0){
                $users = User::where('id', $list->prepared_by)->first()->name;            
            }
            else{
                $users = NULL;
            }
            return $users;
        })
        ->toJson();
    }

    public function requestDetails(Request $request){
        $stockreq = StockRequest::query()->select('items.item','items.prodcode AS prodcode','items.UOM AS uom','items.id AS item_id','quantity','served','pending')
            ->join('items', 'items.id', 'stock_request.item')
            ->where('request_number',$request->reqnum)
            ->orderBy('item', 'ASC')
            ->get();        
        
        return DataTables::of($stockreq)
        ->addColumn('qtystock', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya1', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '1')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya2', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '2')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya3', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '3')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtya4', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '4')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtybal', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '5')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->addColumn('qtymal', function(StockRequest $stockreq){
            $stocks = Stock::query()
                ->where('item_id', $stockreq->item_id)
                ->where('location_id', '6')
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->make(true);
    }

    public function reissueItems(Request $request){        
        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
        (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id')
            ->whereIn('stocks.id', $request->items)
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->groupBy('category','prodcode','item','uom','serial','qty','item_id','id')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function receivedItems(Request $request){
        if($request->included != 'no'){
            if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
                $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
            }
        }
        $include[] = $request->request_number;
        
        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
        (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id')
            ->whereIn('request_number', $include)
            ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->groupBy('category','prodcode','item','uom','serial','qty','item_id','id')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }
    
    public function schedItems(Request $request){
        if($request->included != 'no'){
            if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
                $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
            }
        }
        $include[] = $request->request_number;

        $status = Requests::select()
            ->where('request_number', $request->request_number)
            ->first()
            ->status;
        if($status == '3' || $status == '4' || $status == '12' || $status == '30' || $status == '31'){
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['prep','staging','assembly'])
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
        else{
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
            (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['prep','staging','assembly'])
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->groupBy('category','prodcode','item','uom','serialize','serial','qty','item_id','id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
    }

    public function incItems(Request $request){
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $status = Requests::select()
            ->where('request_number', $request->request_number)
            ->first()
            ->status;
        if($status == '17'){
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id')
                ->whereIn('request_number', $include)
                ->where('stocks.status', 'incomplete')
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
        else{
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
            (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id')
                ->whereIn('request_number', $include)
                ->where('stocks.status', 'incomplete')
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->groupBy('category','prodcode','item','uom','serialize','serial','qty','item_id','id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
    }

    public function retItems(Request $request){
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $status = Requests::select()
            ->where('request_number', $request->request_number)
            ->first()
            ->status;
        if($status == '17'){
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id')
                ->whereIn('request_number', $include)
                ->where('stocks.status', 'return')
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
        else{
            $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
            (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id')
                ->whereIn('request_number', $include)
                ->where('stocks.status', 'return')
                ->join('items','items.id','stocks.item_id')
                ->join('categories','categories.id','items.category_id')
                ->groupBy('category','prodcode','item','uom','serialize','serial','qty','item_id','id')
                ->orderBy('item', 'ASC')
                ->get();
    
            return DataTables::of($list)->make(true);
        }
    }

    public function dfcItems(Request $request){
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id')
            ->whereIn('request_number', $include)
            ->where('stocks.status', 'defective')
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function incdfcItems(Request $request){
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id')
            ->whereIn('request_number', $include)
            ->where('stocks.status', 'incdefective')
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function asmItems(Request $request){
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list = Stock::query()->selectRaw('categories.category AS category, items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, items.serialize AS serialize, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id, 
        (CASE WHEN items.serialize = \'NO\' THEN 0 ELSE stocks.id END) AS id, 
        locations.location AS location')
            ->whereIn('assembly_reqnum', $include)
            ->join('items','items.id','stocks.item_id')
            ->join('categories','categories.id','items.category_id')
            ->join('locations','locations.id','stocks.location_id')
            ->groupBy('category','prodcode','item','uom','serialize','serial','qty','item_id','id','location')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function editSerial(Request $request){
        $serials = Stock::query()->select()
            ->where('serial', '!=', 'N/A')
            ->whereRaw('UPPER(serial) = ?', strtoupper($request->newserial))
            ->count();
        if($serials > 0){
            return response('duplicate');
        }

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

            $subject = "EDITED ITEM SERIAL: '$request->origserial' => '$request->newserial'";
            $emails = User::role('admin')
                ->where('status','ACTIVE')
                ->where('email','!=',auth()->user()->email)
                ->get('email')
                ->toArray();
            foreach($emails as $email){
                $sendTo[] = $email['email'];
            }
            $details = [
                'name' => 'ADMIN',
                'editdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'edited_by' => auth()->user()->name,
                'category' => $request->category,
                'item' => $request->item,
                'serialfrom' => $request->origserial,
                'serialto' => $request->newserial
            ];
            Mail::to($sendTo)->send(new editSerial($details, $subject));
            unset($sendTo);
            $details = [
                'name' => auth()->user()->name,
                'editdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'edited_by' => auth()->user()->name,
                'category' => $request->category,
                'item' => $request->item,
                'serialfrom' => $request->origserial,
                'serialto' => $request->newserial
            ];
            Mail::to(auth()->user()->email)->send(new editSerial($details, $subject));

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "EDITED ITEM SERIAL: User successfully edited Serial from '$request->origserial' to '$request->newserial' of Item '$request->item' with Category '$request->category'.";
            $userlogs->save();
        }

        return response($result);
    }

    public function delReqItem(Request $request){
        do{
            $reqitems = StockRequest::where('request_number', $request->req_num)
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
            $userlogs->activity = "REMOVED STOCK REQUEST ITEM: User successfully removed from request list $request->quantity-$request->uom/s of '$request->item' from Stock Request No. $request->req_num.";
            $userlogs->save();
        }

        $count = StockRequest::where('request_number', $request->req_num)->count();
        if($count == 0){
            $files = Requests::where('request_number', $request->req_num)->first()->reference_upload;
            if($files != NULL){
                $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                foreach($files as $file){
                    $file = str_replace('"','',$file);
                    if(file_exists(public_path('uploads/'.$file))){
                        unlink(public_path('uploads/'.$file));
                    }
                }
            }

            do{
                $sql = Requests::where('request_number', $request->req_num)->delete();
            }
            while(!$sql);

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DELETED STOCK REQUEST: User successfully deleted Stock Request No. $request->req_num.";
            $userlogs->save();
        }

        $data = array('result' => $result, 'count' => $count);
        return response()->json($data);
    }

    public function deleteRequest(Request $request){
        $files = Requests::where('request_number', $request->request_number)->first()->reference_upload;
        if($files != NULL){
            $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
            foreach($files as $file){
                $file = str_replace('"','',$file);
                if(file_exists(public_path('uploads/'.$file))){
                    unlink(public_path('uploads/'.$file));
                }
            }
        }

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

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "DELETED STOCK REQUEST: User successfully deleted Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function approveRequest(Request $request){
        if(Requests::where('request_number', $request->request_number)->first()->prepared_by > 0){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '3', 'reason' => '']);
            }
            while(!$sql);
        }
        else{
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '1', 'reason' => '']);
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

    public function logApprove(Request $request){
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, request_type.id AS req_type_id, client_name, location, contact, remarks, reference, reason, needdate, asset_reqby_email')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->first();
        }
        while(!$request_details);

        if($request_details->reqtype == 'SALES' || $request_details->reqtype == 'FOR STAGING'){
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get()
                    ->toArray();
                foreach($items as $key => $value){
                    if($value['warranty'] == '0' || $value['warranty'] == ''){
                        $items[$key]['Warranty_Name'] = 'NO WARRANTY';
                    }
                    else{
                        $items[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty'])->first()->Warranty_Name;
                    }
                }
            }
            while(!$items);
        }
        else{
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$items);
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
        
        $subject = '[APPROVED] STOCK REQUEST NO. '.$request->request_number;
        $emails = User::role('accounting')->where('status','ACTIVE')->get('email')->toArray();
        foreach($emails as $email){
            $sendTo[] = $email['email'];
        }
        $details = [
            'name' => 'ACCOUNTING',
            'action' => 'STOCK REQUEST',
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
            'approvedby' => auth()->user()->name,
            'role' => 'Accounting',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($sendTo)->send(new approvedRequest($details, $subject));
        unset($sendTo);
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
            'contact' => $request_details->contact,
            'remarks' => $request_details->remarks,
            'reference' => $request_details->reference,
            'approvedby' => auth()->user()->name,
            'role' => 'Sales',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($request_details->email)->send(new approvedRequest($details, $subject));
        $details = [
            'name' => $request_details->client_name,
            'action' => 'STOCK REQUEST',
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
            'approvedby' => auth()->user()->name,
            'role' => '',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($request_details->asset_reqby_email)->send(new approvedRequest($details, $subject));

        switch($request_details->req_type_id){
            case 1: $reqtype = 'Service Unit'; break;
            case 2: $reqtype = 'Sales'; break;
            case 3: $reqtype = 'Demo Unit'; break;
            case 4: $reqtype = 'Replacement'; break;
            case 5: $reqtype = 'Assembly'; break;
            case 6: $reqtype = 'Merchant'; break;
            case 7: $reqtype = 'Fixed Asset'; break;
            case 8: $reqtype = 'For Staging'; break;
            default: $reqtype = NULL;
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "APPROVED $request_details->reqtype STOCK REQUEST: User successfully approved $reqtype Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function disapproveRequest(Request $request){
        if(Requests::where('request_number', $request->request_number)->first()->prepared_by > 0){
            $request_number_prev = Requests::where('request_number', $request->request_number)->first()->orderID;
            $list = StockRequest::where('request_number', $request->request_number)->get();
            foreach($list as $key){
                if($key->quantity == StockRequest::where('request_number', $request_number_prev)->where('item', $key->item)->first()->pending){
                    Stock::where('request_number', $request->request_number)
                        ->where('item_id', $key->item)
                        ->update(['request_number' => $request_number_prev]);
                    StockRequest::where('request_number', $request_number_prev)
                        ->where('item', $key->item)
                        ->increment('served', $key->quantity);
                    StockRequest::where('request_number', $request_number_prev)
                        ->where('item', $key->item)
                        ->decrement('pending', $key->quantity);
                    StockRequest::where('request_number', $request->request_number)
                        ->where('item', $key->item)
                        ->decrement('served', $key->quantity);
                    StockRequest::where('request_number', $request->request_number)
                        ->where('item', $key->item)
                        ->increment('pending', $key->quantity);
                }
                else{
                    Stock::where('request_number', $request->request_number)
                        ->where('item_id', $key->item)
                        ->update(['status' => 'return', 'warranty_id' => '', 'batch' => '', 'user_id' => auth()->user()->id]);
                    StockRequest::where('request_number', $request->request_number)
                        ->where('item', $key->item)
                        ->decrement('served', $key->quantity);
                    StockRequest::where('request_number', $request->request_number)
                        ->where('item', $key->item)
                        ->increment('pending', $key->quantity);
                }
            }
            if(Stock::where('request_number', $request->request_number)->where('status','return')->count() != 0){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '11', 'orderID' => '', 'prepared_by' => '', 'schedule' => '', 'prepdate' => '']);
                }
                while(!$sql);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '7', 'reason' => ucfirst($request->reason), 'orderID' => '', 'prepared_by' => '', 'schedule' => '', 'prepdate' => '']);
                }
                while(!$sql);
            }

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
                if(StockRequest::where('request_number', $request_number_prev)->sum('pending') == 0){
                    Requests::where('request_number', $request_number_prev)
                        ->update(['status' => '30']);
                }
            }
        }
        else{
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '7', 'reason' => ucfirst($request->reason)]);
            }
            while(!$sql);

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
        }
        return response($result);
    }

    public function logDisapprove(Request $request){
        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, request_type.id AS req_type_id, client_name, location, contact, remarks, reference, reason, needdate')
                ->where('requests.request_number', $request->request_number)
                ->join('users', 'users.id', '=', 'requests.requested_by')
                ->join('request_type', 'request_type.id', '=', 'requests.request_type')
                ->first();
        }
        while(!$request_details);

        if($request_details->reqtype == 'SALES' || $request_details->reqtype == 'FOR STAGING'){
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get()
                    ->toArray();
                foreach($items as $key => $value){
                    if($value['warranty'] == '0' || $value['warranty'] == ''){
                        $items[$key]['Warranty_Name'] = 'NO WARRANTY';
                    }
                    else{
                        $items[$key]['Warranty_Name'] = Warranty::query()->where('id',$value['warranty'])->first()->Warranty_Name;
                    }
                }
            }
            while(!$items);
        }
        else{
            do{
                $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $request->request_number)
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$items);
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
        
        $subject = '[DISAPPROVED] STOCK REQUEST NO. '.$request->request_number;
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
            'contact' => $request_details->contact,
            'remarks' => $request_details->remarks,
            'reference' => $request_details->reference,
            'reason' => $request_details->reason,
            'disapprovedby' => auth()->user()->name,
            'role' => 'Sales',
            'items' => $items,
            'files' => $attachments
        ];
        Mail::to($request_details->email)->send(new disapprovedRequest($details, $subject));

        switch($request_details->req_type_id){
            case 1: $reqtype = 'Service Unit'; break;
            case 2: $reqtype = 'Sales'; break;
            case 3: $reqtype = 'Demo Unit'; break;
            case 4: $reqtype = 'Replacement'; break;
            case 5: $reqtype = 'Assembly'; break;
            case 6: $reqtype = 'Merchant'; break;
            case 7: $reqtype = 'Fixed Asset'; break;
            case 8: $reqtype = 'For Staging'; break;
            default: $reqtype = NULL;
        }

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "DISAPPROVED $request_details->reqtype STOCK REQUEST: User successfully disapproved $reqtype Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function reschedRequest(Request $request){
        if($request->request_type == '4' || $request->request_type == '5' || $request->request_type == '7' || $request->request_type == '8'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '17', 'prepared_by' => auth()->user()->id, 'schedule' => $request->resched]);
            }
            while(!$sql);
            $sched = 'RESCHEDULED FOR RECEIVING';
        }
        else{
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '16', 'prepared_by' => auth()->user()->id, 'schedule' => $request->resched]);
            }
            while(!$sql);
            $sched = 'RESCHEDULED';
        }
        
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
            switch($request->request_type){
                case 1: $reqtype = 'Service Unit'; break;
                case 2: $reqtype = 'Sales'; break;
                case 3: $reqtype = 'Demo Unit'; break;
                case 4: $reqtype = 'Replacement'; break;
                case 5: $reqtype = 'Assembly'; break;
                case 6: $reqtype = 'Merchant'; break;
                case 7: $reqtype = 'Fixed Asset'; break;
                case 8: $reqtype = 'For Staging'; break;
                default: $reqtype = NULL;
            }
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = $sched." STOCK REQUEST: User successfully rescheduled on $request->resched $reqtype Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function stageRequest(Request $request){
        if(StockRequest::where('request_number', $request->request_number)->sum('pending') == 0){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '30']);
            }
            while(!$sql);
            $sched = 'FOR STAGING';
        }
        else{
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '31']);
            }
            while(!$sql);
            $sched = 'PARTIAL FOR STAGING';
        }

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = $sched." STOCK REQUEST: User successfully processed for staging Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
    }

    public function inTransit(Request $request){
        if($request->status == '2' || $request->status == '30'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '3']);
            }
            while(!$sql);
            $sched = 'FOR RECEIVING';
        }
        else if($request->status == '5' || $request->status == '31'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '4']);
            }
            while(!$sql);
            $sched = 'PARTIAL FOR RECEIVING';
        }
        else if($request->status == '16'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->where('status','16')
                    ->update(['status' => '17']);
            }
            while(!$sql);
            $sched = 'INCOMPLETE FOR RECEIVING';
        }
        else{
            return response('false');
        }

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = $sched." STOCK REQUEST: User successfully processed for receiving Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response($result);
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
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '10', 'reference' => strtoupper($request->reference)]);
            }
            while(!$sql);

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
        }

        return response($result);
    }

    public function sellItems(Request $request){
        do{
            $sql = Stock::where('id', $request->id)
                ->whereIn('status', ['demo'])
                ->update(['status' => 'out', 'user_id' => auth()->user()->id, 'warranty_id' => $request->warranty_id]);
        }
        while(!$sql);
        
        return response('true');
    }

    public function logSold(Request $request){
        if(Requests::where('request_number', $request->request_number)->count() == 0){
            return response('false');
        }
        Stock::where('request_number', $request->request_number)
            ->where('status', '=', 'demo')
            ->update(['status' => 'return', 'warranty_id' => '', 'batch' => '', 'user_id' => auth()->user()->id]);
        
        $returns = Stock::where('request_number', $request->request_number)
            ->where('status', '=', 'return')
            ->count();
        if($returns != 0){
            $sql = Requests::where('request_number', $request->request_number)
                ->update(['status' => '27']);
        }

        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['out'])
            ->where('batch', '=', 'old')
            ->update(['batch' => 'new']);

        if(Requests::where('request_number', $request->request_number)->first()->status == '10'){
            Requests::where('request_number', $request->request_number)
                ->update(['verify' => 'Confirmed']);
        }

        do{
            $request_details = Requests::selectRaw('requests.created_at AS reqdate, users.name AS reqby, users.email AS email, request_type.name AS reqtype, request_type.id AS req_type_id, status.id AS status_id, client_name, location, contact, remarks, reference, schedule, needdate, prepdate')
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

        do{
            $items = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['out'])
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
        
        $subject = '[SOLD] STOCK REQUEST NO. '.$request->request_number;
        $emails = User::role('admin')->where('status','ACTIVE')->get('email')->toArray();
        foreach($emails as $email){
            $sendTo[] = $email['email'];
        }
        $details = [
            'name' => 'ADMIN',
            'action' => 'STOCK REQUEST',
            'verb' => 'SOLD',
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
            'prepared_by' => $prep->prepby,
            'prepdate' => $request_details->prepdate,
            'scheddate' => $request_details->schedule,
            'receivedby' => auth()->user()->name,
            'role' => 'Admin',
            'items' => $items,
            'olditems' => array(),
            'incitems' => array(),
            'penditems' => array(),
            'files' => array(),
            'req_type_id' => $request_details->req_type_id,
            'status_id' => $request_details->status_id,
            'token' => ''
        ];
        Mail::to($sendTo)->send(new receivedRequest($details, $subject));
        unset($sendTo);
        $emails = User::role('accounting')->where('status','ACTIVE')->get('email')->toArray();
        foreach($emails as $email){
            $sendTo[] = $email['email'];
        }
        $details = [
            'name' => 'ACCOUNTING',
            'action' => 'STOCK REQUEST',
            'verb' => 'SOLD',
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
            'prepared_by' => $prep->prepby,
            'prepdate' => $request_details->prepdate,
            'scheddate' => $request_details->schedule,
            'receivedby' => auth()->user()->name,
            'role' => 'Accounting',
            'items' => $items,
            'olditems' => array(),
            'incitems' => array(),
            'penditems' => array(),
            'files' => $attachments,
            'req_type_id' => $request_details->req_type_id,
            'status_id' => $request_details->status_id,
            'token' => ''
        ];
        Mail::to($sendTo)->send(new receivedRequest($details, $subject));
        unset($sendTo);
        $details = [
            'name' => auth()->user()->name,
            'action' => 'STOCK REQUEST',
            'verb' => 'SOLD',
            'request_number' => $request->request_number,
            'reqdate' => $request_details->reqdate,
            'requested_by' => auth()->user()->name,
            'needdate' => $request_details->needdate,
            'reqtype' => $request_details->reqtype,
            'client_name' => $request_details->client_name,
            'location' => $request_details->location,
            'contact' => $request_details->contact,
            'remarks' => $request_details->remarks,
            'reference' => $request_details->reference,
            'prepared_by' => $prep->prepby,
            'prepdate' => $request_details->prepdate,
            'scheddate' => $request_details->schedule,
            'receivedby' => auth()->user()->name,
            'role' => 'Sales',
            'items' => $items,
            'olditems' => array(),
            'incitems' => array(),
            'penditems' => array(),
            'files' => $attachments,
            'req_type_id' => $request_details->req_type_id,
            'status_id' => $request_details->status_id,
            'token' => ''
        ];
        Mail::to(auth()->user()->email)->send(new receivedRequest($details, $subject));

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "SOLD STOCK REQUEST: User successfully sold Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function returnRequest(Request $request){
        if($request->all == 'true'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '11']);
            }
            while(!$sql);

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }

            return response($result);
        }
        else{
            return response('true');
        }
    }

    public function returnItems(Request $request){
        do{
            $sql = Stock::where('id', $request->id)
                ->whereIn('status', ['demo'])
                ->update(['status' => 'return', 'warranty_id' => '', 'batch' => '', 'user_id' => auth()->user()->id]);
        }
        while(!$sql);
        
        return response('true');
    }

    public function logReturn(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "RETURNED STOCK REQUEST ITEMS: User successfully returned items of Stock Request No. $request->request_number.";
        $userlogs->save();

        return response('true');
    }

    public function checkStatus(Request $request){       
        do{
            $sql = Requests::where('request_number', $request->assembly_reqnum)->first()->status;
        }
        while(!$sql);

        return response($sql);
    }

    public function stockreq(Request $request){       
        $list = StockRequest::select('items.prodcode AS prodcode', 'items.item AS item', 'items.id AS item_id', 'stock_request.quantity AS qty', 'stock_request.served AS served', 'stock_request.pending AS pending', 'items.UOM AS uom', 'stock_request.warranty AS warranty_id')
            ->where('stock_request.item', $request->item_id)
            ->where('stock_request.request_number', $request->reqnum)
            ->where('stocks.status','in')
            ->whereIn('stocks.location_id',['1','2','3','4'])
            ->join('items','items.id','=','stock_request.item')
            ->join('stocks','stocks.item_id','stock_request.item')
            ->join('locations','locations.id','stocks.location_id')
            ->orderBy('item', 'ASC')
            ->limit(1)
            ->get();

        return DataTables::of($list)
        ->addColumn('qtystock', function(StockRequest $list){
            $stocks = Stock::query()
                ->where('item_id', $list->item_id)
                ->whereIn('location_id', ['1','2','3','4'])
                ->where('status', 'in')
                ->count();
            return $stocks;
        })
        ->toJson();
    }

    public function soldreq(Request $request){
        $list = Stock::query()->selectRaw('items.item AS item, items.prodcode AS prodcode, items.UOM AS uom, stocks.serial AS serial, stocks.qty AS qty, stocks.item_id AS item_id, stocks.id AS id, locations.location AS location')
            ->where('stocks.id', $request->item_id)
            ->join('items','items.id','stocks.item_id')
            ->join('locations','locations.id','stocks.location_id')
            ->orderBy('item', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function setwarranty(Request $request){
        $list = Warranty::select('id','Warranty_Name')->orderBy('Warranty_Name','ASC')->get();
        
        return response()->json($list);
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
            ->first();

        return $list;
    }

    public function prepareItems(Request $request){
        if($request->req_type_id == '5'){
            do{
                $sql = Stock::where('id',$request->stock_id)
                    ->update(['status' => 'assembly', 'user_id' => auth()->user()->id, 'request_number' => $request->request_number]);
            }
            while(!$sql);
        }
        else if($request->req_type_id == '4' && Requests::where('request_number', $request->orig_reqnum)->first()->request_type == '5'){
            do{
                $sql = Stock::where('id',$request->stock_id)
                    ->update(['status' => 'assembly', 'user_id' => auth()->user()->id, 'request_number' => $request->request_number]);
            }
            while(!$sql);
        }
        else if($request->req_type_id == '4' && Requests::where('request_number', $request->orig_reqnum)->first()->request_type == '8'){
            do{
                if($request->warranty_id > -1){
                    $warranty_id = $request->warranty_id;
                }
                else{
                    $warranty_id = '';
                }
                $sql = Stock::where('id',$request->stock_id)
                    ->update(['status' => 'staging', 'user_id' => auth()->user()->id, 'warranty_id' => $warranty_id, 'request_number' => $request->request_number]);
            }
            while(!$sql);
        }
        else{
            do{
                if($request->warranty_id > -1){
                    $warranty_id = $request->warranty_id;
                }
                else{
                    $warranty_id = '';
                }
                $sql = Stock::where('id',$request->stock_id)
                    ->update(['status' => 'prep', 'user_id' => auth()->user()->id, 'warranty_id' => $warranty_id, 'request_number' => $request->request_number]);
            }
            while(!$sql);
        }

        if(!$sql){
            $result = 'false';
        }
        else{
            $result = 'true';

            StockRequest::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->increment('served', $request->qty);

            StockRequest::where('request_number', $request->request_number)
                ->where('item',$request->item_id)
                ->decrement('pending', $request->qty);
        }

        return response($result);
    }

    public function logSched(Request $request){
        if($request->req_type_id == '4' || $request->req_type_id == '5' || $request->req_type_id == '7'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '3']);
            }
            while(!$sql);
            $sched = 'SCHEDULED FOR RECEIVING';
        }
        else if($request->req_type_id == '8'){
            $total = StockRequest::where('request_number', $request->request_number)->sum('pending');
            if($total == 0){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '3']);
                }
                while(!$sql);
                $sched = 'SCHEDULED FOR RECEIVING';
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '4']);
                }
                while(!$sql);
                $sched = 'PARTIAL SCHEDULED FOR RECEIVING';
            }
        }
        else{
            $total = StockRequest::where('request_number', $request->request_number)->sum('pending');
            if($total == 0){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '2']);
                }
                while(!$sql);
                $sched = 'SCHEDULED';
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '5']);
                }
                while(!$sql);
                $sched = 'PARTIAL SCHEDULED';
            }
        }

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            Requests::where('request_number', $request->request_number)
                ->update(['prepared_by' => auth()->user()->id, 'schedule' => $request->schedOn, 'prepdate' => date('Y-m-d')]);

            if($request->req_type_id == '7'){
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "PREPARED FOR RECEIVING STOCK REQUEST: User successfully prepared on $request->schedOn Fixed Asset Stock Request No. $request->request_number.";
                $userlogs->save();
            }
            else{
                switch($request->req_type_id){
                    case 1: $reqtype = 'Service Unit'; break;
                    case 2: $reqtype = 'Sales'; break;
                    case 3: $reqtype = 'Demo Unit'; break;
                    case 4: $reqtype = 'Replacement'; break;
                    case 5: $reqtype = 'Assembly'; break;
                    case 6: $reqtype = 'Merchant'; break;
                    case 7: $reqtype = 'Fixed Asset'; break;
                    case 8: $reqtype = 'For Staging'; break;
                    default: $reqtype = NULL;
                }
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = $sched." STOCK REQUEST: User successfully scheduled on $request->schedOn $reqtype Stock Request No. $request->request_number.";
                $userlogs->save();
            }
        }

        return response($result);
    }

    public function receiveRequest(Request $request){
        Requests::where('request_number', $request->request_number)
            ->update(['verify' => '']);
        if($request->inc == 'true'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '15']);
            }
            while(!$sql);
        }
        else{
            $total = StockRequest::where('request_number', $request->request_number)->sum('pending');
            if($request->request_type == '3'){
                if($total == 0){
                    do{
                        $sql = Requests::where('request_number', $request->request_number)
                            ->update(['status' => '9']);
                    }
                    while(!$sql);
                }
                else{
                    do{
                        $sql = Requests::where('request_number', $request->request_number)
                            ->update(['status' => '24']);
                    }
                    while(!$sql);
                }
            }
            if($request->request_type == '8' && ($request->status == '3' || $request->status == '4')){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '32']);
                }
                while(!$sql);
            }
            else{
                if($total == 0){
                    do{
                        $sql = Requests::where('request_number', $request->request_number)
                            ->update(['status' => '8']);
                    }
                    while(!$sql);
                    if($request->request_type == '1' || $request->request_type == '4' || $request->request_type == '5' || $request->request_type == '6'){
                        Requests::where('request_number', $request->request_number)
                            ->update(['verify' => 'Confirmed']);
                    }
                }
                else{
                    do{
                        $sql = Requests::where('request_number', $request->request_number)
                            ->update(['status' => '24']);
                    }
                    while(!$sql);
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

    public function receiveItems(Request $request){
        if($request->status == '3' || $request->status == '4'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->whereNotIn('status', ['out','staging','asset','demo','assembly','assembled'])
                    ->update(['status' => 'received', 'user_id' => auth()->user()->id]);
            }
            while(!$sql);
        }
        if($request->status == '17'){
            if($request->request_type == '3'){
                do{
                    $sql = Stock::where('id', $request->id)
                        ->update(['status' => 'demo', 'user_id' => auth()->user()->id]);
                }
                while(!$sql);
                if(Transfer::where('stock_id', $request->id)->where('request_number', $request->request_number)->count() == 0){
                    $transfer = new Transfer;
                    $transfer->request_number = $request->request_number;
                    $transfer->stock_id = $request->id;
                    $transfer->save();
                }
            }
            else if($request->request_type == '7'){
                do{
                    $sql = Stock::where('id', $request->id)
                        ->update(['status' => 'asset', 'user_id' => auth()->user()->id]);
                }
                while(!$sql);
            }
            else if($request->request_type == '8'){
                do{
                    $sql = Stock::where('id', $request->id)
                        ->update(['status' => 'staging', 'user_id' => auth()->user()->id]);
                }
                while(!$sql);
            }
            else{
                do{
                    $sql = Stock::where('id', $request->id)
                        ->update(['status' => 'out', 'user_id' => auth()->user()->id]);
                }
                while(!$sql);
            }
        }
        
        return response('true');
    }

    public function logReceive(Request $request){
        if($request->status == '3' || $request->status == '4'){
            if($request->request_type == '3'){
                Stock::where('request_number', $request->request_number)
                    ->where('status', '=', 'received')
                    ->update(['status' => 'demo', 'user_id' => auth()->user()->id]);

                $demos = Stock::select('id')
                    ->where('request_number', $request->request_number)
                    ->where('status', 'demo')
                    ->get();
                
                foreach($demos as $demo){
                    if(Transfer::where('stock_id', $demo->id)->where('request_number', $request->request_number)->count() == 0){
                        $transfer = new Transfer;
                        $transfer->request_number = $request->request_number;
                        $transfer->stock_id = $demo->id;
                        $transfer->save();
                    }
                }
            }
            else if($request->request_type == '7'){
                Stock::where('request_number', $request->request_number)
                    ->where('status', '=', 'received')
                    ->update(['status' => 'asset', 'user_id' => auth()->user()->id]);
            }
            else if($request->request_type == '8'){
                Stock::where('request_number', $request->request_number)
                    ->where('status', '=', 'received')
                    ->update(['status' => 'staging', 'user_id' => auth()->user()->id]);
            }
            else{
                Stock::where('request_number', $request->request_number)
                    ->where('status', '=', 'received')
                    ->update(['status' => 'out', 'user_id' => auth()->user()->id]);
            }

            Stock::where('request_number', $request->request_number)
                ->whereNotIn('status', ['out','staging','asset','demo','assembly','assembled'])
                ->update(['status' => 'incomplete', 'user_id' => auth()->user()->id]);
        }

        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['out','staging','asset','demo','assembly','assembled'])
            ->where('batch', '=', 'new')
            ->update(['batch' => 'old']);
        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['out','staging','asset','demo','assembly','assembled'])
            ->where('batch', '=', '')
            ->update(['batch' => 'new']);

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

        if($request_details->req_type_id == 2 || $request_details->req_type_id == 6 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
            do{
                $items = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                    ->whereIn('request_number', $include)
                    ->whereIn('stocks.batch', ['new'])
                    ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
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
                    ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
                    ->join('items','items.id','stocks.item_id')
                    ->groupBy('prodcode','item','uom','serial','qty','item_id')
                    ->orderBy('item', 'ASC')
                    ->get();
            }
            while(!$items);
        }
        if(Stock::whereIn('request_number', $include)->where('batch','old')->count() != 0 && Stock::whereIn('request_number', $include)->where('batch','new')->count() == 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 6 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
                do{
                    $items = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
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
                        ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
                        ->join('items','items.id','stocks.item_id')
                        ->groupBy('prodcode','item','uom','serial','qty','item_id')
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                while(!$items);
            }
        }

        if(Stock::whereIn('request_number', $include)->where('batch','old')->count() != 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 6 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
                do{
                    $olditems = Stock::query()->select('items.prodcode AS prodcode', 'items.item AS item', 'items.UOM AS uom', 'stocks.serial AS serial', DB::raw('SUM(stocks.qty) AS qty'), 'stocks.item_id AS item_id', 'stocks.warranty_id AS warranty_id')
                        ->whereIn('request_number', $include)
                        ->whereIn('stocks.batch', ['old'])
                        ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
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
                        ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
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

        if(Stock::whereIn('request_number', $include)->where('status','incomplete')->count() != 0){
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 6 || $request_details->req_type_id == 8 || ($request_details->req_type_id == 3 && ($request_details->status_id == 10 || $request_details->status_id >= 27))){
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

        if($request_details->req_type_id == 2 || $request_details->req_type_id == 3 || $request_details->req_type_id == 7 || $request_details->req_type_id == 8){
            if($request_details->req_type_id == 8 && ($request_details->status_id == 15 || $request_details->status_id == 32)){
                echo(null);
            }
            else{
                do{
                    $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $key = array();
                    $charLength = strlen($char) - 1;
                    for($i = 0; $i < 25; $i++){
                        $n = rand(0, $charLength);
                        $key[] = $char[$n];
                    }
                    $token = implode($key);
                    Requests::where('request_number', $request->request_number)
                        ->update(['token' => $token]);
                }
                while(Requests::query()->select()->where('token',$token)->count() > 1);
            }
        }

        if($request_details->req_type_id == '7'){
            $subject = '[RECEIVED] STOCK REQUEST NO. '.$request->request_number;
            $details = [
                'name' => $request_details->asset_reqby,
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
                'token' => $token
            ];
            Mail::to($request_details->asset_reqby_email)->send(new receivedRequest($details, $subject));
            
            if(Stock::whereIn('request_number', $include)->where('status','incomplete')->count() == 0){
                $inc1 = 'COMPLETE';
                $inc2 = 'complete';
            }
            else{
                $inc1 = 'INCOMPLETE';
                $inc2 = 'incomplete';
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED $inc1 FIXED ASSET STOCK REQUEST: User successfully received $inc2 requested items of Fixed Asset Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            if($request_details->req_type_id == 8 && ($request_details->status_id == 15 || $request_details->status_id == 32)){
                if(Stock::whereIn('request_number', $include)->where('status','incomplete')->count() == 0){
                    $inc1 = 'COMPLETE';
                    $inc2 = 'complete';
                }
                else{
                    $inc1 = 'INCOMPLETE';
                    $inc2 = 'incomplete';
                }
    
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "RECEIVED $inc1 FOR STAGING: User successfully received $inc2 items for staging of Stock Request No. $request->request_number.";
                $userlogs->save();

                return response('true');
            }
            $subject = '[RECEIVED] STOCK REQUEST NO. '.$request->request_number;
            if($request_details->req_type_id == 1 || $request_details->req_type_id == 4 || $request_details->req_type_id == 5 || $request_details->req_type_id == 6){
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
                    'receivedby' => auth()->user()->name,
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
                if($request_details->req_type_id == 6){
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
                        'receivedby' => auth()->user()->name,
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
                    'receivedby' => auth()->user()->name,
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
            if($request_details->req_type_id == 2 || $request_details->req_type_id == 3 || $request_details->req_type_id == 8){
                $details = [
                    'name' => $request_details->client_name,
                    'action' => 'STOCK REQUEST',
                    'verb' => 'RECEIVED',
                    'request_number' => $request->request_number,
                    'reqdate' => $request_details->reqdate,
                    'requested_by' => auth()->user()->name,
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
                    'role' => '',
                    'items' => $items,
                    'olditems' => $olditems,
                    'incitems' => $incitems,
                    'penditems' => $penditems,
                    'files' => $attachments,
                    'req_type_id' => $request_details->req_type_id,
                    'status_id' => $request_details->status_id,
                    'token' => $token
                ];
                Mail::to($request_details->asset_reqby_email)->send(new receivedRequest($details, $subject));
            }

            if(Stock::whereIn('request_number', $include)->where('status','incomplete')->count() == 0){
                $inc1 = 'COMPLETE';
                $inc2 = 'complete';
            }
            else{
                $inc1 = 'INCOMPLETE';
                $inc2 = 'incomplete';
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED $inc1 STOCK REQUEST: User successfully received $inc2 requested items of Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
    }

    public function receiveReplacement(Request $request){
        if($request->inc == 'true'){
            Requests::where('request_number', $request->request_number)
                ->update(['status' => '15']);
            Requests::where('request_number', $request->assembly_reqnum)
                ->update(['status' => '23']);
        }
        else{
            Requests::where('request_number', $request->request_number)
                ->update(['status' => '19']);
            if(StockRequest::where('request_number', $request->assembly_reqnum)->sum('pending') == 0){
                Requests::where('request_number', $request->assembly_reqnum)
                    ->update(['status' => '30']);
            }
            else{
                Requests::where('request_number', $request->assembly_reqnum)
                    ->update(['status' => '31']);
            }
        }

        return response('true');
    }

    public function replacementItems(Request $request){
        if($request->status == '3'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'received', 'user_id' => auth()->user()->id]);
            }
            while(!$sql);
        }
        if($request->status == '17'){
            do{
                $sql = Stock::where('id', $request->id)
                    ->update(['status' => 'staging', 'user_id' => auth()->user()->id]);
            }
            while(!$sql);
        }
        
        return response('true');
    }

    public function logReplacement(Request $request){
        if($request->status == '3'){
            Stock::where('request_number', $request->request_number)
                ->where('status', '=', 'staging')
                ->update(['status' => 'incomplete', 'user_id' => auth()->user()->id]);
            
            Stock::where('request_number', $request->request_number)
                ->where('status', '=', 'received')
                ->update(['status' => 'staging', 'user_id' => auth()->user()->id]);
        }
        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['staging'])
            ->where('batch', '=', 'new')
            ->update(['batch' => 'old']);
        Stock::where('request_number', $request->request_number)
            ->whereIn('status', ['staging'])
            ->where('batch', '=', '')
            ->update(['batch' => 'new']);

        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE FOR STAGING REPLACEMENTS: User successfully received incomplete replacement items of For Staging Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE FOR STAGING REPLACEMENTS: User successfully received complete replacement items of For Staging Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
    }

    public function receiveReturned(Request $request){
        if($request->status_id == '11' || $request->status_id == '25'){
            if($request->inc == 'true'){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '25']);
                }
                while(!$sql);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '26']);
                }
                while(!$sql);
            }
        }
        else{
            if($request->inc == 'true'){
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '28']);
                }
                while(!$sql);
            }
            else{
                do{
                    $sql = Requests::where('request_number', $request->request_number)
                        ->update(['status' => '29']);
                    Requests::where('request_number', $request->request_number)
                        ->update(['verify' => 'Confirmed']);
                }
                while(!$sql);
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

    public function receiveRetItems(Request $request){
        do{
            $sql = Stock::where('id', $request->id)
                ->update(['status' => 'in', 'request_number' => '', 'user_id' => auth()->user()->id]);
        }
        while(!$sql);
        
        return response('true');
    }

    public function logReceiveRet(Request $request){
        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE RETURNED ITEMS: User successfully received incomplete returned items of Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE RETURNED ITEMS: User successfully received complete returned items of Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
    }

    public function receiveDefective(Request $request){
        if($request->inc == 'true'){
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '21']);
            }
            while(!$sql);
        }
        else{
            do{
                $sql = Requests::where('request_number', $request->request_number)
                    ->update(['status' => '22']);
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

    public function receiveDfcItems(Request $request){
        do{
            $sql = Stock::where('id', $request->id)
                ->update(['status' => 'dfcreceived', 'warranty_id' => '', 'batch' => '', 'user_id' => auth()->user()->id]);
        }
        while(!$sql);
        
        return response('true');
    }

    public function logReceiveDfc(Request $request){
        Stock::where('request_number', $request->request_number)
            ->where('status', '=', 'defective')
            ->update(['status' => 'incdefective', 'user_id' => auth()->user()->id]);
        
        Stock::where('request_number', $request->request_number)
            ->where('status', '=', 'dfcreceived')
            ->update(['status' => 'defectives', 'user_id' => auth()->user()->id, 'defectiveDate' => date("Y-m-d H:i:s")]);

        if(Requests::where('request_number', $request->request_number)->first()->request_type == 8){
            $reqtype = 'For Staging';
            $items = 'items';
        }
        else{
            $reqtype = 'Assembly';
            $items = 'parts';
        }

        if($request->inc == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED INCOMPLETE DEFECTIVE ITEMS: User successfully received incomplete defective $items of $reqtype Stock Request No. $request->request_number.";
            $userlogs->save();
        }
        else{
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "RECEIVED COMPLETE DEFECTIVE ITEMS: User successfully received complete defective $items of $reqtype Stock Request No. $request->request_number.";
            $userlogs->save();
        }

        return response('true');
    }

    public function checkProcessed(Request $request){       

        $count = Stock::query()->select()
            ->where('request_number', $request->request_number)
            ->whereIn('status', ['out','demo'])
            ->count();
                
        return $count;
    }
    
    public function getReceive(Request $request){       
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list = Stock::query()->selectRaw('users.name AS recby, stocks.created_at AS recsched')
            ->whereIn('assembly_reqnum', $include)
            ->join('users','users.id','stocks.user_id')
            ->limit(1)
            ->first();

        return $list;
    }
    
    public function getLink(Request $request){       
        $link = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        return $link;
    }

    public function printRequest(Request $request){
        $list = Requests::selectRaw('requests.id AS req_id, requests.created_at AS req_date, requests.request_number AS req_num, requests.requested_by AS user_id, users.name AS req_by, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, client_name, location, contact, remarks, reference, needdate, prepdate, requests.item_id AS item_id, qty, assembly_reqnum, orderID, asset_reqby, asset_apvby, asset_reqby_email, asset_apvby_email')
            ->where('request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->first();

        if(!$list){
            return redirect()->to('/stockrequest');
        }

        $list1 = Item::selectRaw('items.item AS item_desc, items.prodcode AS item_code')
            ->where('id', '=', $list->item_id)
            ->get();

        $list2 = Requests::selectRaw('users.name AS prepby')
            ->where('requests.request_number', $request->request_number)
            ->join('users', 'users.id', '=', 'requests.prepared_by')
            ->first();
        
        if(!$list2){
            return redirect()->to('/stockrequest');
        }
        
        if(Requests::where('assembly_reqnum', $request->request_number)->count() > 0){
            $include[] = Requests::where('assembly_reqnum', $request->request_number)->first()->request_number;
        }
        $include[] = $request->request_number;

        $list3 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
            ->whereIn('request_number', $include)
            ->whereIn('stocks.batch', ['new',''])
            ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
            ->join('items','items.id','stocks.item_id')
            ->groupBy('prodcode','item','uom','serial','qty','item_id')
            ->orderBy('item', 'ASC')
            ->get();
        if(Stock::whereIn('request_number', $include)->where('batch','old')->count() != 0 && Stock::whereIn('request_number', $include)->where('batch','new')->count() == 0){
            $list3 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.batch', ['old'])
                ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
            $getList3 = true;
        }
        else{
            $getList3 = false;
        }

        if($request->demo == 'received'){
            if($list->status_id < 9){
                return redirect()->to('/stockrequest');
            }
            unset($list3);
            $list3 = Transfer::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, items.id AS item_id')
                ->where('transferred_items.request_number', $request->request_number)
                ->join('stocks','stocks.id','transferred_items.stock_id')
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        
        if(!$list3){
            return redirect()->to('/stockrequest');
        }

        if(Stock::whereIn('request_number', $include)->where('status','incomplete')->count() != 0){
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

        if(Stock::whereIn('request_number', $include)->where('batch','old')->count() != 0){
            $list5 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.batch', ['old'])
                ->whereIn('stocks.status', ['out','staging','asset','demo','assembly','assembled'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        else{
            $list5 = array();
        }

        if(Stock::whereIn('request_number', $include)->whereIn('status',['incdefective','defective','defectives','FOR RECEIVING'])->count() > 0){
            $list6 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
                ->whereIn('request_number', $include)
                ->whereIn('stocks.status', ['incdefective','defective','defectives','FOR RECEIVING'])
                ->join('items','items.id','stocks.item_id')
                ->groupBy('prodcode','item','uom','serial','qty','item_id')
                ->orderBy('item', 'ASC')
                ->get();
        }
        else{
            $list6 = array();
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

        if(Stock::whereIn('request_number', $include)->where('status','prep')->count() != 0){
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

        return view('/pages/stockRequest/printStockRequest', compact('list','listX','list0','list1','list2','list3','list4','list5','list6','getList3'));
    }

    public function notify(){
        $stockrequest = Requests::select('requests.id AS req_id', 'requests.created_at AS req_date', 'requests.request_number AS req_num', 'requests.requested_by AS user_id', 'users.name AS req_by', 'users.email AS email', 'users.company AS company', 'request_type.name AS req_type', 'status.status AS status', 'users.name AS req_by', 'request_type.id AS req_type_id', 'status.id AS status_id', 'requests.schedule AS sched', 'prepared_by', 'client_name', 'location', 'contact', 'remarks', 'reference', 'needdate', 'prepdate', 'requests.item_id AS item_id', 'qty', 'assembly_reqnum', 'orderID', 'notify')
            ->whereNotIn('requests.status', ['7','8','10','14','19','26','29'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get()
            ->toArray();
        
        foreach($stockrequest as $key => $value){
            $item_desc = Item::selectRaw('items.item AS item_desc, items.prodcode AS item_code')
                ->where('id', '=', $value['item_id'])
                ->get();

            $today = new DateTime(date("Y-m-d"));
            $deadline = new DateTime($value['needdate']);

            $difference = $today->diff($deadline)->format("%r%a");

            if(($difference > 0 && $difference <= 3) && !$value['notify']){
                Requests::where('request_number', $value['req_num'])->update(['notify' => '3-Days']);
                if($value['req_type'] == 'SALES' || $value['req_type'] == 'MERCHANT'){
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($items as $keys => $values){
                        if($values['warranty'] == '0' || $values['warranty'] == ''){
                            $items[$keys]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $items[$keys]['Warranty_Name'] = Warranty::query()->where('id',$values['warranty'])->first()->Warranty_Name;
                        }
                    }
                }
                else{
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                $attachments = [];
                $files = Requests::where('request_number', $value['req_num'])->first()->reference_upload;
                if($files != NULL){
                    $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                    foreach($files as $file){
                        $file = str_replace('"','',$file);
                        if(file_exists(public_path('uploads/'.$file))){
                            array_push($attachments, public_path('uploads/'.$file));
                        }
                    }
                }
                $subject = '[LAST '.$difference.' DAY/S] STOCK REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')->where('status','ACTIVE')->get('email')->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'Admin',
                    'items' => $items,
                    'files' => array()
                ];
                Mail::to($sendTo)->send(new notifRequest($details, $subject));
                unset($sendTo);
                if(($value['req_type'] == 'SALES' || $value['req_type'] == 'DEMO UNIT') && $value['status'] == 'FOR APPROVAL'){
                    $emails = User::role('approver - sales')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'APPROVER - SALES',
                        'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'orderID' => $value['orderID'],
                        'assembly_reqnum' => $value['assembly_reqnum'],
                        'item_desc' => $item_desc[0]['item_desc'] ?? '',
                        'item_code' => $item_desc[0]['item_code'] ?? '',
                        'qty' => $value['qty'],
                        'role' => 'Approver - Sales',
                        'items' => $items,
                        'files' => $attachments
                    ];
                    Mail::to($sendTo)->send(new notifRequest($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'own user',
                    'items' => $items,
                    'files' => $attachments
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
            if($difference == 0 && ($value['notify'] == '3-Days' || !$value['notify'])){
                Requests::where('request_number', $value['req_num'])->update(['notify' => 'Today']);
                if($value['req_type'] == 'SALES' || $value['req_type'] == 'MERCHANT'){
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($items as $keys => $values){
                        if($values['warranty'] == '0' || $values['warranty'] == ''){
                            $items[$keys]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $items[$keys]['Warranty_Name'] = Warranty::query()->where('id',$values['warranty'])->first()->Warranty_Name;
                        }
                    }
                }
                else{
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                $attachments = [];
                $files = Requests::where('request_number', $value['req_num'])->first()->reference_upload;
                if($files != NULL){
                    $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                    foreach($files as $file){
                        $file = str_replace('"','',$file);
                        if(file_exists(public_path('uploads/'.$file))){
                            array_push($attachments, public_path('uploads/'.$file));
                        }
                    }
                }
                $subject = '[DEADLINE TODAY] STOCK REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')->where('status','ACTIVE')->get('email')->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'Admin',
                    'items' => $items,
                    'files' => array()
                ];
                Mail::to($sendTo)->send(new notifRequest($details, $subject));
                unset($sendTo);
                if(($value['req_type'] == 'SALES' || $value['req_type'] == 'DEMO UNIT') && $value['status'] == 'FOR APPROVAL'){
                    $emails = User::role('approver - sales')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'APPROVER - SALES',
                        'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'orderID' => $value['orderID'],
                        'assembly_reqnum' => $value['assembly_reqnum'],
                        'item_desc' => $item_desc[0]['item_desc'] ?? '',
                        'item_code' => $item_desc[0]['item_code'] ?? '',
                        'qty' => $value['qty'],
                        'role' => 'Approver - Sales',
                        'items' => $items,
                        'files' => $attachments
                    ];
                    Mail::to($sendTo)->send(new notifRequest($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'own user',
                    'items' => $items,
                    'files' => $attachments
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
            if($difference <= -1 && ($value['notify'] == 'Today' || !$value['notify'])){
                Requests::where('request_number', $value['req_num'])->update(['notify' => 'Overdue']);
                if($value['req_type'] == 'SALES' || $value['req_type'] == 'MERCHANT'){
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity','warranty')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get()
                        ->toArray();
                    foreach($items as $keys => $values){
                        if($values['warranty'] == '0' || $values['warranty'] == ''){
                            $items[$keys]['Warranty_Name'] = 'NO WARRANTY';
                        }
                        else{
                            $items[$keys]['Warranty_Name'] = Warranty::query()->where('id',$values['warranty'])->first()->Warranty_Name;
                        }
                    }
                }
                else{
                    $items = StockRequest::query()->select('items.prodcode AS prodcode','items.item AS item','items.UOM AS uom','quantity')
                        ->join('items', 'items.id', 'stock_request.item')
                        ->where('request_number', $value['req_num'])
                        ->orderBy('item', 'ASC')
                        ->get();
                }
                $attachments = [];
                $files = Requests::where('request_number', $value['req_num'])->first()->reference_upload;
                if($files != NULL){
                    $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                    foreach($files as $file){
                        $file = str_replace('"','',$file);
                        if(file_exists(public_path('uploads/'.$file))){
                            array_push($attachments, public_path('uploads/'.$file));
                        }
                    }
                }
                $subject = '[OVERDUE] STOCK REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')->where('status','ACTIVE')->get('email')->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'Admin',
                    'items' => $items,
                    'files' => array()
                ];
                Mail::to($sendTo)->send(new notifRequest($details, $subject));
                unset($sendTo);
                if(($value['req_type'] == 'SALES' || $value['req_type'] == 'DEMO UNIT') && $value['status'] == 'FOR APPROVAL'){
                    $emails = User::role('approver - sales')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'APPROVER - SALES',
                        'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'orderID' => $value['orderID'],
                        'assembly_reqnum' => $value['assembly_reqnum'],
                        'item_desc' => $item_desc[0]['item_desc'] ?? '',
                        'item_code' => $item_desc[0]['item_code'] ?? '',
                        'qty' => $value['qty'],
                        'role' => 'Approver - Sales',
                        'items' => $items,
                        'files' => $attachments
                    ];
                    Mail::to($sendTo)->send(new notifRequest($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'orderID' => $value['orderID'],
                    'assembly_reqnum' => $value['assembly_reqnum'],
                    'item_desc' => $item_desc[0]['item_desc'] ?? '',
                    'item_code' => $item_desc[0]['item_code'] ?? '',
                    'qty' => $value['qty'],
                    'role' => 'own user',
                    'items' => $items,
                    'files' => $attachments
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
        }

        $stocktransfer = RequestTransfer::select('request_transfer.id AS req_id', 'request_transfer.created_at AS req_date', 'request_transfer.request_number AS req_num', 'request_transfer.requested_by AS user_id', 'users.name AS req_by', 'status.status AS status', 'users.email AS email', 'status.id AS status_id', 'request_transfer.schedule AS sched', 'prepared_by', 'needdate', 'prepdate', 'locfrom', 'locto', 'notify')
            ->whereNotIn('request_transfer.status', ['7','8'])
            ->join('users', 'users.id', '=', 'request_transfer.requested_by')
            ->join('status', 'status.id', '=', 'request_transfer.status')
            ->orderBy('request_transfer.created_at', 'DESC')
            ->get()
            ->toArray();

        foreach($stocktransfer as $key => $value){
            $today = new DateTime(date("Y-m-d"));
            $deadline = new DateTime($value['needdate']);

            $difference = $today->diff($deadline)->format("%r%a");

            if(($difference > 0 && $difference <= 3) && !$value['notify']){
                RequestTransfer::where('request_number', $value['req_num'])->update(['notify' => '3-Days']);
                $locfrom = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locfrom'])->first()->location;
                $locto = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locto'])->first()->location;
                $items = StockTransfer::selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, quantity')
                    ->where('request_number', $value['req_num'])
                    ->join('items', 'items.id', 'stock_transfer.item')
                    ->orderBy('item', 'ASC')
                    ->get();
                $subject = '[LAST '.$difference.' DAY/S] STOCK TRANSFER REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')
                    ->where('status','ACTIVE')
                    ->where('email','!=',$value['email'])
                    ->get('email')
                    ->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin',
                    'items' => $items
                ];
                Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                unset($sendTo);
                if($value['status'] == 'FOR APPROVAL'){  
                    $emails = User::role('approver - warehouse')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }      
                    $details = [
                        'name' => 'APPROVER - WAREHOUSE',
                        'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'locfrom' => $locfrom,
                        'locto' => $locto,
                        'status' => $value['status'],
                        'role' => 'Approver - Warehouse',
                        'items' => $items
                    ];
                    Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is '.$difference.'-DAY/S PRIOR its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin / Encoder',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifTransfer($details, $subject));
            }
            if($difference == 0 && ($value['notify'] == '3-Days' || !$value['notify'])){
                RequestTransfer::where('request_number', $value['req_num'])->update(['notify' => 'Today']);
                $locfrom = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locfrom'])->first()->location;
                $locto = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locto'])->first()->location;
                $items = StockTransfer::selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, quantity')
                    ->where('request_number', $value['req_num'])
                    ->join('items', 'items.id', 'stock_transfer.item')
                    ->orderBy('item', 'ASC')
                    ->get();
                $subject = '[DEADLINE TODAY] STOCK TRANSFER REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')
                    ->where('status','ACTIVE')
                    ->where('email','!=',$value['email'])
                    ->get('email')
                    ->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin',
                    'items' => $items
                ];
                Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                unset($sendTo);
                if($value['status'] == 'FOR APPROVAL'){
                    $emails = User::role('approver - warehouse')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'APPROVER - WAREHOUSE',
                        'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'locfrom' => $locfrom,
                        'locto' => $locto,
                        'status' => $value['status'],
                        'role' => 'Approver - Warehouse',
                        'items' => $items
                    ];
                    Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is now DUE TODAY '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin / Encoder',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifTransfer($details, $subject));
            }
            if($difference <= -1 && ($value['notify'] == 'Today' || !$value['notify'])){
                RequestTransfer::where('request_number', $value['req_num'])->update(['notify' => 'Overdue']);
                $locfrom = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locfrom'])->first()->location;
                $locto = Location::selectRaw('locations.location AS location')->where('id', '=', $value['locto'])->first()->location;
                $items = StockTransfer::selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, quantity')
                    ->where('request_number', $value['req_num'])
                    ->join('items', 'items.id', 'stock_transfer.item')
                    ->orderBy('item', 'ASC')
                    ->get();
                $subject = '[OVERDUE] STOCK TRANSFER REQUEST NO. '.$value['req_num'];
                $emails = User::role('admin')
                    ->where('status','ACTIVE')
                    ->where('email','!=',$value['email'])
                    ->get('email')
                    ->toArray();
                foreach($emails as $email){
                    $sendTo[] = $email['email'];
                }
                $details = [
                    'name' => 'ADMIN',
                    'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin',
                    'items' => $items
                ];
                Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                unset($sendTo);
                if($value['status'] == 'FOR APPROVAL'){
                    $emails = User::role('approver - warehouse')
                        ->where('status','ACTIVE')
                        ->where('company',$value['company'])
                        ->get('email')
                        ->toArray();
                    foreach($emails as $email){
                        $sendTo[] = $email['email'];
                    }
                    $details = [
                        'name' => 'APPROVER - WAREHOUSE',
                        'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'locfrom' => $locfrom,
                        'locto' => $locto,
                        'status' => $value['status'],
                        'role' => 'Approver - Warehouse',
                        'items' => $items
                    ];
                    Mail::to($sendTo)->send(new notifTransfer($details, $subject));
                }
                unset($sendTo);
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is already OVERDUE past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'locfrom' => $locfrom,
                    'locto' => $locto,
                    'status' => $value['status'],
                    'role' => 'Admin / Encoder',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifTransfer($details, $subject));
            }
        }
    }
}