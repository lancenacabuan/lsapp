<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;
use App\Models\Stock;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Warranty;
use App\Models\User;
use App\Models\UserLogs;
use App\Mail\requestLocation;
use App\Mail\requestStatusChange;
use Yajra\Datatables\Datatables;

class FileMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function maintenance(Request $request){
        if(!auth()->user()->hasanyRole('admin')) //---ROLES---//
        {
            return redirect('/');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');

        return view('pages/maintenance', compact('categories'));   
    }

    public function fm_items(){
        $list = Item::select('items.id', 'items.item', 'items.prodcode', 'categories.category', 'items.category_id', 'items.UOM AS uom', 'items.serialize', 'items.minimum')
            ->where('items.assemble', 'NO')
            ->join('categories', 'categories.id', 'category_id')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function asm_items(){
        $list = Item::select('items.id', 'items.item', 'items.prodcode', 'categories.category', 'items.category_id', 'items.UOM AS uom', 'items.minimum' )
            ->where('items.assemble', 'YES')
            ->join('categories', 'categories.id', 'category_id')
            ->orderBy('item', 'ASC')
            ->orderBy('items.id', 'ASC')
            ->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_categories(){
        $list = Category::select('id', 'category')->orderBy('category', 'ASC')->orderBy('id', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_locations(){
        $list = Location::select('id AS location_id', 'location', 'status')->orderBy('location', 'ASC')->orderBy('id', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_items_reload(){
        $data_update = Item::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function asm_items_reload(){
        $data_update = Item::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_categories_reload(){
        $data_update = Category::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_locations_reload(){
        $data_update = Location::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function fm_warranty_reload(){
        $data_update = Warranty::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function import(Request $request){
        $file = $request->file('xlsx');
        $import = new ItemsImport;
        $data = Excel::toArray($import, $file);
        if(count($data[0]) == 0){
            return redirect()->to('/maintenance?import=failed');
        }
        $failed_rows = [];
        $row_num = 2;
        foreach($data[0] as $key => $value){
            $category = Category::select('id','category')
                ->where('category', $value['category_name'])
                ->get();
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?', strtolower($value['item_description']))
                ->count();
            $itemcode = Item::query()->select()
                ->where('prodcode', strtoupper($value['item_code']))
                ->count();
            if(!$value['category_name'] || !$value['item_code'] || !$value['item_description'] || !$value['min_stock'] || !$value['uom'] || !$value['serial_y_n']){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Fill Required Fields!]');
            }
            else if(!$category){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Category!]');
            }
            else if($value['min_stock'] < 1){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Invalid Quantity!]');
            }
            else if($item > 0){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Duplicate Item Description!]');
            }
            else if($itemcode > 0){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Duplicate Item Code!]');
            }
            else if($value['uom'] != 'Unit' && $value['serial_y_n'] == 'Y'){
                array_push($failed_rows, '[Row: '.$row_num.' => Error: Serial not allowed!]');
            }
            else{
                if($value['serial_y_n'] == 'Y'){
                    $hasSerial = 'YES';
                }
                else{
                    $hasSerial = 'NO';
                }
                $item_name = ucwords($value['item_description']);
                $category_name = $category[0]['category'];

                $items = new Item;
                $items->created_by = auth()->user()->id;
                $items->item = $item_name;
                $items->prodcode = strtoupper($value['item_code']);
                $items->category_id = $category[0]['id'];
                $items->minimum = $value['min_stock'];
                $items->UOM = $value['uom'];
                $items->assemble = 'NO';
                $items->serialize = $hasSerial;
                $sql = $items->save();
                $id = $items->id;
                if(!$sql){
                    array_push($failed_rows, '[Row: '.$row_num.', Error: Save Failed!]');
                }
                else{
                    $stocks = new Stock;
                    $stocks->item_id = $id;
                    $stocks->user_id = auth()->user()->id;
                    $stocks->status = 'default';
                    $stocks->qty = '1';
                    $stocks->save();

                    $userlogs = new UserLogs;
                    $userlogs->user_id = auth()->user()->id;
                    $userlogs->activity = "ITEM ADDED: User successfully saved new Item '$item_name' with ItemID#$id under Category '$category_name'.";
                    $userlogs->save();
                }
            }
            $row_num++;
        }
        if(count($failed_rows) == count($data[0])){
            return redirect()->to('/maintenance?import=failed');
        }
        else if(count($failed_rows) == 0){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEMS FILE IMPORT [NO ERRORS]: User successfully imported file data into Items without any errors.";
            $userlogs->save();

            return redirect()->to('/maintenance?import=success_without_errors');
        }
        else{
            $errors = implode(', ', $failed_rows);
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEMS FILE IMPORT [WITH ERRORS]: User successfully imported file data into Items with the following errors: $errors.";
            $userlogs->save();

            return redirect()->to('/maintenance?import=success_with_errors');
        }
    }

    public function saveItem(Request $request){
        $item = Item::query()->select()
            ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
            ->count();
        if($item != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }

        $itemcode = Item::query()->select()
            ->whereRaw('UPPER(prodcode) = ?',strtoupper($request->prodcode))
            ->count();
        if($itemcode != 0){
            $data = array('result' => 'duplicatecode');
            return response()->json($data);
        }

        $item_name = ucwords($request->item_name);

        $items = new Item;
        $items->created_by = auth()->user()->id;
        $items->item = $item_name;
        $items->prodcode = $request->prodcode;
        $items->category_id = $request->item_category;
        $items->minimum = $request->minimum;
        $items->UOM = $request->item_uom;
        $items->assemble = 'NO';
        $items->serialize = $request->serialize;
        $sql = $items->save();
        $id = $items->id;

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $stocks = new Stock;
            $stocks->item_id = $id;
            $stocks->user_id = auth()->user()->id;
            $stocks->status = 'default';
            $stocks->qty = '1';
            $stocks->save();

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEM ADDED: User successfully saved new Item '$item_name' with ItemID#$id under Category '$request->category_name'.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function updateItem(Request $request){       
        if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
            $item = Item::query()->select()
                ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
                ->count();
        }
        else{
            $item = 0;
        }
        if($item != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }

        if(strtoupper($request->prodcode) != strtoupper($request->prodcode_original)){
            $itemcode = Item::query()->select()
                ->whereRaw('UPPER(prodcode) = ?',strtoupper($request->prodcode))
                ->count();
        }
        else{
            $itemcode = 0;
        }
        if($itemcode != 0){
            $data = array('result' => 'duplicatecode');
            return response()->json($data);
        }

        $item_name = ucwords($request->item_name);

        $items = Item::find($request->input('item_id'));
        $items->created_by = auth()->user()->id;
        $items->item = $item_name;
        $items->prodcode = $request->prodcode;
        $items->category_id = $request->item_category;
        $items->minimum = $request->minimum;
        $items->UOM = $request->item_uom;
        $items->serialize = $request->serialize;
        $sql = $items->save();
        $id = $items->id;

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
            
            if($request->item_category != $request->item_category_original){
                $category_name = "[Category Name: FROM '$request->category_name_original' TO '$request->category_name']";
            }
            else{
                $category_name = NULL;
            }
            if(strtoupper($request->item_name) != strtoupper($request->item_name_original)){
                $item_desc = "[Item Description: FROM '$request->item_name_original' TO '$item_name']";
            }
            else{
                $item_desc = NULL;
            }
            if($request->prodcode != $request->prodcode_original){
                $prodcode = "[Item Code: FROM '$request->prodcode_original' TO '$request->prodcode']";
            }
            else{
                $prodcode = NULL;
            }
            if($request->minimum != $request->minimum_original){
                $minimum = "[Minimum Stock: FROM '$request->minimum_original' TO '$request->minimum']";
            }
            else{
                $minimum = NULL;
            }
            if($request->item_uom != $request->item_uom_original){
                $item_uom = "[Unit of Measure (UOM): FROM '$request->item_uom_original' TO '$request->item_uom']";
            }
            else{
                $item_uom = NULL;
            }
            if($request->item_uom == 'Unit' && ($request->serialize != $request->serialize_original)){
                if($request->serialize == 'YES'){
                    $serialize = 'Required';
                }
                else{
                    $serialize = 'Optional';
                }
                if($request->serialize_original == 'YES'){
                    $serialize_original = 'Required';
                }
                else{
                    $serialize_original = 'Optional';
                }
                $serial = "[Serial (Required/Optional): FROM '$serialize_original' TO '$serialize']";
            }
            else{
                $serial = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "ITEM UPDATED: User successfully updated details of '$request->item_name_original' with the following CHANGES: $category_name $item_desc $prodcode $minimum $item_uom $serial.";
            $userlogs->save();
        }

        $data = array('result' => $result);
        return response()->json($data);
    }

    public function saveCategory(Request $request){
        $category = Category::query()->select()
            ->where('category',strtoupper($request->category))
            ->count();
        if($category != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $categories = new Category;
            $categories->category = strtoupper($request->category);
            $sql = $categories->save();
            $id = $categories->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
            
            $data = array('result' => $result, 'id' => $id, 'category' => strtoupper($request->category));
            return response()->json($data);
        }
    }

    public function logNewCategory(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "CATEGORY ADDED: User successfully saved new Category '$request->category' with CategoryID#$request->id.";
        $userlogs->save();

        return response('true');
    }

    public function updateCategory(Request $request){
        if(strtoupper($request->category_details) != strtoupper($request->category_original)){
            $category = Category::query()->select()
                ->where('category',strtoupper($request->category_details))
                ->count();
        }
        else{
            $category = 0; 
        }
        if($category != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $categories = Category::find($request->input('category_id'));
            $categories->category = strtoupper($request->category_details);
            $sql = $categories->save();

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
            
            $data = array('result' => $result, 'category_id' => $request->category_id, 'category_details' => strtoupper($request->category_details), 'category_original' => strtoupper($request->category_original));
            return response()->json($data);
        }
    }

    public function logUpdateCategory(Request $request){
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "CATEGORY UPDATED: User successfully updated Category FROM '$request->category_original' TO '$request->category_details' with CategoryID#$request->category_id.";
        $userlogs->save();

        return response('true');
    }

    public function saveLocation(Request $request){
        $location = Location::query()->select()
            ->whereRaw('LOWER(location) = ?',strtolower($request->location))
            ->count();
        if($location != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $location_name = strtoupper($request->location);

            $locations = new Location;
            $locations->location = $location_name;
            $locations->status = 'PENDING';
            $sql = $locations->save();
            $id = $locations->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }

            $data = array('result' => $result, 'id' => $id, 'location' => $location_name);
            return response()->json($data);
        }
    }

    public function logNewLocation(Request $request){
        $subject = 'NEW LOCATION REQUEST: '.$request->location;
        $details = [
            'location' => $request->location,
            'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
            'requested_by' => auth()->user()->name
        ];
        Mail::to(explode(',',env('MAIL_TO_DEV')))->send(new requestLocation($details, $subject));
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "LOCATION REQUESTED: User successfully requested new Location '$request->location' with LocationID#$request->id.";
        $userlogs->save();

        return response('true');
    }

    public function updateLocation(Request $request){
        if($request->status != $request->status_original){
            do{
                $locations = Location::find($request->input('location_id'));
                $locations->status = $request->status_original.' - CHANGE REQUESTED';
                $sql = $locations->save();
            }
            while(!$sql);
            
            $data = array(
                'result' => 'request', 
                'id' => $request->location_id, 
                'location' => strtoupper($request->location_details), 
                'status_original' => $request->status_original, 
                'status' => $request->status
            );
            return response()->json($data);
        }
        if(strtoupper($request->location_details) != strtoupper($request->location_original)){
            $location = Location::query()->select()
                ->where('location',strtoupper($request->location_details))
                ->count();
        }
        else{
            $location = 0; 
        }
        if($location != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $location_details = strtoupper($request->location_details);

            $locations = Location::find($request->input('location_id'));
            $locations->location = $location_details;
            $sql = $locations->save();
            $id = $locations->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "LOCATION UPDATED: User successfully updated Location FROM '$request->location_original' TO '$location_details' with LocationID#$id.";
                $userlogs->save();
            }
            
            $data = array('result' => $result);
            return response()->json($data);
        }
    }

    public function requestStatusChange(Request $request){
        $subject = 'LOCATION STATUS CHANGE REQUEST: '.$request->location;
        $details = [
            'location' => $request->location,
            'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
            'requested_by' => auth()->user()->name,
            'status_original' => $request->status_original,
            'status' => $request->status
        ];
        Mail::to(explode(',',env('MAIL_TO_DEV')))->send(new requestStatusChange($details, $subject));
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "LOCATION STATUS CHANGE REQUESTED: User successfully requested Location Status Change of '$request->location' FROM '$request->status_original' TO '$request->status' with LocationID#$request->id.";
        $userlogs->save();

        return response('true');
    }

    public function GetWarranty(){
        return DataTables::of(Warranty::all())
        ->make(true);
    }

    public function AddWarranty(Request $request){
        $inclusive = implode(", ",$request->inclusive);
        $sql = Warranty::create([
            'Warranty_Name' => $request->warranty,
            'Duration' => $request->duration,
            'Inclusive' => $inclusive
        ]);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "WARRANTY ADDED: User successfully saved new Warranty '$request->warranty' with Duration '$request->duration-Month/s' and Inclusive: [$inclusive].";
            $userlogs->save();
        }

        return response($result);
    }

    public function UpdateWarranty(Request $request){
        $inclusive = implode(", ",$request->inclusive);
        $sql = Warranty::where('id', $request->id)->update([
            'Warranty_Name' => $request->warranty,
            'Duration' => $request->duration,
            'Inclusive' => $inclusive
        ]);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "WARRANTY UPDATED: User successfully updated details of Warranty '$request->warranty' with Duration '$request->duration-Month/s' and Inclusive: [$inclusive].";
            $userlogs->save();
        }

        return response($result);
    }
}