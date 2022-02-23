<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class FileMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function filemaintenance(Request $request){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        $categories = Category::select('id','category')->get()->sortBy('category');

        return view('pages/filemaintenance', compact('categories'));   
    }

    public function fm_items()
    {
        $list = Item::select('items.id AS item_id', 'items.item AS item_name', 'category', 'items.category_id AS category_id', 'items.UOM AS uom')
            ->join('categories', 'categories.id', '=', 'category_id');
        return DataTables::of($list)->make(true);
    }

    public function fm_categories(){
        $list = Category::select('id','category')->orderBy('category','ASC')->get();
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
                $userlogs->activity = "ITEM ADDED: User successfully saved new item '$item_name' with ItemID#$id under category '$request->category_name'.";
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
                    $activity = "ITEM UPDATED: User successfully updated item description from '$request->item_name_original' into '$item_name' with ItemID#$id under category '$request->category_name'.";
                }
                else if(strtoupper($request->item_name) == strtoupper($request->item_name_original) && $request->item_category != $request->item_category_original && $request->item_uom == $request->item_uom_original){
                    $activity = "ITEM UPDATED: User successfully updated item category of '$item_name' with ItemID#$id changed from '$request->category_name_original' into '$request->category_name' with new CategoryID#'$request->item_category'.";
                }
                else if(strtoupper($request->item_name) == strtoupper($request->item_name_original) && $request->item_category == $request->item_category_original && $request->item_uom != $request->item_uom_original){
                    $activity = "ITEM UPDATED: User successfully updated item UOM of '$item_name' with ItemID#$id changed from '$request->item_uom_original' into '$request->item_uom'.";
                }
                else{
                    $activity = "ITEM UPDATED: User successfully updated details of ItemID#$id with changes:\n
                        Category Name: '$request->category_name_original' => '$request->category_name'\n
                        Item Description: '$request->item_name_original' => '$item_name'\n
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
        $userlogs->activity = "CATEGORY ADDED: User successfully saved new category '$request->category' with CategoryID#$request->id.";
        $userlogs->save();
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
        $userlogs->activity = "CATEGORY UPDATED: User successfully updated category from '$request->category_original' into '$request->category_details' with CategoryID#$request->category_id.";
        $userlogs->save();
    }
}