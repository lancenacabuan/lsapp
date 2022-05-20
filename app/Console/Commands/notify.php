<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\notifRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\RequestType;
use App\Models\Warranty;
use App\Models\Status;
use App\Models\Stock;
use App\Models\StockRequest;
use App\Models\Requests;
use App\Models\RequestTransfer;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class notify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the excel file to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle(){
        $list = Requests::select('requests.id AS req_id', 'requests.created_at AS req_date', 'requests.request_number AS req_num', 'requests.requested_by AS user_id', 'users.name AS req_by', 'users.email AS email', 'request_type.name AS req_type', 'status.status AS status', 'users.name AS req_by', 'request_type.id AS req_type_id', 'status.id AS status_id', 'requests.schedule AS sched', 'prepared_by', 'client_name', 'location', 'contact', 'remarks', 'reference', 'needdate', 'prepdate', 'requests.item_id AS item_id', 'qty', 'assembly_reqnum', 'notify')
            ->whereNotIn('requests.status', ['7','8','10','11','14','19'])
            ->join('users', 'users.id', '=', 'requests.requested_by')
            ->join('request_type', 'request_type.id', '=', 'requests.request_type')
            ->join('status', 'status.id', '=', 'requests.status')
            ->orderBy('requests.created_at', 'DESC')
            ->get()
            ->toArray();
        
        foreach($list as $key => $value){
            $today = new DateTime(date("Y-m-d"));
            $deadline = new DateTime($value['needdate']);

            $difference = $today->diff($deadline)->format("%r%a");

            if($value['req_type'] == 'SALES'){
                $items = StockRequest::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity','warranty')
                    ->join('categories', 'categories.id', 'stock_request.category')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $value['req_num'])
                    ->get()
                    ->toArray();
                foreach($items as $keys => $values){
                    if($values['warranty'] == '0'){
                        $items[$keys]['Warranty_Name'] = 'NO WARRANTY';
                    }
                    else{
                        $items[$keys]['Warranty_Name'] = Warranty::query()->where('id',$values['warranty'])->first()->Warranty_Name;
                    }
                }
            }
            else{
                $items = StockRequest::query()->select('categories.category AS category','items.item AS item','items.UOM AS uom','quantity')
                    ->join('categories', 'categories.id', 'stock_request.category')
                    ->join('items', 'items.id', 'stock_request.item')
                    ->where('request_number', $value['req_num'])
                    ->get();
            }

            if($difference == '3' && !$value['notify']){
                Requests::where('request_number',$value['req_num'])->update(['notify' => '3-Days']);
                $subject = '[LAST 3 DAYS] STOCK REQUEST NO. '.$value['req_num'];
                $user = User::role('admin')->where('status','ACTIVE')->get();
                foreach($user as $keyx){
                    $details = [
                        'name' => ucwords($keyx->name),
                        'action' => 'is 3-Days Prior its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'role' => 'Admin',
                        'items' => $items
                    ];
                    Mail::to($keyx->email)->send(new notifRequest($details, $subject));
                }
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is 3-Days Prior its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'role' => 'Sales',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
            if($difference == '0' && $value['notify'] == '3-Days'){
                Requests::where('request_number',$value['req_num'])->update(['notify' => 'Today']);       
                $subject = '[DEADLINE TODAY] STOCK REQUEST NO. '.$value['req_num'];
                $user = User::role('admin')->where('status','ACTIVE')->get();
                foreach($user as $keyx){
                    $details = [
                        'name' => ucwords($keyx->name),
                        'action' => 'is now due today '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'role' => 'Admin',
                        'items' => $items
                    ];
                    Mail::to($keyx->email)->send(new notifRequest($details, $subject));
                }
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'is now due today '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'role' => 'Sales',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
            if($difference == '-1' && $value['notify'] == 'Today'){
                Requests::where('request_number',$value['req_num'])->update(['notify' => 'Overdue']);       
                $subject = '[OVERDUE] STOCK REQUEST NO. '.$value['req_num'];
                $user = User::role('admin')->where('status','ACTIVE')->get();
                foreach($user as $keyx){
                    $details = [
                        'name' => ucwords($keyx->name),
                        'action' => 'has already gone past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                        'request_number' => $value['req_num'],
                        'reqdate' => $value['req_date'],
                        'requested_by' => $value['req_by'],
                        'needdate' => $value['needdate'],
                        'reqtype' => $value['req_type'],
                        'status' => $value['status'],
                        'client_name' => $value['client_name'],
                        'location' => $value['location'],
                        'contact' => $value['contact'],
                        'remarks' => $value['remarks'],
                        'reference' => $value['reference'],
                        'role' => 'Admin',
                        'items' => $items
                    ];
                    Mail::to($keyx->email)->send(new notifRequest($details, $subject));
                }
                $details = [
                    'name' => $value['req_by'],
                    'action' => 'has already gone past its deadline on '.Carbon::parse($value['needdate'])->isoformat('dddd, MMMM DD, YYYY').'.',
                    'request_number' => $value['req_num'],
                    'reqdate' => $value['req_date'],
                    'requested_by' => $value['req_by'],
                    'needdate' => $value['needdate'],
                    'reqtype' => $value['req_type'],
                    'status' => $value['status'],
                    'client_name' => $value['client_name'],
                    'location' => $value['location'],
                    'contact' => $value['contact'],
                    'remarks' => $value['remarks'],
                    'reference' => $value['reference'],
                    'role' => 'Sales',
                    'items' => $items
                ];
                Mail::to($value['email'])->send(new notifRequest($details, $subject));
            }
        }
    }
}


