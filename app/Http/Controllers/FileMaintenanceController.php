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
}
