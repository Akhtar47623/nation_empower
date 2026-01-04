<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\Donate;
use Session;
use Validator;
use App\User;
use Notification;
use App\Notifications\CommentsNotification;

class CheckoutController extends Controller
{
    private $messages = [

        'amount.required' => ':attribute is required',
        'amount.numeric' => ':attribute must be Numeric',
        'first_name.required' => ':attribute is required',
        'first_name.regex' => ':attribute must be character',
        'last_name.required' => ':attribute is required',
        'last_name.regex' => ':attribute must be character',
        'email.required' => ':attribute is required',
        'email.regex' => 'Invalid Email Format',
        'country.required' => ':attribute is required',
        'country.regex' => ':attribute must be character',
        'phone_number.required' => ':attribute is required',

    ];

    private $attributes = [
        'amount' => 'Amount',
        'amount' => 'Amount',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'country' => 'Country',
        'phone_number' => 'Phone Number',

    ];

    public function checkout(Request $request)
    {
        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [

                'amount' => 'required|regex:/^[0-9]+$/',
                'first_name' => $request->anonymous === 'on' ? 'nullable' : 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'last_name' => $request->anonymous === 'on' ? 'nullable' : 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'email' => $request->anonymous === 'on' ? 'nullable' : 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'country' => $request->anonymous === 'on' ? 'nullable' : 'required',
                'phone_number' => $request->anonymous === 'on' ? 'nullable' : 'required|numeric',

            ], $this->messages, $this->attributes);

            if ($validator->fails()) {

                session::flash('error', $validator->errors()->first());
                return Redirect()->back()->withErrors($validator)->withInput();
            }

            // dd($request->all());
            // Enter Your Stripe Secret
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $amount = $request->amount;
            $amount *= 100;
            // $amount = (int) $amount;

            // dd($amount);
            $payment_intent = \Stripe\PaymentIntent::create([
                'description' => 'Stripe Test Payment',
                'amount' => $amount,
                'currency' => 'USD',
                'description' => 'Payment From Codehunger',
                'payment_method_types' => ['card'],
            ]);
            $intent = $payment_intent->client_secret;
            Session::put([

                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'title' => $request->title,
                'phone_number' => $request->phone_number,
                'country' => $request->country,
                'program' => $request->program,
                'address' => $request->address,
                'amount' => $request->amount,
                'transactionid' => $payment_intent->id,


            ]);



            // $first_name=$request->session()->put('first_name','here');
            // $last_name=session('last_name',$request->last_name);


            return view('front/paymentstripe', compact('intent'));
        }
    }

    public function afterPayment(Request $request)
    {

        $session_data = Session::all();
        $donate = new Donate();

        $donate->first_name = $session_data['first_name'];
        $donate->last_name = $session_data['last_name'];
        $donate->email = $session_data['email'];
        $donate->title = $session_data['title'];
        $donate->phone_number = $session_data['phone_number'];
        $donate->address = $session_data['address'];
        $donate->country = $session_data['country'];
        $donate->amount = $session_data['amount'];
        $donate->transactionid = $session_data['transactionid'];
        $donate->method = 'Stripe';

        $donate->save();
        $user = User::first();
        Notification::send($user, new CommentsNotification($donate));
        return redirect('/')->with('success', 'Thanks For Your Donations!');
        Session::flash();
    }
}
