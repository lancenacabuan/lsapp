<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StocksImport;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class StocksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stocks(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales') || auth()->user()->hasanyRole('accounting')) //---ROLES---//
        {
            return redirect('/');
        }
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
        $locations = Location::select('id','location')->whereNotIn('id', ['7','8','9','10'])->get()->sortBy('location');

        return view('/pages/stocks', compact('categories','locations'));
    }

    public function reload(){
        $data_update = Stock::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function category_data(){
        $list = Category::query()->select('categories.id',
                DB::raw
                ("
                    categories.category as Category,
                    SUM(CASE WHEN stocks.status = 'defectives' OR stocks.status = 'FOR RECEIVING' THEN 1 ELSE 0 END) as Defective,
                    SUM(CASE WHEN stocks.status = 'demo' THEN 1 ELSE 0 END) as Demo,
                    SUM(CASE WHEN stocks.status = 'assembly' THEN 1 ELSE 0 END) as Assembly,
                    SUM(CASE WHEN stocks.status = 'asset' THEN 1 ELSE 0 END) as Asset,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '1' THEN 1 ELSE 0 END) as A1,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '2' THEN 1 ELSE 0 END) as A2,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '3' THEN 1 ELSE 0 END) as A3,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '4' THEN 1 ELSE 0 END) as A4,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '5' THEN 1 ELSE 0 END) as Balintawak,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '6' THEN 1 ELSE 0 END) as Malabon,
                    SUM(CASE WHEN stocks.status = 'in' OR stocks.status = 'defectives' OR stocks.status = 'FOR RECEIVING' OR stocks.status = 'demo' OR stocks.status = 'assembly' OR stocks.status = 'asset' THEN 1 ELSE 0 END) as Total_stocks
                ")
            )
            ->join('items', 'items.category_id', 'categories.id')
            ->join('stocks', 'stocks.item_id', 'items.id')
            ->groupBy('categories.id','Category')
            ->orderBy('Category', 'ASC')
            ->orderBy('categories.id', 'ASC')
            ->get()
            ->toArray();
        
        foreach($list as $key => $value){
            $items = Item::query()->select('items.id',
                DB::raw
                ("
                    minimum as Minimum_stocks,
                    SUM(CASE WHEN stocks.status = 'in' OR stocks.status = 'defectives' OR stocks.status = 'FOR RECEIVING' OR stocks.status = 'demo' OR stocks.status = 'assembly' OR stocks.status = 'asset' THEN 1 ELSE 0 END) as Total_stocks
                ")
            )
            ->where('items.category_id', $value['id'])
            ->join('stocks', 'stocks.item_id', 'items.id')
            ->groupBy('items.id')
            ->orderBy('Item', 'ASC')
            ->get()
            ->toArray();
            foreach($items as $itemkey => $itemvalue){
                if($itemvalue['Total_stocks'] <= $itemvalue['Minimum_stocks']){
                    $list[$key]['RowColor'] = 'RED';
                }
            }
        }

        return DataTables::of($list)->make(true);
    }

    public function item_data(Request $request){
        $list = Item::query()->select('items.id',
                DB::raw
                ("
                    items.item as Item, items.prodcode as ProdCode, serialize, minimum as Minimum_stocks,
                    SUM(CASE WHEN stocks.status = 'defectives' OR stocks.status = 'FOR RECEIVING' THEN 1 ELSE 0 END) as Defective,
                    SUM(CASE WHEN stocks.status = 'demo' THEN 1 ELSE 0 END) as Demo,
                    SUM(CASE WHEN stocks.status = 'assembly' THEN 1 ELSE 0 END) as Assembly,
                    SUM(CASE WHEN stocks.status = 'asset' THEN 1 ELSE 0 END) as Asset,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '1' THEN 1 ELSE 0 END) as A1,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '2' THEN 1 ELSE 0 END) as A2,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '3' THEN 1 ELSE 0 END) as A3,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '4' THEN 1 ELSE 0 END) as A4,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '5' THEN 1 ELSE 0 END) as Balintawak,
                    SUM(CASE WHEN stocks.status = 'in' AND stocks.location_id = '6' THEN 1 ELSE 0 END) as Malabon,
                    SUM(CASE WHEN stocks.status = 'in' OR stocks.status = 'defectives' OR stocks.status = 'FOR RECEIVING' OR stocks.status = 'demo' OR stocks.status = 'assembly' OR stocks.status = 'asset' THEN 1 ELSE 0 END) as Total_stocks
                ")
            )
            ->where('items.category_id', $request->CategoryId)
            ->join('stocks', 'stocks.item_id', 'items.id')
            ->groupBy('items.id','Item','ProdCode','serialize')
            ->orderBy('Item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get()
            ->toArray();
        
        foreach($list as $key => $value){
            if($value['Total_stocks'] <= $value['Minimum_stocks']){
                $list[$key]['RowColor'] = 'RED';
            }
            else{
                $list[$key]['RowColor'] = 'BLACK';
            }
        }

        return DataTables::of($list)->make(true);
    }

    public function itemserial_data(Request $request){
        $UOM = Item::select()
            ->where('id', $request->ItemId)
            ->first()
            ->UOM;

        if($UOM == 'Unit'){
            $stock = Stock::query()
                ->select('stocks.id AS stock_id', 'category', 'item', 'serialize', 'stocks.qty', 'UOM', 'name', 'rack', 'row', 'stocks.status AS status', 'stocks.created_at AS addDate', 'stocks.updated_at AS modDate')
                ->selectRaw('DATE_FORMAT(stocks.created_at, "%b. %d, %Y, %h:%i %p") AS addDatetime, DATE_FORMAT(stocks.updated_at, "%b. %d, %Y, %h:%i %p") AS modDatetime, UPPER(serial) AS serial')
                ->selectRaw('
                    (CASE
                        WHEN stocks.status = "defectives" THEN "DEFECTIVE"
                        WHEN stocks.status = "FOR RECEIVING" THEN "DEFECTIVE (RETURNED)"
                        WHEN stocks.status = "demo" THEN "DEMO"
                        WHEN stocks.status = "assembly" THEN "ASSEMBLY"
                        WHEN stocks.status = "asset" THEN "FIXED ASSET"
                        ELSE location END
                    )AS location
                ')
                ->where('item_id', $request->ItemId)
                ->whereIn('stocks.status', ['in','defectives','FOR RECEIVING','demo','assembly','asset'])
                ->join('items', 'items.id', 'item_id')
                ->join('categories', 'categories.id', 'category_id')
                ->join('locations', 'locations.id', 'location_id')
                ->join('users', 'users.id', 'user_id')
                ->orderBy('modDate', 'DESC')
                ->orderBy('addDate', 'ASC')
                ->orderBy('name', 'ASC')
                ->get();
            
            return DataTables::of($stock)->make(true);
        }
        else{
            $stock = Stock::query()
                ->select('category', 'item', 'serialize', DB::raw('SUM(stocks.qty) AS qty'), 'UOM', 'name', 'rack', 'row', 'stocks.status AS status', 'stocks.created_at AS addDate', 'stocks.updated_at AS modDate')
                ->selectRaw('DATE_FORMAT(stocks.created_at, "%b. %d, %Y, %h:%i %p") AS addDatetime, DATE_FORMAT(stocks.updated_at, "%b. %d, %Y, %h:%i %p") AS modDatetime, UPPER(serial) AS serial')
                ->selectRaw('
                    (CASE
                        WHEN stocks.status = "defectives" THEN "DEFECTIVE"
                        WHEN stocks.status = "FOR RECEIVING" THEN "DEFECTIVE (RETURNED)"
                        WHEN stocks.status = "demo" THEN "DEMO"
                        WHEN stocks.status = "assembly" THEN "ASSEMBLY"
                        WHEN stocks.status = "asset" THEN "FIXED ASSET"
                        ELSE location END
                    )AS location
                ')
                ->where('item_id', $request->ItemId)
                ->whereIn('stocks.status', ['in','defectives','FOR RECEIVING','demo','assembly','asset'])
                ->join('items', 'items.id', 'item_id')
                ->join('categories', 'categories.id', 'category_id')
                ->join('locations', 'locations.id', 'location_id')
                ->join('users', 'users.id', 'user_id')
                ->groupBy('category','item','serialize','qty','UOM','name','location','serial','rack','row','status','addDate','modDate')
                ->orderBy('modDate', 'DESC')
                ->orderBy('addDate', 'ASC')
                ->orderBy('name', 'ASC')
                ->get();
            
            return DataTables::of($stock)->make(true);
        }
    }

    public function serial_data(Request $request){
        $count = Stock::select()
            ->where('serial', 'like', '%'.$request->serial.'%')
            ->count();
        if($count > 0){
            $stock = Stock::query()
                ->select('stocks.id AS stock_id', 'category', 'item', 'serialize', 'prodcode', 'stocks.qty', 'UOM', 'name', 'rack', 'row', 'stocks.status AS status', 'stocks.created_at AS addDate', 'stocks.updated_at AS modDate')
                ->selectRaw('DATE_FORMAT(stocks.created_at, "%b. %d, %Y, %h:%i %p") AS addDatetime, DATE_FORMAT(stocks.updated_at, "%b. %d, %Y, %h:%i %p") AS modDatetime, UPPER(serial) AS serial')
                ->selectRaw('
                    (CASE
                        WHEN stocks.status = "defectives" THEN "DEFECTIVE"
                        WHEN stocks.status = "FOR RECEIVING" THEN "DEFECTIVE (RETURNED)"
                        WHEN stocks.status = "demo" THEN "DEMO"
                        WHEN stocks.status = "assembly" THEN "ASSEMBLY"
                        WHEN stocks.status = "asset" THEN "FIXED ASSET"
                        ELSE location END
                    )AS location
                ')
                ->where('serial', 'like', '%'.$request->serial.'%')
                ->where('serial', '!=', 'N/A')
                ->where('UOM', 'Unit')
                ->whereIn('stocks.status', ['in','defectives','FOR RECEIVING','demo','assembly','asset'])
                ->join('items', 'items.id', 'item_id')
                ->join('categories', 'categories.id', 'category_id')
                ->join('locations', 'locations.id', 'location_id')
                ->join('users', 'users.id', 'user_id')
                ->orderBy('modDate', 'DESC')
                ->orderBy('addDate', 'ASC')
                ->orderBy('name', 'ASC')
                ->orderBy('item', 'ASC')
                ->get();
            
            return DataTables::of($stock)->make(true);
        }
        else{
            return 0;
        }
    }

    public function minstocks_data(Request $request){
        $stocks = Item::query()->select('items.id', 'items.item as Item', 'items.prodcode as ProdCode', 'items.UOM as uom', 'categories.category as Category', 'items.minimum as Minimum_stocks', 
                DB::raw("SUM(CASE 
                    WHEN stocks.status = 'in' THEN 1
                    WHEN stocks.status = 'defectives' THEN 1
                    WHEN stocks.status = 'FOR RECEIVING' THEN 1
                    WHEN stocks.status = 'demo' THEN 1
                    WHEN stocks.status = 'assembly' THEN 1
                    WHEN stocks.status = 'asset' THEN 1
                    ELSE 0 END
                ) as Current_stocks"))
            ->join('categories', 'categories.id', 'items.category_id')
            ->join('stocks', 'stocks.item_id', 'items.id')
            ->groupBy('items.id','Item','ProdCode','uom','Category')
            ->orderBy('Category', 'ASC')
            ->orderBy('Item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        
        foreach($stocks as $stock){
            if($stock->Current_stocks <= $stock->Minimum_stocks){
                $list[]=$stock;
            }
        }

        return DataTables::of($list)->make(true);
    }

    public function getItems(Request $request){
        $list = Item::query()->select('id','item')
            ->where('category_id',$request->category_id)
            ->orderBy('item','ASC')
            ->get();
        return response()->json($list);
    }

    public function getUOM(Request $request){
        $data = Item::selectRaw('UOM, prodcode, serialize')
            ->where('id',$request->id)
            ->get();
        return response($data);
    }
     
    public function save(Request $request){
        if($request->uom == 'Unit' && $request->qty == '1'){
            $serials = Stock::query()->select()
                ->where('serial', '!=', 'N/A')
                ->whereRaw('UPPER(serial) = ?', strtoupper($request->serial))
                ->count();
            if($serials > 0){
                return response('duplicate');
            }

            do{
                $stocks = new Stock;
                $stocks->item_id = $request->item;
                $stocks->user_id = auth()->user()->id;
                $stocks->location_id = $request->location;
                $stocks->status = 'in';
                $stocks->qty = '1';
                $stocks->serial = $request->serial;
                $stocks->rack = $request->rack;
                $stocks->row = $request->row;
                $sql = $stocks->save();
            }
            while(!$sql);

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "ADDED STOCK: User successfully added 1-$request->uom/s Stock of '$request->item_name' to $request->location_name with Serial '$request->serial'.";
                $userlogs->save();
            }
        }
        else{
            for($i=0; $i < $request->qty; $i++){
                do{
                    $stocks = new Stock;
                    $stocks->item_id = $request->item;
                    $stocks->user_id = auth()->user()->id;
                    $stocks->location_id = $request->location;
                    $stocks->status = 'in';
                    $stocks->qty = '1';
                    $stocks->serial = 'N/A';
                    $stocks->rack = $request->rack;
                    $stocks->row = $request->row;
                    $sql = $stocks->save();
                }
                while(!$sql);
            }
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ADDED STOCK: User successfully added $request->qty-$request->uom/s Stock of '$request->item_name' to $request->location_name.";
            $userlogs->save();
        }
        return response($result);
    }

    public function add(Request $request){
        if(Item::where('id', $request->item_id)->count() > 0){
            $return = Item::where('id', $request->item_id)->first()->category_id;
        }
        else{
            $return = 'false';
        }
        return $return;
    }

    public function import(Request $request){
        $file = $request->file('xlsx');
        $import = new StocksImport;
        $data = Excel::toArray($import, $file);
        if(count($data[0]) == 0){
            return redirect()->to('/stocks?import=failed');
        }
        $failed_rows = [];
        $row_num = 2;
        foreach($data[0] as $key => $value){
            $item = Item::selectRaw('id, UOM')
                ->where('item', $value['item_description'])
                ->get();
            $location = Location::select()
                ->where('location', $value['location'])
                ->first();
            $serials = Stock::query()->select()
                ->where('serial', '!=', 'N/A')
                ->where('serial', strtoupper($value['serial']))
                ->count();
            
            if(!$value['item_description'] || !$value['location'] || !$value['qty']){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Fill Required Fields!]');
            }
            else if(!$item){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Item!]');
            }
            else if(!$location){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Location!]');
            }
            else if($value['qty'] < 1){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Quantity!]');
            }
            else if(strlen($value['serial']) < 5){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Serial!]');
            }
            else if(ctype_alnum($value['serial']) == false){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Serial!]');
            }
            else if($serials > 0){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Duplicate Serial!]');
            }
            else if($item[0]['UOM'] != 'Unit' && $value['serial']){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Serial not allowed!]');
            }
            else if(($item[0]['UOM'] == 'Unit' && $value['qty'] > 1) || ($value['serial'] && $value['qty'] > 1)){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Must be equal to 1-Qty!]');
            }
            else{
                $item_id = $item[0]['id'];
                $item_name = $value['item_description'];
                $qty = $value['qty'];
                $uom = $item[0]['UOM'];
                $location_id = $location->id;
                $location_name = $value['location'];
                if(!$value['serial'] || strtoupper($value['serial'] == 'N/A')){
                    $serial = 'N/A';
                }
                else{
                    $serial = strtoupper($value['serial']);
                }
                if(!$value['rack'] || strtoupper($value['rack'] == 'N/A')){
                    $rack = 'N/A';
                }
                else{
                    $rack = strtoupper($value['rack']);
                }
                if(!$value['row'] || strtoupper($value['row'] == 'N/A')){
                    $row = 'N/A';
                }
                else{
                    $row = strtoupper($value['row']);
                }
                if($value['qty'] == 1){
                    $add = new Stock;
                    $add->user_id = auth()->user()->id;
                    $add->item_id = $item_id;
                    $add->location_id = $location_id;
                    $add->rack = $rack;
                    $add->row = $row;
                    $add->qty = '1';
                    $add->serial = $serial;
                    $add->status = 'in';
                    $sql = $add->save();
                }
                else{
                    for($i = 0; $i < $value['qty']; $i++){
                        $add = new Stock;
                        $add->user_id = auth()->user()->id;
                        $add->item_id = $item_id;
                        $add->location_id = $location_id;
                        $add->rack = $rack;
                        $add->row = $row;
                        $add->qty = '1';
                        $add->serial = $serial;
                        $add->status = 'in';
                        $sql = $add->save();
                    }
                }
                if(!$sql){
                    array_push($failed_rows, '[Row: '.$row_num.', Error: Save Failed!]');
                }
                else{
                    if($serial == 'N/A'){
                        $userlogs = new UserLogs;
                        $userlogs->user_id = auth()->user()->id;
                        $userlogs->activity = "ADDED STOCK: User successfully added $qty-$uom/s Stock of '$item_name' to $location_name.";
                        $userlogs->save();
                    }
                    else{
                        $userlogs = new UserLogs;
                        $userlogs->user_id = auth()->user()->id;
                        $userlogs->activity = "ADDED STOCK: User successfully added $qty-$uom/s Stock of '$item_name' to $location_name with Serial '$serial'.";
                        $userlogs->save();
                    }
                }
            }
            $row_num++;
        }
        if(count($failed_rows) == count($data[0])){
            return redirect()->to('/stocks?import=failed');
        }
        else if(count($failed_rows) == 0){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "STOCKS FILE IMPORT [NO ERRORS]: User successfully imported file data into Stocks without any errors.";
            $userlogs->save();

            return redirect()->to('/stocks?import=success_without_errors');
        }
        else{
            $errors = implode(', ', $failed_rows);
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "STOCKS FILE IMPORT [WITH ERRORS]: User successfully imported file data into Stocks with the following errors: $errors.";
            $userlogs->save();

            return redirect()->to('/stocks?import=success_with_errors');
        }
    }
}