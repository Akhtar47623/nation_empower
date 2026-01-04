<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use Cviebrock\EloquentSluggable\Services\SlugService;

use App\model\HelpUs;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use Session;

use Validator;

use App\imagetable;



class HelpUsController extends Controller

{

    private $messages = [

        'title.required' => 'Provide a :attribute',
        'target.required' => 'Set Amount :attribute',

        'sub_title.required' => 'Provide a :attribute',

        'sub_title.max' => ':attribute can not exceed :max characters',

        'description.required' => 'Provide :attribute',

        'button_name.required' => 'Provide a :attribute',

        'button_link.required' => 'Provide a :attribute',

        'button_link.url' => ':attribute  must be a URL',

    ];

    private $attributes = [

        'title' => 'help Heading',

        'target' => 'Target',

        'sub_title' => 'Sub Title',

        'description' => 'Description',

        'button_name' => 'Button Name',

        'button_link' => 'Button Link',

    ];
    public function index() {
        $help_us = HelpUs::orderBy('id','DESC')->get();

        return view('admin.Help.index',compact('help_us'));

    }
    public function create() {

        return view('admin.Help.add');

    }
    public function check_slug(Request $request)
    {
        $slug = SlugService::createSlug(HelpUs::class , 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [

            'title' => 'required|string|max:255',
            'target' => 'required',

            'short_description' => 'required',

            'image_path' => 'required|mimes:jpeg,jpg,png|max:2000000',

            // 'button_link' => 'required',

        ], $this->messages, $this->attributes);
        if($validator->fails())
            return redirect()->back()->withErrors($validator->errors());
        $help = new HelpUs;
        $help->title = $request->title;
        $help->targer = $request->targer;
        $help->status = 1;
        $help->slug = $request->slug;
        $help->short_description = $request->short_description;
         if($request->hasfile('image_path'))
        {
            $file = $request->file('image_path');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('uploads/admin/help/'),$filename);
            $help->image_path = 'uploads/admin/help/'.$filename;
        }

        $help->save();

        return redirect('admin/help-us')->with('message', 'Data Has been inserted!');
    }
    public function edit($id)

    {
        $help_us = HelpUs::findOrFail($id);

        return view('admin.Help.edit',compact('help_us'));

    }

     public function show($id){

        $help_us=HelpUs::findOrFail($id);

        return view('admin.Help.view',compact('help_us'));

    }



    public function update(Request $request , $id)

    {



        $validator = Validator::make($request->all(), [



            'title' => 'required|string|max:255',

            // 'image_path' => 'mimes:jpeg,jpg,png|max:2000000'

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator->errors());

        $help = HelpUs::find($id);

        if ($request->title != null) {

            $help->title = $request->title;

        }
          if ($request->target != null) {

            $help->target = $request->target;

        }
        if ($request->slug != null) {

            $help->slug = $request->slug;

        }
         if ($request->short_description != null) {

            $help->short_description = $request->short_description;

        }

        if ($request->status != null) {

            $help->status = $request->status;

        }
        if ($request->hasfile('image_path')) {

            $destination = public_path().'/'.$help->image_path;

            if (File::exists($destination)) {

                File::delete($destination);

            }

            $file = $request->file('image_path');

            $extention = $file->getClientOriginalExtension();

            $filename = time().'.'.$extention;

            $file->move(public_path('uploads/admin/help/'),$filename);

            $help->image_path = 'uploads/admin/help/'.$filename;



        }

        $help->save();

        return redirect('admin/help-us')->with('message', 'Data Has Been Updated Successfully!');

    }

    public function destroy($id){

        HelpUs::destroy($id);

        Session::flash('flash_message','Data Has Been Deleted!');

        return redirect('admin/help-us');

    }



}

