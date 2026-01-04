<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Validator;
use Session;
use App\User;
use Notification;
use App\Notifications\ApplicationNotification;
use App\model\Donate;

class PayPalController extends Controller
{
    /**
     * create transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function createTransaction()
    {

        return redirect('/');
    }

    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    private $messages = [
       
        'p_amount.required' => 'Amount is required',
        'p_amount.numeric' => 'Amount must be Numeric',
        'p_first_name.required' => ':attribute is required',
        'p_first_name.regex' => ':attribute must be character',
        'p_last_name.required' => ':attribute is required',
        'p_last_name.regex' => ':attribute must be character',
        'p_email.required' => ':attribute is required',
        'p_email.regex' => 'Invalid Email Format',
        'p_country.required' => ':attribute is required',
        'p_country.regex' => ':attribute must be character',
        'p_phone_number.required' => ':attribute is required',
        // 'p_phone_number.regex' => ':attribute must be ',
       
       
    ];

    private $attributes = [
        'p_amount' => 'Amount',
        'p_first_name' => 'First Name',
        'p_last_name' => 'Last Name',
        'p_email' => 'Email',
        'p_country' => 'Country',
        'p_phone_number' => 'Phone Number',

    ];
    public function processTransaction(Request $request)
    {

            $validator = Validator::make($request->all(),[

                'p_amount' => 'required|regex:/^[0-9]+$/',
                'p_first_name' => $request->p_anonymous === 'on' ? 'nullable': 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'p_last_name' => $request->p_anonymous === 'on' ? 'nullable': 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'p_email' => $request->p_anonymous === 'on' ? 'nullable': 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'p_country' => $request->p_anonymous === 'on' ? 'nullable': 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'p_phone_number' => $request->p_anonymous === 'on' ? 'nullable': 'required|numeric',

            ],$this->messages, $this->attributes);

            if($validator->fails()){
             
                session::flash('error',$validator->errors()->first());
                return Redirect()->back()->withErrors($validator)->withInput();
            }

    	
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('successTransaction'),
                "cancel_url" => route('cancelTransaction'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->p_amount,
                    ],

                ]
            ]
        ]);
         Session::put([

            'first_name'=> $request->p_first_name,
            'last_name'=> $request->p_last_name,
            'email'=> $request->p_email,
            'title'=> $request->title,
            'phone_number'=> $request->p_phone_number,
            'country'=> $request->p_country,
            // 'program'=> $request->program,
            'address'=> $request->p_address,
            'amount'=> $request->p_amount,
            'transactionid'=>$response['id'],


    ]);


        if (isset($response['id']) && $response['id'] != null) {
            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->route('createTransaction')
                ->with('error', 'Something went wrong.');

        } else {
            return redirect()
                ->route('createTransaction')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function successTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        $response2 = $response['purchase_units'];
        $response3 =$response2[0]['payments'];
        $response4=$response3['captures'];
        $transactionid= $response4[0]['id'];
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

       $session_data=Session::all();
		$donate =new Donate();
			
		$donate->first_name = $session_data['first_name'];
		$donate->last_name = $session_data['last_name'];
        $donate->email = $session_data['email'];
		$donate->title = $session_data['title'];
		$donate->phone_number = $session_data['phone_number'];
		$donate->address = $session_data['address'];
		$donate->country = $session_data['country'];
        $donate->amount = $session_data['amount'];
        $donate->transactionid = $transactionid;
		$donate->method = 'PayPal';

		$donate->save();
          $user=User::first();
                Notification::send($user, new ApplicationNotification($donate));
        return redirect('/')->with('success','Thanks For Your Donations!');
        Session::flash();
            // return redirect('/')
            //     ->route('createTransaction')
            //     ->with('success', 'Transaction complete.');
        } else {
            return redirect()
                ->route('createTransaction')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelTransaction(Request $request)
    {
        return redirect()
            ->route('createTransaction')
            ->with('error', $response['message'] ?? 'You have canceled the transaction.');
    }
}