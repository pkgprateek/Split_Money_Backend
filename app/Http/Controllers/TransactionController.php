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
        $friends1 = \DB::table('Friends')->where('useremail', $request->email)->get();
        $friends2 = \DB::table('Friends')->where('friendemail', $request->email)->get();
        $friends = $friends1->merge($friends2);

        foreach($friends as $friend)
        {
        	$transactions1 = \DB::table('transactions')->where('user_id1', $request->email)->where('user_id2', $friend->friendemail)->get();

			$transactions2 = \DB::table('transactions')->where('user_id2', $request->email)->where('user_id1', $friend->friendemail)->get();        	
    	
        	$transactions = $transactions1->merge($transactions2);

        		$amount=0;

	    	$amount_1 = $transactions1->sum('credit1') + $transactions2->sum('credit2');
	    	$amount_2 = $transactions1->sum('credit2') + $transactions2->sum('credit1');
    		

	    	if($amount_1 > $amount_2)
	    	{
	    		$amount = $amount_1 - $amount_2;
	    	
	    		if($transactions1[0]->user_id1 == $request->email)
	    			return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
	    		
	    		elseif($transactions1[0]->user_id2 == $request->email)
	    			return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
	    	}

	    	elseif ($amount_1 < $amount_2) {
	    		$amount = $amount_2 - $amount_1;
	    		
	    		if($transactions1[0]->user_id1 == $friend->friendemail)
	    			return response()->json(["info" => "Owes you", "Amount" => $amount,"Transactions" => $transactions]);
	    		
	    		elseif($transactions1[0]->user_id2 == $friend->friendemail)
	    			return response()->json(["info" => "You owe", "Amount" => $amount,"Transactions" => $transactions]);
	    	}
	    	
	    	else {
	    		
	    		return response()->json(["info" => "Balance Settled", "Transactions" => $transactions]);
	    	}	
       
        }
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->option == 1)
        {
        	$user_pay1 = $request->amount;        	
        	$user_pay2 = 0;
        	$credit1 = 0;
        	$credit2 = $request->amount/2;
		}

		elseif($request->option == 2)
        {
        	$user_pay1 = 0;
        	$user_pay2 = $request->amount;
        	$credit2 = 0;
        	$credit1 = $request->amount/2;
		}

		elseif($request->option == 3)
        {
        	$user_pay1 = $request->amount;
        	$user_pay2 = 0;
        	$credit1 = 0;
        	$credit2 = $request->amount;
		}

		elseif($request->option == 4)
        {
        	$user_pay1 = 0;
        	$user_pay2 = $request->amount;
        	$credit2 = 0;
        	$credit1 = $request->amount;
		}

		$sql = \DB::table('Transaction')->insert(['expenditure' => $request->expenditure, 'user_id1' => $request->user_id1, 'user_id2' => $request->user_id2, 'user_pay1' => $request->user_pay1, 'user_pay2' => $request->user_pay2, 'option' => $request->option, 'credit1' => $request->credit1, 'credit2' => $request->credit2]);

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
    				$indi_trans[$i]['info'] = "You lent";
    				$indi_trans[$i]['amount'] = $transaction->credit1 - $transaction->credit2;
    			}

    			elseif ($transaction->credit2 > $request->credit1) 
    			{
    				$indi_trans[$i]['info'] = "You borrowed";
    				$indi_trans[$i]['amount'] = $transaction->credit2 - $transaction->credit1;
    			}

    			$i++;

    		}

    		elseif ($transaction->user_id1 == $request->user_id2)
    		{
    			if($transaction->credit1 > $transaction->credit2)
    			{
    				$indi_trans[$i]['info'] = "You borrowed";
    				$indi_trans[$i]['amount'] = $transaction->credit1 - $transaction->credit2;
    			}

    			elseif ($transaction->credit1 < $transaction->credit2) 
    			{
    				$indi_trans[$i]['info'] = "You lent";
    				$indi_trans[$i]['amount'] = $transaction->credit2 - $transaction->credit1;
    			}

    			$i++;
    		}

    	}
    	// $amount_1 = $transactions1->sum('credit1') + $transactions2->sum('credit2');
    	// $amount_2 = $transactions1->sum('credit2') + $transactions2->sum('credit1');
 		
 		dd($indi_trans);   	
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
