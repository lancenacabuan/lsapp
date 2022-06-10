<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
use App\Models\RequestTransfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function merchant(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales') || auth()->user()->hasanyRole('accounting')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/assembly');
        }
        if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')) //---ROLES---//
        {
            return redirect('/');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');
        $items = Item::select('id','item')->get()->sortBy('item');
        $warranty = Warranty::select('id','Warranty_Name')->get()->sortBy('Warranty_Name');
        
        return view('/pages/merchant', compact('categories','items','warranty'));
    }

    public function items(Request $request){       
        $list = Item::query()->select('items.id','items.item')
            ->where('items.category_id',$request->category_id)
            ->groupBy('items.id')
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }

    public function uom(Request $request){
        $data = Item::selectRaw('UOM as uom, prodcode')
            ->where('id',$request->item_id)
            ->get();
        
        return response($data);
    }

    public function warranty(Request $request){       
        $list = Warranty::query()->select()
            ->where('id',$request->id)
            ->get();
        
        return response()->json($list);
    }

    public function merchant_data(){
        $list = Requests::selectRaw('DATE_FORMAT(requests.created_at, "%Y-%m-%d") AS reqdate, requests.id AS req_id, requests.created_at AS date, requests.request_number AS req_num, requests.requested_by AS user_id, request_type.name AS req_type, status.status AS status, users.name AS req_by, request_type.id AS req_type_id, status.id AS status_id, requests.schedule AS sched, prepared_by, needdate, reference_upload, orderID')
            ->where('requests.requested_by', auth()->user()->id)
            ->whereIn('requests.request_type', ['6'])
            ->whereNotIn('requests.status', ['7','8'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('reqdate', 'ASC')
            ->orderBy('requests.needdate', 'ASC')
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
        ->make(true);
    }

    public function saveReqNum(Request $request){
        $orderID = Requests::query()->select()
            ->where('orderID', '!=', 'N/A')
            ->whereRaw('UPPER(orderID) = ?', strtoupper($request->orderID))
            ->count();
        if($orderID > 0){
            return response('duplicate');
        }

        do{
            $requests = new Requests;
            $requests->request_number = $request->request_number;
            $requests->requested_by = auth()->user()->id;
            $requests->needdate = $request->needdate;
            $requests->orderID = $request->orderID;
            $requests->request_type = '6';
            $requests->status = '1';
            $sql = $requests->save();
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
        for($i=0; $i < count($reference_upload); $i++){
            $request->reference_upload[$i]->move(public_path('/uploads'), $reference_upload[$i]);
        }

        Requests::where('request_number', $request->reqnum)
            ->update(['reference_upload' => $reference_upload]);

        if($request->action == 'SUBMIT'){
            return redirect()->to('/merchant?submit=success');
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
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "NEW MERCHANT STOCK REQUEST: User successfully submitted Merchant Stock Request No. $request->request_number.";
        $userlogs->save();
        
        return response('true');
    }
}