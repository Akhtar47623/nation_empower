<?php
namespace App\Http\Controllers\cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;
use Illuminate\Support\Facades\Validator;
use App\model\Banner;
use App\model\Slider;
use App\model\Product;
use App\model\Donate;
use App\model\Order;
use App\model\OrderDetails;
use Session;
use Auth;
class CartController extends Controller

{
	private $messages=[

		'first_name.required' =>'Provide Your :attribute',

		'last_name.required' =>'Provide Your :attribute',

		'email.required' =>'Provide Your :attribute',

		'phone_number.required' =>'Provide Your :attribute',

		'country.required' =>'Mention Your :attribute Name',

		'amount.required' =>'Provide Your :attribute',

		'address.required' =>'Provide Your :attribute',


	];

	private $attributes=[

		'first_name' =>'First Name',

		'last_name' =>'Last Name',

		'email' =>'Email Address',

		'phone_number' =>'Phone Number',

		'country' =>'Country',

		'amount' =>'Amount',

		'address' =>'Address',


	];

	public function index(Request $request){
				$quantity = (isset($request->quant[1])) ? $request->quant[1] : 1;
				$cartData = \Cart::session()->add(array(
				    // 'id' => uniqid(),
				    'amount' => $request->amount,
				));
				if($cartData)
				{
					return redirect()->route('AddToCart');
				}

			}
		}
		// if (Auth::check()) {
		// 	$cart = \Cart::session(auth()->user()->id)->getContent(auth()->user()->id);
		// 	$products_details=Product::where('status',1)->first();
		// 	$sliders=Slider::where('page_name','Cart')->orderBy('id','DESC')->get();
		// 	$banners=Banner::where('page_name','Cart')->first();
		// 	$lastIndex = 0;
		// 	foreach ($cart as $key => $value) {
		// 		$lastIndex = $value->id;
		// 	}
		// 	if (count($cart) > 0) {
		// 		return view('front.cart',compact('banners','sliders','products_details','cart','lastIndex'));
		// 	}
		// 	else{
		// 		return redirect()->route('webIndexPage')->with('error','Your Cart Is Empty');	
		// 	}

		// }
			return redirect()->route('webIndexPage')->with('error','Login Before Proceed');
	}

	 public function updateCart(Request $request){

	 	if($request->quantity > 0){

	 		\Cart::session(auth()->user()->id)->update($request->id,
            array(

                	'quantity' => array(

                    'relative' => false,

                    'value' => $request->quantity,

                )

            ));

			return json_encode(1);
		}

	}

	public function remove($id){

		\Cart::session(auth()->user()->id)->remove($id);

		return redirect()->back()->with('message','Product Has Been Removed From Cart');

	}

	public function checkout(Request $request){


		if($request->method() == 'POST'){

			$FileAddedToCart = \Cart::session(auth()->user()->id)->update($request->last_index, array(

				'subtotal' => $request->sub_total,

				'total' => $request->total,

				'shipping_price' => $request->shipping_price,

			));

			if ($FileAddedToCart) {

				return redirect()->route('webCheckoutPage');

			}

		}

		$sliders=Slider::where('page_name','Checkout')->orderBy('id','DESC')->get();

		$banners=Banner::where('page_name','Checkout')->first();

		$cart = \Cart::session(auth()->user()->id)->getContent(auth()->user()->id);

		$countries = Country::all();

		if (count($cart) > 0) {

			return view('front.checkout',compact('banners','sliders','cart','countries'));

		}

		else{

			return redirect()->route('webIndexPage')->with('message','Your Order Has Been Placed Successfully');

		}

	}

	public function placeOrder(Request $request){

	$validator=Validator::make($request->all(),[

		'first_name' => 'required|string|max:255',

		'last_name' => 'required|string|max:255',

		'email' => 'required|email',

		'phone_number' => 'required|numeric',

		'country' => 'required',

		'amount' => 'required',

		'address' => 'required',



	], $this->messages, $this->attributes);



	if($validator->fails())

		return redirect()->back()->withErrors($validator)->withInput();

		$cart = \Cart::session(auth()->user()->id)->getContent(auth()->user()->id);

		$donate = new Donate();

		// $donate->number = uniqid();

		// $donate->user_id = auth()->user()->id;

		$donate->first_name = $request->first_name;

		$donate->last_name = $request->last_name;

		$donate->email = $request->email;

		$donate->phone_number = $request->phone_number;

		$donate->country = $request->country;

		// $donate->city = $request->city;

		$donate->address = $request->address;

		// $donate->address2 = $request->address2;

		$donate->amount = $request->amount;

		$donate->save();

		$cart_quantity = 0;

		$shipping_price = 0;

		$sutotal = 0;

		$cart = \Cart::session(auth()->user()->id)->getContent(auth()->user()->id);

		foreach ($cart as $key => $value) {

			$shipping_price = $value->shipping_price;

			$sutotal = $value->subtotal;

			$donate_detail = new OrderDetails();

			$donate_detail->user_id = auth()->user()->id;

			$donate_detail->product_name = $value->name;

			$donate_detail->quantity =  $value->quantity;

			$donate_detail->total_price = $value->price;

			$donate_detail->sku = $value->attributes['sku'];

			$donate_detail->order_id = $donate->id;

			$donate_detail->save();
			// Update the quantity after purchasing products
			$quantity_db = Product::where('sku',$value->attributes['sku'])->first('quantity');

			$quantity_update = $quantity_db->quantity - $value->quantity;

			Product::where('sku',$value->attributes['sku'])
			->update([
				'quantity' => $quantity_update,
			]);
			// end of update quantity

			\Cart::session(auth()->user()->id)->remove($value->id);

			$cart_quantity ++;	

		}

		Order::where('id', $donate->id)
            ->update([
                'quantity' => $cart_quantity,
                'shipping_price' => $shipping_price,
                'subtotal' => $sutotal,
            ]);
            return redirect('panel/user/orders');
        }

}
