<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\model\Slider;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use Session;

use Validator;

use App\imagetable;



class SliderController extends Controller

{

    private $messages = [

        'image_path.required' => 'Provide :attribute',

        'image_path.mimes' => 'Provide:attribute of jpeg,jpg or png Type',

        'image_path.max' => 'Size of :attribute shold be less than 2 MBs',

    ];

    private $attributes = [

       

        'image_path' => 'Slider Image',

    ];



    public function index() {

        $sliders = Slider::where('page_name','HomePage')->get();

        return view('admin.slider.index',compact('sliders'));

    }



    public function create() {

        return view('admin.slider.add');

    }



    public function store(Request $request) {



        $validator = Validator::make($request->all(), [

            'image_path' => 'required|mimes:jpeg,jpg,png|max:2000000'

        ], $this->messages, $this->attributes);



        if($validator->fails())

        return redirect()->back()->withErrors($validator->errors());



        $slider = new Slider;

        $home_page='HomePage';

        $slider->page_name = $home_page;

        if($request->hasfile('image_path'))

        {

            $file = $request->file('image_path');

            $extention = $file->getClientOriginalExtension();

            $filename = time() . '.' . $extention;

            $file->move(public_path('uploads/admin/slider/'),$filename);

            $slider->image_path = 'uploads/admin/slider/'.$filename;

        }

        $slider->save();

        return redirect('panel/admin/slider')->with('message', 'Slider Has been inserted!');

    }



    public function edit($id)

    {

        $sliders = Slider::findOrFail($id);

        return view('admin.slider.edit',compact('sliders'));

    }

    public function show($id){

        $sliders=Slider::findOrFail($id);

        return view('admin.slider.view',compact('sliders'));

    }

    public function update(Request $request , $id)

    {



        $validator = Validator::make($request->all(), [

            'image_path' => 'mimes:jpeg,jpg,png|max:2000000'

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator->errors());

        $second_slider = Slider::find($id);

        if ($request->hasfile('image_path')) {

            $destination = public_path().'/'.$second_slider->image_path;

            if (File::exists($destination)) {

                File::delete($destination);

            }

            $file = $request->file('image_path');

            $extention = $file->getClientOriginalExtension();

            $filename = time().'.'.$extention;

            $file->move(public_path('uploads/admin/slider/'),$filename);

            $second_slider->image_path = 'uploads/admin/slider/'.$filename;



        }

        $second_slider->save();

        return redirect('panel/admin/slider')->with('message', 'Slider Info Has Been Updated Successfully!');

    }

    public function destroy($id){

        Slider::destroy($id);

        Session::flash('flash_message', 'Slider Has Been Deleted!');

        return redirect('panel/admin/slider');

    }



}

