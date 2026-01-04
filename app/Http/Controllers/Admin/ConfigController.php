<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\model\Config;
use App\imagetable;
use App\model\Slider;
use Validator;
use Session;

class ConfigController extends Controller
{

    private $messages = [
        'PHONE.required' => ':attribute is required!',
        'EMAIL.email' => ':attribute should be in mail format!',
        'EMAIL.required' => ':attribute is required!',
        'ADDRESS.required' => ':attribute is required!',
        'FACEBOOK.url' => ':attribute should in url format!',
        'FACEBOOK.required' => ':attribute link is Required!',
        'TWITTER.required' => ':attribute is Required!',
        'TWITTER.url' => ':attribute should in url format!',
        'INSTAGRAM.url' => ':attribute should in url format!',
        'INSTAGRAM.required' => ':attribute link is Required!',
        'COPYRIGHT.required'=> ':attribute is Required! ',
        'FLAT_SHIPPING.gt' => 'Provide Positive :attribute!',
        'FAX.required' =>'Provide :attribute!'

    ];
    private $attributes = [
        'PHONE' => 'Phone Number',
        'EMAIL' => 'Email',
        'ADDRESS' => 'Address ',
        'FACEBOOK' =>'Facebook',
        'TWITTER' =>'Twitter Link',
        'INSTAGRAM' =>'Instagram',
        'COPYRIGHT'=> 'Copyright',
        'FLAT_SHIPPING' => 'Shipping Price',
        'FAX' => 'FAX Number',

    ];

    public function update(Request $request)
    {
        if($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [                
                // 'ADDRESS' => 'required',
                // 'EMAIL' => 'required|email',
                // 'PHONE' => 'required',
                // 'COPYRIGHT' =>'required',
                // 'FACEBOOK' =>'required',

            ], $this->messages, $this->attributes);

            if($validator->fails()){
                session::flash('flash_message',$validator->errors()->first());
                return redirect()->back();
            }

            foreach($request->except(['_token']) as $index => $value) {
                // dd($index);
                $config = Config::where('flag_type', $index)->first();
                // dd($config);
                $config->flag_value = $value;
                $config->flag_additionalText = $value;
                $config->save();
            }
            session()->flash('message', 'Successfully Updated');
            return redirect('admin/config');
        }
        return view('admin.dashboard.index-config');
    }

}
