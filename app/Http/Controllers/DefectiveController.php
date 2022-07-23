<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Defective;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class DefectiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function defective(){
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

        return view('/pages/defective');
    }

    public function defective_data(){
        $data = Stock::query()
            ->select('stocks.id AS stock_id', 'categories.id AS category_id', 'items.id AS item_id', 'categories.category', 'item', 'name', 'serial', 'return_number', 'stocks.status AS status', 'defectiveDate')
            ->selectRaw('DATE_FORMAT(stocks.defectiveDate, "%b. %d, %Y, %h:%i %p") AS defectiveDatetime')
            ->join('items', 'items.id', 'item_id')
            ->join('categories', 'categories.id', 'category_id')
            ->join('users', 'users.id', 'user_id')
            ->whereIn('stocks.status', ['defectives','FOR RECEIVING'])
            ->orderBy('defectiveDate', 'ASC')
            ->orderBy('name', 'ASC')
            ->orderBy('category', 'ASC')
            ->orderBy('item', 'ASC')
            ->orderBy('stocks.id', 'ASC')
            ->get();
        
        return DataTables::of($data)->make(true);
    }

    public function reload(){
        $data_update = Stock::latest('updated_at')->first()->updated_at;
        return $data_update;
    }

    public function generateReturnNum(Request $request){
        $reqnum = Defective::query()->select()->where('return_number',$request->return_number)->count();
        if($reqnum == 0){
            return response('unique');
        }
        return response('duplicate');
    }

    public function defective_return(Request $request){
        do{
            $stock = Stock::where('id', $request->stock_id)
                ->update(['status' => 'FOR RECEIVING', 'return_number' => $request->return_number]);
        }
        while(!$stock);
        
        if(!$stock){
            $result = 'false';
        }
        else{
            do{
                $defective = new Defective;
                $defective->return_number = $request->return_number;
                $defective->user_id = auth()->user()->id;
                $defective->user = auth()->user()->name;
                $defective->stock_id = $request->stock_id;
                $defective->category_id = $request->category_id;
                $defective->category = $request->category;
                $defective->item_id = $request->item_id;
                $defective->item = $request->item;
                $defective->serial = $request->serial;
                $defective->status = 'FOR RECEIVING';
                $sql = $defective->save();
            }
            while(!$sql);

            if(!$sql){
                $result = 'false';
            }
            else{
                $result = 'true';

                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "FOR RECEIVING DEFECTIVE ITEM: User successfully prepared Defective Item for receiving with Return Number $request->return_number.";
                $userlogs->save();
            }
        }

        return response($result);
    }
}