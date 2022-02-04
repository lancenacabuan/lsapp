<?php

namespace App\Http\Controllers;

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
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver'))
        {
            return redirect('/stockrequest');
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

    // public function joborder(){
    //     $title = 'JOB ORDER';
    //     return view('pages/joborder')->with('title', $title);
    // }

    public function assembly(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver'))
        {
            return redirect('/stockrequest');
        }
        $title = 'ASSEMBLY';
        return view('pages/assembly')->with('title', $title);
    }

    // public function pullout(){
    //     $title = 'PULLOUT';
    //     return view('pages/pullout')->with('title', $title);
    // }

    public function filemaintenance(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver'))
        {
            return redirect('/stockrequest');
        }
        $title = 'FILE MAINTENANCE';
        return view('pages/filemaintenance')->with('title', $title);   
    }
    
    public function changepassword(){
        return view('pages/changepassword');
    }

    public function password_save(Request $request)
    {
        if (Hash::check($request->current, auth()->user()->password))
        {
            $users = User::find(auth()->user()->id);
            $users->password = Hash::make($request->pass2);
            $users->save();
            return response('true');
        }
        return response('false');
    }

    public function users(){
        if(auth()->user()->hasanyRole('sales') || auth()->user()->hasanyRole('approver'))
        {
            return redirect('/stockrequest');
        }
        if(!auth()->user()->hasanyRole('admin'))
        {
            return redirect('/');
        }
        $role =  Role::query()->select()
            ->get()
            ->sortBy('name');
        $list = User::selectRaw('users.id AS user_id, users.name AS user_name, users.email AS user_email, roles.name AS role_name')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->get();
        
        return view('pages/users', compact('list','role'));
    }

    public function users_data(){
        $list = User::selectRaw('users.id AS user_id, users.name AS user_name, users.email AS user_email, roles.name AS role_name')
            ->join('model_has_roles', 'model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->get();

        return DataTables::of($list)->make(true);
    }

    public function users_store(Request $request){
        // $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        // $pass = array();
        // $charLength = strlen($char) - 1;
        // for ($i = 0; $i < 8; $i++) {
        //     $n = rand(0, $charLength);
        //     $pass[] = $char[$n];
        // }
        // $password = implode($pass);

        // $_SESSION['email'] = strtolower($request->email);
        // $_SESSION['name'] = ucwords($request->name);
        // $_SESSION['password'] = $password;

        //return $_SESSION['email'].' '.$_SESSION['name'].' '.$_SESSION['password'];
        //return view('emails/emailNewUser');

        $users = new User;
        $users->name = ucwords($request->name);
        $users->email = strtolower($request->email);
        $users->password = Hash::make('qwerty');
        $users->assignRole($request->role);
        $users->save();

        return response()->json($users);   
    }

    public function users_update(Request $request)
    { 
        $users = User::find($request->input('id1'));
        $users->name = $request->name1;
        $users->email = $request->email1;
        $users->removeRole($request->role2);
        $users->assignRole($request->role1);
        $users->save();

        return response()->json($users);
    }
}
