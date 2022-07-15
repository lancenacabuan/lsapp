<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Requests;
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
        
        $include = Requests::query()->select('request_number')
            ->where('assembly_reqnum', $request->request_number)
            ->get();
        
        $include = str_replace("{\"request_number\":","",$include);
        $include = str_replace("}","",$include);
        $include = json_decode($include);
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
}