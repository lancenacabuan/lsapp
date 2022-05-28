<?php

namespace App\Http\Controllers;

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

    public function pull(){
        $output = shell_exec('cd /var/www/html/main-warehouse && git pull');
        return $output;
    }

    public function index(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/assembly');
        }
        return view('pages/index');        
    }

    public function index_data(){
        $list = UserLogs::selectRaw('users.id AS user_id, users.name AS username, users.email AS email, roles.name AS role, user_logs.activity AS activity, user_logs.created_at AS date, user_logs.id AS log_id')
        ->join('users', 'users.id', '=', 'user_id')
        ->join('model_has_roles', 'model_id', '=', 'users.id')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->orderBy('user_logs.id', 'DESC')
        ->get();

        return DataTables::of($list)->make(true);
    }

    public function changepassword(){
        return view('pages/changepassword');
    }

    public function password_save(Request $request){
        if(Hash::check($request->current, auth()->user()->password)){
            $users = User::find(auth()->user()->id);
            $users->password = Hash::make($request->new);
            $sql = $users->save();

            if(!$sql){
                $result = 'false';
            }
            else {
                $result = 'true';
            }
        }
        else{
            $result = 'error';
        }

        if($result == 'true'){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "CHANGE PASSWORD: User successfully changed own account password.";
            $userlogs->save();
        }

        return response($result);
    }

    public function users(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver - sales')) //---ROLES---//
        {
            return redirect('/stockrequest');
        }
        if(auth()->user()->hasanyRole('approver - warehouse')) //---ROLES---//
        {
            return redirect('/stocktransfer');
        }
        if(auth()->user()->hasanyRole('assembler')) //---ROLES---//
        {
            return redirect('/assembly');
        }
        if(!auth()->user()->hasanyRole('admin')) //---ROLES---//
        {
            return redirect('/');
        }
        $role = Role::query()->select()->get()->sortBy('name');
        
        return view('pages/users', compact('role'));
    }

    public function users_data(){
        $list = User::selectRaw('users.id AS user_id, users.name AS user_name, users.email AS user_email, roles.name AS role_name, users.status AS user_status')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
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
        $users->removeRole($request->role2);
        $users->assignRole($request->role1);
        $users->status = $request->status1;
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
            if($request->role1 != $request->role2){
                $role = "[User Level: FROM '$request->role2' TO '$request->role1']";
            }
            else{
                $role = NULL;
            }
            if($request->status1 != $request->status2){
                $status = "[Status: FROM '$request->status2' TO '$request->status1']";
            }
            else{
                $status = NULL;
            }

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "USER UPDATED: User successfully updated details of $request->name2 with UserID#$request->id1 with the following CHANGES: $name $email $role $status.";
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
        $report->details = ucfirst($request->details);
        $sql = $report->save();
    
        if(!$sql){
            $result = 'false';
        }
        else {
            $result = 'true';

            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = "USER REPORTED AN ISSUE: User successfully reported issue with Ticket Number $request->ticket_number.";
            $userlogs->save();
        }
        
        return response($result);
    }
}