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
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
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
        $categories= Category::select('id','category')->get()->sortBy('category');
        $locations= Location::select('id','location')->whereNotIn('id', ['7','8','9','10'])->get()->sortBy('location');
        $items= Item::select('id','item')->get()->sortBy('item');
        $list = DB::table('stocks')->get();
        return view('/pages/stocks', compact('list','categories','locations','items'));
    }

    public function GetLocation(){
        $location = Location::all();
        return response()->json($location);
    }

    public function category_data(){
        $list = Category::query()->select('categories.id',
            DB::raw
            (
                'categories.category as Category'
            )
        )->orderBy('Category', 'ASC')->get();
        return DataTables::of($list)
            ->addColumn('Defective', function(Category $Category){
                $Defective = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->whereIn('status', ['defectives', 'FOR RECEIVING'])
                    ->count();
                return $Defective;
            })
            ->addColumn('Demo', function(Category $Category){
                $Demo = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('status', 'demo')
                    ->count();
                return $Demo;
            })
            ->addColumn('Assembly', function(Category $Category){
                $Assembly = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('status', 'assembly')
                    ->count();
                return $Assembly;
            })
            ->addColumn('A1', function(Category $Category){
                $A1 = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 1)
                    ->where('status', 'in')
                    ->count();
                return $A1;
            })
            ->addColumn('A2', function(Category $Category){
                $A2 = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 2)
                    ->where('status', 'in')
                    ->count();
                return $A2;
            })
            ->addColumn('A3', function(Category $Category){
                $A3 = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 3)
                    ->where('status', 'in')
                    ->count();
                return $A3;
            })
            ->addColumn('A4', function(Category $Category){
                $A4 = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 4)
                    ->where('status', 'in')
                    ->count();
                return $A4;
            })
            ->addColumn('Balintawak', function(Category $Category){
                $Balintawak = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 5)
                    ->where('status', 'in')
                    ->count();
                return $Balintawak;
            })
            ->addColumn('Malabon', function(Category $Category){
                $Malabon = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->where('location_id', 6)
                    ->where('status', 'in')
                    ->count();
                return $Malabon;
            })
            ->addColumn('Total_stocks', function(Category $Category){
                $Total_stocks = Stock::query()
                    ->join('items', 'items.id', 'stocks.item_id')
                    ->where('items.category_id', $Category->id)
                    ->whereIn('status', ['in','defectives','FOR RECEIVING','demo','assembly'])
                    ->count();
                return $Total_stocks;
            })->make(true);
    }

    public function itemserial_data(Request $request){
        $stock = Stock::query()
            ->select('stocks.id AS stock_id', 'category', 'item', 'name', 'location', 'serial', 'rack', 'row', 'stocks.status AS status', 'stocks.created_at AS addDate', 'stocks.updated_at AS modDate')
            ->join('categories', 'categories.id', 'category_id')
            ->join('items', 'items.id', 'item_id')
            ->join('locations', 'locations.id', 'location_id')
            ->join('users', 'users.id', 'user_id')
            ->where('item_id', $request->ItemId)
            ->whereIn('stocks.status', ['in','defectives','FOR RECEIVING','demo','assembly'])
            ->orderBy('modDate', 'DESC')
            ->get();
        
        return DataTables::of($stock)->make(true);
    }

    public function item_data(Request $request){
        $list = Item::query()->select(
            'items.id',
            DB::raw
            (
                'items.item as Item, items.prodcode as ProdCode'
            )
        )
        ->where('items.category_id', $request->CategoryId)
        ->orderBy('Item', 'ASC')->get();
         return DataTables::of($list)
            ->addColumn('Defective', function(Item $Item){
                $Defective = Stock::query()
                    ->where('item_id', $Item->id)
                    ->whereIn('status', ['defectives', 'FOR RECEIVING'])
                    ->count();
                return $Defective;
            })
            ->addColumn('Demo', function(Item $Item){
                $Demo = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('status', 'demo')
                    ->count();
                return $Demo;
            })
            ->addColumn('Assembly', function(Item $Item){
                $Assembly = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('status', 'assembly')
                    ->count();
                return $Assembly;
            })
            ->addColumn('A1', function(Item $Item){
                $A1 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 1)
                    ->where('status', 'in')
                    ->count();
                return $A1;
            })
            ->addColumn('A2', function(Item $Item){
                $A2 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 2)
                    ->where('status', 'in')
                    ->count();
                return $A2;
            })
            ->addColumn('A3', function(Item $Item){
                $A3 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 3)
                    ->where('status', 'in')
                    ->count();
                return $A3;
            })
            ->addColumn('A4', function(Item $Item){
                $A4 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 4)
                    ->where('status', 'in')
                    ->count();
                return $A4;
            })
            ->addColumn('Balintawak', function(Item $Item){
                $Balintawak = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 5)
                    ->where('status', 'in')
                    ->count();
                return $Balintawak;
            })
            ->addColumn('Malabon', function(Item $Item){
                $Malabon = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 6)
                    ->where('status', 'in')
                    ->count();
                return $Malabon;
            })
            ->addColumn('Total_stocks', function(Item $Item){
                $Total_stocks = Stock::query()
                    ->where('item_id', $Item->id)
                    ->whereIn('status', ['in','defectives','FOR RECEIVING','demo','assembly'])
                    ->count();
                return $Total_stocks;
            })
        ->make(true);
    }

    public function stock_data(Request $request){
        $stock = Stock::select('location_id','serial','item_id','stocks.id', 'item')
            ->where('item_id', $request->id)
            ->join('items', 'items.id', 'item_id')
            ->get();
        return DataTables::of($stock)->make(true);
    }

    public function addStockitem(Request $request){
        $list = Item::query()->select('id','item')
            ->where('category_id',$request->category_id)
            ->orderBy('item','ASC')->get();
        return response()->json($list);
    }

    public function getUOM(Request $request){
        $data = Item::selectRaw('UOM, prodcode')
            ->where('id',$request->id)
            ->get();
        return response($data);
    }
     
    public function store(Request $request){
        if($request->serial){
            do{
                $stocks = new Stock;
                $stocks->item_id = $request->item;
                $stocks->user_id =auth()->user()->id;
                $stocks->location_id =$request->location;
                $stocks->status = 'in';
                $stocks->serial = $request->serial;
                $stocks->rack = $request->rack;
                $stocks->row = $request->row;
                $sql = $stocks->save();
            }
            while(!$sql);

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ADDED STOCK: User successfully added 1-$request->uom/s Stock of '$request->item_name' to $request->location_name with Serial '$request->serial'.";
            $userlogs->save();
        }
        else if($request->qty > 0){
            for($i=0; $i < $request->qty; $i++){
                do{
                    $stocks = new Stock;
                    $stocks->item_id = $request->item;
                    $stocks->user_id =auth()->user()->id;
                    $stocks->location_id =$request->location;
                    $stocks->status = 'in';
                    $stocks->serial = 'N/A';
                    $stocks->rack = $request->rack;
                    $stocks->row = $request->row;
                    $sql = $stocks->save();
                }
                while(!$sql);
            }
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ADDED STOCK: User successfully added $request->qty-$request->uom/s Stock of '$request->item_name' to $request->location_name.";
            $userlogs->save();
        }
        return response()->json($stocks);
    }

    public function update(Request $request){ 
        for($i=0; $i < $request->qty ; $i++){ 
            $stocks = Stock::where('item_id','=',$request->item)
                ->where('location_id',$request->locationfrom)
                ->where('status','in')
                ->first();                
            $stocks->location_id = $request->locationto;
            $stocks->save();
        }
        return response()->json($stocks);
    }

    public function import(Request $request){
        $file = $request->file('xlsx');
        $import = new StocksImport;
        $data = Excel::toArray($import, $file);
        $failed_rows = [];
        foreach($data[0] as $key => $value){
            $row_num = 2;
            $add = new Stock;
            $add->user_id = auth()->user()->id;
            $add->item_id = $value['item'];
            $add->location_id = $value['location'];
            $add->rack = $value['rack'];
            $add->row = $value['row'];
            $add->qty = $value['qty'];
            $add->serial = $value['serial'];
            $add->status = 'in';
            $sql = $add->save();
            if(!$sql){
                array_push($failed_rows, $row_num);
            }
            else{
                // $userlogs = new UserLogs;
                // $userlogs->user_id = auth()->user()->id;
                // $userlogs->activity = "ADDED STOCK: User successfully added $qty-$uom/s Stock of '$item_name' to $location_name.";
                // $userlogs->save();
            }
        }
        if(count($failed_rows) == 0){
            return redirect()->to('/stocks?import=success_without_errors');
        }
        else{
            return redirect()->to('/stocks?import=success_with_errors');
        }
    }
}
