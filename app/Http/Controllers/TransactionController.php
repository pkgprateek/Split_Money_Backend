<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $friends1 = \DB::table('friends')->where('useremail', $request->email)->get();
        // $friends2 = \DB::table('friends')->where('friendemail', $request->email)->get();
        $friends = $friends1;
        
        $i=0;

        $r_array = array();
            
        foreach($friends as $friend)
        {
            $transactions1 = \DB::table('transactions')->where('user_id1', $request->email)->where('user_id2', $friend->friendemail)->get();

			$transactions2 = \DB::table('transactions')->where('user_id2', $request->email)->where('user_id1', $friend->friendemail)->get();        	
    	
        	$transactions = $transactions1->merge($transactions2);

        		$amount=0;

	    	$amount_1 = $transactions1->sum('credit1') + $transactions2->sum('credit2');
	    	$amount_2 = $transactions1->sum('credit2') + $transactions2->sum('credit1');
    		

            // dd($transactions2);
	    	if($amount_1 > $amount_2)
	    	{
	    		$amount = $amount_1 - $amount_2;
	    	
	    		if($transactions[0]->user_id1 == $request->email)
                {
                    $r_array[$i]['f_name'] = $friend->friend_name;
                    $r_array[$i]['f_email'] = $friend->friendemail;
                    $r_array[$i]['f_info'] = "You Owe";
                    $r_array[$i]['f_amount'] = $amount; 
	    		
                	// return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
                }
	    		
                elseif($transactions[0]->user_id2 == $request->email)
	    	    {
                    $r_array[$i]['f_name'] = $friend->friend_name;
                    $r_array[$i]['f_email'] = $friend->friendemail;
                    $r_array[$i]['f_info'] = "Owes You";
                    $r_array[$i]['f_amount'] = $amount; 

                	// return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
	    	    }
            }

	    	elseif ($amount_1 < $amount_2) {
	    		$amount = $amount_2 - $amount_1;
	    		
	    		if($transactions[0]->user_id1 == $request->email)
                {
                    $r_array[$i]['f_name'] = $friend->friend_name;
                    $r_array[$i]['f_email'] = $friend->friendemail;
                    $r_array[$i]['f_info'] = "Owes you";
                    $r_array[$i]['f_amount'] = $amount; 
                
	    			// return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
                }
	    		
	    		elseif($transactions[0]->user_id2 == $request->email)
                {
	    		     $r_array[$i]['f_name'] = $friend->friend_name;
                    $r_array[$i]['f_email'] = $friend->friendemail;
                    $r_array[$i]['f_info'] = "You owe";
                    $r_array[$i]['f_amount'] = $amount; 
                
                	// return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
                }
	    	}
	    	
	    	else {
	    		
	    		    $r_array[$i]['f_name'] = $friend->friend_name;
                    $r_array[$i]['f_email'] = $friend->friendemail;
                    $r_array[$i]['f_info'] = "Balance Settled";
                    $r_array[$i]['f_amount'] = $amount; 
                

                // return response()->json(["info" => "Balance Settled", "Transactions" => $transactions]);
	    	}	
                
                $i++;
        }
         return response()->json($r_array);
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user_pay1=0;
        $user_pay2=0;
        $credit1=0;
        $credit2=0;
        $option = 1;
        if($request->option == "one")
        {
        	$user_pay1 = $request->amount;        	
        	$user_pay2 = 0;
        	$credit1 = 0;
        	$credit2 = $request->amount/2;
	        $option = 1;
    	}

		elseif($request->option == "two")
        {
        	$user_pay1 = 0;
        	$user_pay2 = $request->amount;
        	$credit2 = 0;
        	$credit1 = $request->amount/2;
		    $option = 2;
        }

		elseif($request->option == "three")
        {
        	$user_pay1 = $request->amount;
        	$user_pay2 = 0;
        	$credit1 = 0;
        	$credit2 = $request->amount;
		    $option = 3;
        }

		elseif($request->option == "four")
        {
        	$user_pay1 = 0;
        	$user_pay2 = $request->amount;
        	$credit2 = 0;
        	$credit1 = $request->amount;
            $option = 4;
		}

		$sql = \DB::table('transactions')->insert(['expenditure' => $request->expenditure, 'user_id1' => $request->user_id1, 'user_id2' => $request->user_id2, 'user_pay1' => $user_pay1, 'user_pay2' => $user_pay2, 'option' => $option, 'credit1' => $credit1, 'credit2' => $credit2]);
        
        return response()->json("success");


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
    public function show(Request $request)
    {
    	$transactions1 = \DB::table('transactions')->where('user_id1', $request->user_id1)->where('user_id2', $request->user_id2)->get();
    	$transactions2 = \DB::table('transactions')->where('user_id2', $request->user_id1)->where('user_id1', $request->user_id2)->get();

    	$transactions = \DB::table('transactions')->where('user_id1', $request->user_id1)->where('user_id2', $request->user_id2)->orwhere('user_id1', $request->user_id2)->where('user_id2', $request->user_id1)->get();

    	// dd($transactions);
    	
    	$indi_trans = array();
        $i=0;

    	foreach ($transactions as $transaction) {
    		
    		if($transaction->user_id1 == $request->user_id1)
    		{
    			if($transaction->credit1 > $transaction->credit2)
    			{
    				$indi_trans[$i]['name'] = $transaction->expenditure;
                    $indi_trans[$i]['info'] = "You borrowed";
    				$indi_trans[$i]['amount'] = $transaction->credit1 - $transaction->credit2;
    			}

    			elseif ($transaction->credit2 > $request->credit1) 
    			{
    				$indi_trans[$i]['name'] = $transaction->expenditure;
                    $indi_trans[$i]['info'] = "You lent";
    				$indi_trans[$i]['amount'] = $transaction->credit2 - $transaction->credit1;
    			}

    			$i++;

    		}

    		elseif ($transaction->user_id1 == $request->user_id2)
    		{
    			if($transaction->credit1 > $transaction->credit2)
    			{
    				$indi_trans[$i]['name'] = $transaction->expenditure;
                    $indi_trans[$i]['info'] = "You borrowed";
    				$indi_trans[$i]['amount'] = $transaction->credit1 - $transaction->credit2;
    			}

    			elseif ($transaction->credit1 < $transaction->credit2) 
    			{
    				$indi_trans[$i]['name'] = $transaction->expenditure;
                    $indi_trans[$i]['info'] = "You lent";
    				$indi_trans[$i]['amount'] = $transaction->credit2 - $transaction->credit1;
    			}

    			$i++;
    		}

    	}
    	// $amount_1 = $transactions1->sum('credit1') + $transactions2->sum('credit2');
    	// $amount_2 = $transactions1->sum('credit2') + $transactions2->sum('credit1');
 		
 		// dd($indi_trans);   	
    	// $transactions = $transactions1->merge($transactions2); 
    	// if($amount_1 > $amount_2)
    	// {
    	// 	$amount = $amount_1 - $amount_2;
    		
    	// 	if($transactions1[0]->user_id1 == $request->user_id1)
    	// 		return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
    		
    	// 	elseif($transactions1[0]->user_id2 == $request->user_id1)
    	// 		return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
    	// }

    	// elseif ($amount_1 < $amount_2) {
    	// 	$amount = $amount_2 - $amount_1;

    	// 	if($transactions1[0]->user_id1 == $request->user_id2)
    	// 		return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
    		
    	// 	elseif($transactions1[0]->user_id1 == $request->user_id1)
    	// 		return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
    	// }
    	// else {
    		
    	// 	return response()->json(["info" => "Balance Settled", "Transactions" => $transactions]);
    	// }

        return response()->json($indi_trans);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
