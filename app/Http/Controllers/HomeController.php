<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Cnfusers;
use Auth;

class HomeController extends Controller{
    public function index(){
        if(!Auth::check()) {
            return redirect('/login');
        }
        $superusers = User::all();
        if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin')  == 'admin'){
            $users = Cnfusers::all();
        }else{
            $users = Cnfusers::where('super_cnf_id',auth()->id())->get();
        }
        return view('home.index', compact('superusers','users'));
    }
}