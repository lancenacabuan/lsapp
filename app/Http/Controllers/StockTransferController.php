<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\RequestTransfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function stocktransfer(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales'))
        {
            return redirect('/stockrequest');
        }
        return view('/pages/stocktransfer');
    }
    
    public function generateReqNum(Request $request){
        $reqnum = RequestTransfer::query()->select()
            ->where('request_number',$request->request_number)
            ->count();
            if($reqnum == 0){
                return response('unique');
            }
            return response('duplicate');
    }
}