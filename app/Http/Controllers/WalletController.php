<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Wallet;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Models\Cnfusers;

class WalletController extends Controller{
    function __construct(){

    }

    public function index(Request $request){
        $users = Cnfusers::where('status','1')->get(); 
        return view('wallet.index', compact('users'));
    }
}