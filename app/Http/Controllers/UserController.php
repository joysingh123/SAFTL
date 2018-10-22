<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;

use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users')->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id',$id)->get();
        $user_first = $user->first();
        $roles = Role::all(['id','name']);
        $role =  $user_first->getRoleNames();
        $role =  $role[0];
        return view("edituser")->with('user',$user->first())->with('roles',$roles)->with('role',$role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        echo "process edit";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator =  Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'roles'=> 'required|integer|min:1'
        ]);
        if($validator->fails()){
//            return response()->json(['error'=>$validator->errors()->all()]);
            return Redirect::back()->withErrors($validator->errors());
        }else{
            $name = $input['name'];
            $email = $input['email'];
            $password = Hash::make($input['password']);
            $update = User::where('id',$id)->update(['name'=>$name,'email'=>$email,'password'=>$password]);
            if($update){
                Session::flash('message', "User Info Updated Successfully");
                return Redirect::back();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
