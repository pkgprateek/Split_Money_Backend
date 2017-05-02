<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Transaction;

class UserController extends Controller
{
    public function register(Request $request)
    {
    	
    	$sql = User::where('email', $request['email'])->get();
    	$sql = json_decode($sql);
    	
    	if(!$sql)
    	{
	    	$user = \DB::table('users')->insert([
	    			'f_id'        		=> $request->f_id,
	                'name'       		=> $request->name,
	                'email'       		=> $request->email,
	                'contact_no'  		=> $request->f_id."".$request->name,
	                ]);
	           
	    }
     	return response()->json("Welcome to SplitMoney");

    }

    public function add_friend(Request $request)
    {
    	$sql = \DB::table('friends')->where('contact_no', $request->contact_no)->get();
    	$sql = json_decode($sql);

    	if(!$sql)
    	{
    		$friend = \DB::table('friends')->insert([
	                'contact_no'  		=> $request->contact_no,
	                'name'       		=> $request->name,
	                ]);
    	
    		return response()->json(['contact_no' => $request->contact_no, 'name' => $request->name]);	
    	}
    	
    	return response("Friend Already Added");
    }

    public function showfriends(Request $request)
    {
        $sql = \DB::table('friends')->where('useremail', $request->useremail)->get(array('friend_name', 'friendemail'));
        $sql = json_decode($sql);

        return response()->json($sql);
    }

}
