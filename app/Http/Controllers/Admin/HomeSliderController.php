<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\model\Slider;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use Session;

use Validator;

use App\imagetable;



class HomeSliderController extends Controller

{

    private $messages = [

        'title.required' => 'Provide a :attribute',

        'sub_title.required' => 'Provide a :attribute',

        'sub_title.max' => ':attribute can not exceed :max characters',

        'description.required' => 'Provide :attribute',

        'button_name.required' => 'Provide a :attribute',

        'button_link.required' => 'Provide a :attribute',

        'button_link.url' => ':attribute  must be a URL',

     

    ];

    private $attributes = [

        'title' => 'Slider Heading',

        'sub_title' => 'Sub Title',

        'description' => 'Description',

        'button_name' => 'Button Name',

        'button_link' => 'Button Link',

    ];



    public function index() {
        $sliders = Slider::orderBy('id','DESC')->get();

        return view('admin.Slider.Home.index',compact('sliders'));

    }



    public function create() {

        return view('admin.Slider.Home.add');

    }



    public function store(Request $request) {



        $validator = Validator::make($request->all(), [

            'title' => 'required|string|max:255',

            // 'sub_title' => 'required|string|max:255',

            // 'bottom_text' => 'required',

            'description' => 'required',

            'button_name' => 'required',

            // 'button_link' => 'required',

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator->errors());



        $slider = new Slider;

        $slider->page_name = 'HomePage';

        $slider->title = $request->title;

        $slider->description = $request->description;

        $slider->button_name = $request->button_name;
         if($request->hasfile('image_path'))
        {
            $file = $request->file('image_path');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('uploads/admin/MainBanner/'),$filename);
            $slider->image_path = 'uploads/admin/MainBanner/'.$filename;
        }

        $slider->save();

        return redirect('admin/home-slider')->with('message', 'Slider Has been inserted!');

    }



    public function edit($id)

    {

        $sliders = Slider::findOrFail($id);

        return view('admin.Slider.Home.edit',compact('sliders'));

    }

     public function show($id){

        $sliders=Slider::findOrFail($id);

        return view('admin.Slider.Home.view',compact('sliders'));

    }



    public function update(Request $request , $id)

    {



        $validator = Validator::make($request->all(), [



            'title' => 'required|string|max:255',

            'image_path' => 'mimes:jpeg,jpg,png|max:2000000'

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator->errors());

        $slider = Slider::find($id);

        if ($request->title != null) {

            $slider->title = $request->title;

        }

        if ($request->sub_title != null) {

            $slider->sub_title = $request->sub_title;

        }

        if ($request->bottom_text != null) {

            $slider->bottom_text = $request->bottom_text;

        }

         if ($request->description != null) {

            $slider->description = $request->description;

        }

        if ($request->button_name != null) {

            $slider->button_name = $request->button_name;

        }

        if ($request->button_link != null) {

            $slider->button_link = $request->button_link;

        }

        if ($request->status != null) {

            $slider->status = $request->status;

        }





        if ($request->hasfile('image_path')) {

            $destination = public_path().'/'.$slider->image_path;

            if (File::exists($destination)) {

                File::delete($destination);

            }

            $file = $request->file('image_path');

            $extention = $file->getClientOriginalExtension();

            $filename = time().'.'.$extention;

            $file->move(public_path('uploads/admin/MainBanner/'),$filename);

            $slider->image_path = 'uploads/admin/MainBanner/'.$filename;



        }

        $slider->save();

        return redirect('admin/home-slider')->with('message', 'Slider Info Has Been Updated Successfully!');

    }

    public function destroy($id){

        Slider::destroy($id);

        Session::flash('flash_message','Main Slider Has Been Deleted!');

        return redirect('admin/home-slider');

    }



}

