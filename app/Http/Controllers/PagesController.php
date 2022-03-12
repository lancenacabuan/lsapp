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
use App\Models\User;
use App\Models\UserLogs;
use Yajra\Datatables\Datatables;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    public function password_save(Request $request)
    {
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
        if(!auth()->user()->hasanyRole('admin')) //---ROLES---//
        {
            return redirect('/');
        }
        $role =  Role::query()->select()->get()->sortBy('name');
        
        return view('pages/users', compact('role'));
    }

    public function users_data(){
        $list = User::selectRaw('users.id AS user_id, users.name AS user_name, users.email AS user_email, roles.name AS role_name, users.status AS user_status')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function users_save(Request $request){
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
            }
            
            $data = array('result' => $result, 'id' => $id, 'name' => $name, 'email' => strtolower($request->email));
            return response()->json($data);
        }
    }

    public function logNewUser(Request $request){
        Password::broker()->sendResetLink(['email'=>$request->email]);

        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = "USER ADDED: User successfully saved details of $request->name with UserID#$request->id.";
        $userlogs->save();

        return response('true');
    }

    public function users_update(Request $request){
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
            $name = ucwords($request->name1);
            
            $users = User::find($request->input('id1'));
            $users->name = $name;
            $users->email = strtolower($request->email1);
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
                $userlogs = new UserLogs;
                $userlogs->user_id = auth()->user()->id;
                $userlogs->activity = "USER UPDATED: User successfully updated details of $name with UserID#$request->id1.";
                $userlogs->save();
            }

            return response($result);
        }
    }
}