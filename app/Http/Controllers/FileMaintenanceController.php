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
        $title = 'FILE MAINTENANCE';
        return view('pages/filemaintenance')->with('title', $title);   
    }

    public function fm_items()
    {
        $list = Item::select('items.id AS item_id', 'items.item AS item_name', 'category')
            ->join('categories', 'categories.id', '=', 'category_id');
        return DataTables::of($list)->make(true);
    }

    public function fm_categories(){
        $list = Category::select('id','category')->orderBy('category','ASC')->get();
        return DataTables::of($list)->make(true);
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
}