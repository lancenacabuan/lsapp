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
    protected $description = 'Send email notification before deadline';

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
        //INSERT CODE HERE...
    }
}


