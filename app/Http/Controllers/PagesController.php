<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\emailNewUser;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;
use App\Mail\reportProblem;
use App\Mail\replySender;
use App\Models\Report;
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function gitpull(){
        $output = shell_exec('cd /var/www/html/main-warehouse && git pull');
        return $output;
    }

    public function index(){
        return view('pages/index');        
    }

    public function index_data(){
        if(auth()->user()->hasanyRole('admin') || auth()->user()->hasanyRole('encoder') || auth()->user()->hasanyRole('viewer')) //---ROLES---//
        {
            $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, 
            UPPER(roles.name) AS role, user_logs.activity AS activity, user_logs.created_at AS date, 
            DATE_FORMAT(user_logs.created_at, "%b. %d, %Y, %h:%i %p") AS datetime')
                ->join('users', 'users.id', '=', 'user_id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy('user_logs.id', 'DESC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('merchant') || auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, 
            UPPER(roles.name) AS role, user_logs.activity AS activity, user_logs.created_at AS date, 
            DATE_FORMAT(user_logs.created_at, "%b. %d, %Y, %h:%i %p") AS datetime')
                ->where('user_logs.user_id', auth()->user()->id)
                ->join('users', 'users.id', '=', 'user_id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy('user_logs.id', 'DESC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, 
            UPPER(roles.name) AS role, user_logs.activity AS activity, user_logs.created_at AS date, 
            DATE_FORMAT(user_logs.created_at, "%b. %d, %Y, %h:%i %p") AS datetime')
                ->where('users.company', auth()->user()->company)
                ->whereRaw('roles.name = "sales" OR (roles.name = "approver - sales" AND users.id="'.auth()->user()->id.'")')
                ->join('users', 'users.id', '=', 'user_id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy('user_logs.id', 'DESC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, 
            UPPER(roles.name) AS role, user_logs.activity AS activity, user_logs.created_at AS date, 
            DATE_FORMAT(user_logs.created_at, "%b. %d, %Y, %h:%i %p") AS datetime')
                ->where('users.company', auth()->user()->company)
                ->whereRaw('roles.name = "admin" OR roles.name = "encoder" OR (roles.name = "approver - warehouse" AND users.id="'.auth()->user()->id.'")')
                ->join('users', 'users.id', '=', 'user_id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy('user_logs.id', 'DESC')
                ->get();
        }
        else if(auth()->user()->hasanyRole('accounting')) //---ROLES---//
        {
            $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, 
            UPPER(roles.name) AS role, user_logs.activity AS activity, user_logs.created_at AS date, 
            DATE_FORMAT(user_logs.created_at, "%b. %d, %Y, %h:%i %p") AS datetime')
                ->whereRaw('roles.name = "sales" OR (roles.name = "accounting" AND users.id="'.auth()->user()->id.'")')
                ->join('users', 'users.id', '=', 'user_id')
                ->join('model_has_roles', 'model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->orderBy('user_logs.id', 'DESC')
                ->get();
        }

        return DataTables::of($list)->make(true);
    }

    public function index_reload(){
        $logs = UserLogs::select()->count();
        return $logs;
    }

    public function change_validate(Request $request){
        if(Hash::check($request->current, auth()->user()->password)){
            $result = 'true';
        }
        else{
            $result = 'false';
        }

        return response($result);
    }

    public function change_password(Request $request){
        do{
            $users = User::find(auth()->user()->id);
            $users->password = Hash::make($request->new);
            $sql = $users->save();
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "CHANGE PASSWORD: User successfully changed own account password.";
            $userlogs->save();
        }

        return response($result);
    }

    public function users(){
        if(!auth()->user()->hasanyRole('admin')) //---ROLES---//
        {
            return redirect('/');
        }
        $role = Role::query()->select()->get()->sortBy('name');
        
        return view('pages/users', compact('role'));
    }

    public function users_data(){
        $list = User::query()->selectRaw('users.id AS user_id, users.name AS user_name, users.email AS user_email, users.company AS company, 
        UPPER(roles.name) AS role_name, roles.name AS role, users.status AS user_status')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->orderBy('user_status', 'ASC')
            ->orderBy('role_name', 'ASC')
            ->orderBy('company', 'ASC')
            ->orderBy('user_name', 'ASC')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function validate_users_save(Request $request){
        $email = User::query()->select()
            ->where('email',$request->email)
            ->count();
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            $data = array('result' => 'invalid');
            return response()->json($data);
        }
        else if($email != 0){
            $data = array('result' => 'duplicate');
            return response()->json($data);
        }
        else {
            $data = array('result' => 'true');
            return response()->json($data);
        }
    }
    
    public function users_save(Request $request){
        $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $charLength = strlen($char) - 1;
        for($i = 0; $i < 8; $i++){
            $n = rand(0, $charLength);
            $pass[] = $char[$n];
        }
        $password = implode($pass);
    
        $name = ucwords($request->name);
    
        $users = new User;
        $users->name = $name;
        $users->email = strtolower($request->email);
        $users->password = Hash::make($password);
        $users->company = $request->company;
        $users->assignRole($request->role);
        $users->status = 'ACTIVE';
        $sql = $users->save();
        $id = $users->id;
    
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            Password::broker()->sendResetLink(['email'=>strtolower($request->email)]);
    
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "USER ADDED: User successfully saved details of $name with UserID#$id.";
            $userlogs->save();
        }
        
        return response($result);
    }

    public function validate_users_update(Request $request){
        if(strtolower($request->email1) != strtolower($request->email2)){
            $email = User::query()->select()
                ->where('email',$request->email1)
                ->count();
        }
        else{
            $email = 0;
        }
        if(!filter_var($request->email1, FILTER_VALIDATE_EMAIL)){
            return response('invalid');
        }
        else if($email != 0){
            return response('duplicate');
        }
        else {
            return response('true');
        }
    }
    
    public function users_update(Request $request){
        $name1 = ucwords($request->name1);
        $email1 = strtolower($request->email1);
        
        $users = User::find($request->input('id1'));
        $users->name = $name1;
        $users->email = $email1;
        $users->company = $request->company1;
        $users->removeRole($request->role2);
        $users->assignRole($request->role1);
        $sql = $users->save();

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        if($result == 'true'){
            if($name1 != $request->name2){
                $name = "[Fullname: FROM '$request->name2' TO '$name1']";
            }
            else{
                $name = NULL;
            }
            if($email1 != $request->email2){
                $email = "[Email: FROM '$request->email2' TO '$email1']";
            }
            else{
                $email = NULL;
            }
            if($request->company1 != $request->company2){
                $company = "[Company: FROM '$request->company2' TO '$request->company1']";
            }
            else{
                $company = NULL;
            }
            if($request->role1 != $request->role2){
                $role = "[User Level: FROM '$request->role2' TO '$request->role1']";
            }
            else{
                $role = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "USER UPDATED: User successfully updated details of $request->name2 with UserID#$request->id1 with the following CHANGES: $name $email $company $role.";
            $userlogs->save();
        }

        return response($result);
    }

    public function users_status(Request $request){
        do{
            $name = ucwords($request->name);
            
            $users = User::find($request->id);
            $users->status = $request->status;
            $sql = $users->save();
        }
        while(!$sql);

        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }

        if($result == 'true'){
            if($request->status == 'ACTIVE'){
                $status1 = 'ACTIVE';
                $status2 = 'INACTIVE';
            }
            else{
                $status1 = 'INACTIVE';
                $status2 = 'ACTIVE';
            }
            $status = "[Status: FROM '$status2' TO '$status1']";

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "USER UPDATED: User successfully updated details of $name with UserID#$request->id with the following CHANGES: $status.";
            $userlogs->save();
        }

        return response($result);
    }

    public function generateTicket(Request $request){
        $ticketnum = Report::query()->select()->where('ticket_number',$request->ticket_number)->count();
        if($ticketnum == 0){
            return response('unique');
        }
        return response('duplicate');
    }

    public function report_submit(Request $request){    
        $report = new Report;
        $report->reported_by =auth()->user()->id;
        $report->ticket_number = $request->ticket_number;
        $report->report_category = ucwords($request->report_category);
        $report->details = ucfirst($request->details);
        $sql = $report->save();
    
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';
        }
        
        return response($result);
    }

    public function report_log(Request $request){
        $subject = 'TICKET NUMBER: '.$request->ticket_number;
        $email = auth()->user()->email;
        if(str_contains($email, '@yahoo')){
            $email = 'noreply@ideaserv.com.ph';
        }
        $sender = [
            'email' => $email,
            'name' => auth()->user()->name
        ];
        $details = [
            'ticket_number' => $request->ticket_number,
            'reportdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
            'reported_by' => auth()->user()->name,
            'report_category' => ucwords($request->report_category),
            'details' => ucfirst($request->details)
        ];
        Mail::to(explode(',',env('MAIL_TO_DEV')))->send(new reportProblem($details, $subject, $sender));

        $details = [
            'name' => auth()->user()->name,
            'ticket_number' => $request->ticket_number,
            'reportdate' => Carbon::now()->isoformat('dddd, MMMM DD, YYYY'),
            'reported_by' => auth()->user()->name,
            'report_category' => ucwords($request->report_category),
            'details' => ucfirst($request->details)
        ];
        Mail::to(auth()->user()->email)->send(new replySender($details, $subject));
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "USER REPORTED AN ISSUE: User successfully reported issue with Ticket Number $request->ticket_number.";
        $userlogs->save();

        return response('true');
    }
}