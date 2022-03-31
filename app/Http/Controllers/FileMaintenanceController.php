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
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
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
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        if(!auth()->user()->hasanyRole('admin')) //---ROLES---//
        {
            return redirect('/');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');

        return view('pages/maintenance', compact('categories'));   
    }

    public function fm_items(){
        $list = Item::select('items.id', 'items.item', 'categories.category', 'items.category_id', 'items.UOM')
            ->where('items.assemble', 'NO')
            ->join('categories', 'categories.id', 'category_id');
        return DataTables::of($list)->make(true);
    }

    public function asm_items(){
        $list = Item::select('items.id', 'items.item', 'categories.category', 'items.category_id', 'items.UOM')
            ->where('items.assemble', 'YES')
            ->join('categories', 'categories.id', 'category_id');
        return DataTables::of($list)->make(true);
    }

    public function fm_categories(){
        $list = Category::select('id', 'category')->orderBy('category', 'ASC')->get();
        return DataTables::of($list)->make(true);
    }

    public function fm_locations(){
        $list = Location::select('id AS location_id', 'location', 'status')->whereNotIn('id',['7','8','9','10'])->get();
        return DataTables::of($list)->make(true);
    }

    public function saveItem(Request $request){
        $item = Item::query()->select()
            ->whereRaw('LOWER(item) = ?',strtolower($request->item_name))
            ->count();
        if($item != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $item_name = ucwords($request->item_name);

            $items = new Item;
            $items->created_by = auth()->user()->id;
            $items->item = $item_name;
            $items->category_id = $request->item_category;
            $items->UOM = $request->item_uom;
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "ITEM ADDED: User successfully saved new Item '$item_name' with ItemID#$id under Category '$request->category_name'.";
                $userlogs->save();
            }

            $data = array('result' => $result);
            return response()->json($data);
        }
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
        else {
            $item_name = ucwords($request->item_name);

            $items = Item::find($request->input('item_id'));
            $items->created_by = auth()->user()->id;
            $items->item = $item_name;
            $items->category_id = $request->item_category;
            $items->UOM = $request->item_uom;
            $sql = $items->save();
            $id = $items->id;

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';

                if(strtoupper($request->item_name) != strtoupper($request->item_name_original) && $request->item_category == $request->item_category_original && $request->item_uom == $request->item_uom_original){
                    $activity = "ITEM UPDATED: User successfully updated Item Description from '$request->item_name_original' into '$item_name' with ItemID#$id under Category '$request->category_name'.";
                }
                else if(strtoupper($request->item_name) == strtoupper($request->item_name_original) && $request->item_category != $request->item_category_original && $request->item_uom == $request->item_uom_original){
                    $activity = "ITEM UPDATED: User successfully updated Item Category of '$item_name' with ItemID#$id changed from '$request->category_name_original' into '$request->category_name' with new CategoryID#'$request->item_category'.";
                }
                else if(strtoupper($request->item_name) == strtoupper($request->item_name_original) && $request->item_category == $request->item_category_original && $request->item_uom != $request->item_uom_original){
                    $activity = "ITEM UPDATED: User successfully updated Item UOM of '$item_name' with ItemID#$id changed from '$request->item_uom_original' into '$request->item_uom'.";
                }
                else{
                    $activity = "ITEM UPDATED: User successfully updated details of ItemID#$id with changes: 
                        Category Name: '$request->category_name_original' => '$request->category_name', 
                        Item Description: '$request->item_name_original' => '$item_name', 
                        UOM: '$request->item_uom_original' => '$request->item_uom'.
                        ";
                }

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = $activity;
                $userlogs->save();
            }

            $data = array('result' => $result);
            return response()->json($data);
        }
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
        $userlogs->activity = "CATEGORY UPDATED: User successfully updated Category from '$request->category_original' into '$request->category_details' with CategoryID#$request->category_id.";
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
        $user = array(
            'c4lance@outlook.com',
            'lancenacabuan@yahoo.com',
            'lorenzonacabuan@gmail.com'
            // 'gerard.mallari@gmail.com',
            // 'jolopez@ideaserv.com.ph',
            // 'lancenacabuan@outlook.com',
            // 'lorenzonacabuan@gmail.com'
        );
        $subject = 'NEW LOCATION REQUEST: '.$request->location;
        foreach($user as $email){
            $details = [
                'location' => $request->location,
                'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'requested_by' => auth()->user()->name
            ];
            Mail::to($email)->send(new requestLocation($details, $subject));
        }
        
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
                $userlogs->activity = "LOCATION UPDATED: User successfully updated Location from '$request->location_original' into '$location_details' with LocationID#$id.";
                $userlogs->save();
            }
            
            $data = array('result' => $result);
            return response()->json($data);
        }
    }

    public function requestStatusChange(Request $request){
        $user = array(
            'c4lance@outlook.com',
            'lancenacabuan@yahoo.com',
            'lorenzonacabuan@gmail.com'
            // 'gerard.mallari@gmail.com',
            // 'jolopez@ideaserv.com.ph',
            // 'lancenacabuan@outlook.com',
            // 'lorenzonacabuan@gmail.com'
        );
        $subject = 'LOCATION STATUS CHANGE REQUEST: '.$request->location;
        foreach($user as $email){
            $details = [
                'location' => $request->location,
                'reqdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
                'requested_by' => auth()->user()->name,
                'status_original' => $request->status_original, 
                'status' => $request->status
            ];
            Mail::to($email)->send(new requestStatusChange($details, $subject));
        }
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "LOCATION STATUS CHANGE REQUESTED: User successfully requested Location Status Change of '$request->location' FROM '$request->status_original' INTO '$request->status' with LocationID#$request->id.";
        $userlogs->save();

        return response('true');
    }
}