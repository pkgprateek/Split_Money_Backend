<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Transaction;
use Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $sql = User::where('email', $request['email'])->get();
        $sql = json_decode($sql);

        if(!$sql)
        {
            $user = \DB::table('users')->insert([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'password'          => bcrypt($request->password),
                    'contact_no'        => $request->email."".$request->name,
                    ]);
                return response()->json("registered");       
        }
        else
        {
                return response()->json("User already exists. Please Login!!!!");       
        }
        
    }

    public function login(Request $request)
    {
    	
    	$sql = User::where('email', $request['email'])->get()->first();
    	$sql = json_decode($sql);

    	if(!$sql)
    	{
            return response()->json(['name'=>'null', 'info'=>"User not registered"]);
	    }
        else{
                if (Auth::attempt(['email'=>$request->email,'password'=>$request->password]))
                {
                    return response()->json(['name'=>$sql->name, 'info'=>'Fuck Yeah']);
                }

                else
                    return response()->json(['name'=>'null', 'info'=>'Incorrect Credentials']);
            }
     	
    }

    public function add_friend(Request $request)
    {
    	$sql = \DB::table('friends')->where('friendemail', $request->user_id2)->get();
    	$sql = json_decode($sql);

    	if(!$sql)
    	{
    		$friend = \DB::table('friends')->insert([
                    'useremail'         => $request->user_id1,
                    'username'          => $request->u_name,
	                'friendemail'  		=> $request->user_id2,
	                'friend_name'       => $request->f_name,
	                ]);
    	
    		return response()->json("Friend Added");	
    	}
    	
    	return response("Friend Already Added");
    }

    public function showfriends(Request $request)
    {
        $sql = \DB::table('friends')->where('useremail', $request->useremail)->get(array('friend_name'));
        $sql = json_decode($sql);

        return response()->json($sql);
    }

    public function edit(Request $request)
    {
        $sql = \DB::table('friends')->where('friendemail', $request->user_id2)->first();
        $sql = json_decode($sql);

        if(!$sql)
        {
            $friend = \DB::table('friends')->where('friendemail', $request->user_id2)->where('friendemail', $request->user_id2)->update([
                    'friendemail'       => $request->new_email,
                    ]);
        
            return response()->json(['name'=>'null', 'info'=>'Incorrect Credentials']); 
        }

        return response("Friend not present.");
    }

}
