<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Stock;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Yajra\Datatables\Datatables;

class StocksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function item()
    {
        $item = Item::select('items.*', 'category')
            ->join('categories', 'category_id', '=', 'categories.id');
        return DataTables::of($item)->make(true);
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
        $categories= Category::select('id','category')->get()->sortBy('category');
        $locations= Location::select('id','location')->whereNotIn('id', ['7','8'])->get()->sortBy('location');
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
        )->get();
        // $dt = DataTables::of($list);
        // $locations = Location::all();
        // foreach ($locations as $location) {
        //     $dt->addColumn($location->location, function (Category $Category)use($location){
        //         Stock::query()
        //             ->where('category_id', $Category->id)
        //             ->where('location_id', $location->id)
        //             ->where('status', 'in')
        //             ->count();
        //     });
        // }
            
        // return $dt->make(true);
        return DataTables::of($list)
            ->addColumn('A1', function (Category $Category){
                $A1 = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 1)
                    ->where('status', 'in')
                    ->count();
                return $A1;
            })
            ->addColumn('A2', function (Category $Category){
                $A2 = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 2)
                    ->where('status', 'in')
                    ->count();
                return $A2;
            })
            ->addColumn('A3', function (Category $Category){
                $A3 = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 3)
                    ->where('status', 'in')
                    ->count();
                return $A3;
            })
            ->addColumn('A4', function (Category $Category){
                $A4 = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 4)
                    ->where('status', 'in')
                    ->count();
                return $A4;
            })
            ->addColumn('Balintawak', function (Category $Category){
                $Balintawak = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 5)
                    ->where('status', 'in')
                    ->count();
                return $Balintawak;
            })
            ->addColumn('Malabon', function (Category $Category){
                $Malabon = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('location_id', 6)
                    ->where('status', 'in')
                    ->count();
                return $Malabon;
            })
            ->addColumn('Total_stocks', function (Category $Category){
                $Total_stocks = Stock::query()
                    ->where('category_id', $Category->id)
                    ->where('status', 'in')
                    ->where('location_id', '!=', '7')
                    ->count();
                return $Total_stocks;
            })->make(true);
    }

    public function item_data(Request $request)
    {
        $list = Item::query()->select(
            'items.id',
            DB::raw
            (
                'items.item as Item'
            )
        )
        ->where('items.category_id', $request->CategoryId)->get();
         return DataTables::of($list)
            ->addColumn('A1', function (Item $Item){
                $A1 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 1)
                    ->where('status', 'in')
                    ->count();
                return $A1;
            })
            ->addColumn('A2', function (Item $Item){
                $A2 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 2)
                    ->where('status', 'in')
                    ->count();
                return $A2;
            })
            ->addColumn('A3', function (Item $Item){
                $A3 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 3)
                    ->where('status', 'in')
                    ->count();
                return $A3;
            })
            ->addColumn('A4', function (Item $Item){
                $A4 = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 4)
                    ->where('status', 'in')
                    ->count();
                return $A4;
            })
            ->addColumn('Balintawak', function (Item $Item){
                $Balintawak = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 5)
                    ->where('status', 'in')
                    ->count();
                return $Balintawak;
            })
            ->addColumn('Malabon', function (Item $Item){
                $Malabon = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('location_id', 6)
                    ->where('status', 'in')
                    ->count();
                return $Malabon;
            })
            ->addColumn('Total_stocks', function (Item $Item){
                $Total_stocks = Stock::query()
                    ->where('item_id', $Item->id)
                    ->where('status', 'in')
                    ->where('location_id', '!=','7')
                    ->count();
                return $Total_stocks;
            })
            

         ->make(true);
    }

    public function stock_data(Request $request)
    {
        $stock = Stock::select('location_id','serial','item_id','stocks.id', 'item')
            ->where('item_id', $request->id)
            ->join('items', 'items.id', 'item_id')
            ->get();
        return DataTables::of($stock)->make(true);
    }

    public function addStockitem(Request $request){
        $list = Item::query()->select('id','item')
            ->where('category_id',$request->category_id)
            ->get();
        return response()->json($list);
    }

    public function getUOM(Request $request){
        $uom = Item::query()->select('UOM as uom')
            ->where('id',$request->id)
            ->first();
        return response()->json($uom);
    }

    public function items(Request $request){ //chef //$request = orders
        $list = Item::query()->select('item_id','item')
            ->join('stocks', 'stocks.item_id', 'items.id')
            ->where('stocks.status', 'in') //order
            ->where('stocks.category_id',$request->category_id) //order
            ->groupBy('items.id')
            ->get();
        return response()->json($list); //finish product send to waiter
    }

    public function locations(Request $request){
        $location = Stock::query()
            ->select('location_id','location')
            ->join('locations','locations.id','location_id')
            ->where('status', 'in')
            ->where('item_id', $request->item_id)
            ->groupBy('location_id')
            ->get();
        return response()->json($location);
    }

    public function stocksAvailable(Request $request){
        $count = Stock::query()
                ->select('category_id','items_id','location_id','status')
                ->where('category_id',$request->category_id)
                ->where('item_id',$request->item_id)
                ->where('location_id',$request->location_id)
                ->where('status','in')
                ->count();
        return response()->json($count);
    }

    public function itemstrans(Request $request){
        $list = Item::query()
                ->join('stocks', 'item_id', '=', 'items.id')
                ->select('items.id','item','items.category_id','stocks.location_id','stocks.status')
                ->where('items.category_id',$request->categories)
                ->where('items.id',$request->items)
                ->where('stocks.location_id',$request->locationfrom)
                ->where('stocks.status','in')
                ->get();
        return $list;
    }
     
    public function store(Request $request)
    {
        if ($request->serial) {
            $stocks = new Stock;
            $stocks->item_id = $request->item;
            $stocks->category_id = $request->category;
            $stocks->user_id =auth()->user()->id;
            $stocks->location_id =$request->location;
            $stocks->status = 'in';
            $stocks->serial = $request->serial;
            $save= $stocks->save();             
        }else if($request->qty > 0){
            for ($i=0; $i < $request->qty; $i++) { 
                $stocks = new Stock;
                $stocks->item_id = $request->item;
                $stocks->category_id = $request->category;
                $stocks->user_id =auth()->user()->id;
                $stocks->location_id =$request->location;
                $stocks->status = 'in';
                $stocks->serial = 'N/A';
                $save= $stocks->save(); 
            }
        }
        return response()->json($stocks);
    }

    public function update(Request $request)
    { 
        for ($i=0; $i < $request->qty ; $i++) { 
            $stocks = Stock::where('item_id','=',$request->item)
                ->where('location_id',$request->locationfrom)
                ->where('status','in')
                ->first();                
            $stocks->location_id = $request->locationto;
            $stocks->save();
        }
        
            return response()->json($stocks);

    }


}
