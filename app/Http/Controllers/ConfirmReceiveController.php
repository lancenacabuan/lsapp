<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class ConfirmReceiveController extends Controller
{
    public function confirm(Request $request){
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
        
        $include = Requests::query()->select('request_number')
            ->where('assembly_reqnum', $request->request_number)
            ->get();
        
        $include = str_replace("{\"request_number\":","",$include);
        $include = str_replace("}","",$include);
        $include = json_decode($include);
        $include[] = $request->request_number;

        $list3 = Stock::query()->selectRaw('items.prodcode AS prodcode, items.item AS item, items.UOM AS uom, stocks.serial AS serial, SUM(stocks.qty) AS qty, stocks.item_id AS item_id')
            ->whereIn('request_number', $include)
            ->whereIn('stocks.status', ['prep','assembly','out','asset','demo','assembled'])
            ->join('items','items.id','stocks.item_id')
            ->groupBy('prodcode','item','uom','serial','qty','item_id')
            ->orderBy('item', 'ASC')
            ->get();
        
        if(!$list3){
            return redirect()->to('/');
        }

        return view('/pages/stockRequest/confirmStockRequest', compact('list','list1','list2','list3'));
    }
}