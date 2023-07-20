<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller{
    public function index(){
        $users = User::where('id','!=',1)->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create(){
        return view('users.create');
    }

    public function store(User $user, StoreUserRequest $request){
        $user->create(array_merge($request->validated(),[
            'password' => '12345678' 
        ]));
        $user->syncRoles('2');
        return redirect()->route('users.index')->withSuccess(__('User created successfully.'));
    }

    public function show(User $user){
        return view('users.show', [
            'user' => $user
        ]);
    }

    public function edit(User $user) {
        return view('users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    public function passwordChange(Request $request, User $user){
        if($request->isMethod('post')){
            #Match The Old Password
            if(!Hash::check($request->old_password, auth()->user()->password)){
                return back()->with("error", "Old Password Doesn't match!");
            }elseif($request->new_password != $request->confirm_password){
                return back()->with("error", "New Password and Confirm Password Doesn't match!");
            }else{
                #Update the new Password
                User::whereId(auth()->user()->id)->update([
                    'password' => Hash::make($request->new_password)
                ]);
                return back()->with("status", "Password changed successfully!");
            }
        }else{
            return view('users.password');
        }
    }

    public function myProfile(User $user){
        return view('users.show', [
            'user' => $user
        ]);
    }

    public function update(User $user, UpdateUserRequest $request){
        $user->update($request->validated());
        $user->syncRoles($request->get('role'));
        return redirect()->route('users.index')->withSuccess(__('User updated successfully.'));
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->withSuccess(__('User deleted successfully.'));
    }
}