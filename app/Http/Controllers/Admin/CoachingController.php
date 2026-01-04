<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\model\Coaching;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use Validator;

use Session;



class CoachingController extends Controller

{

    private $messages = [

        'title.required' => 'Provide a :attribute',

        'title.max' => ':attribute can not exceed :max characters',

        'description.required' => 'Provide :attribute',

        'image.required' => 'Provide :attribute',

        'image.mimes' => 'Provide:attribute of jpeg,jpg or png Type',

        'image.max' => 'Size of :attribute shold be less than 2 MBs',

    ];

    private $attributes = [

        'title' => 'Coahcing Title',

        'description' => 'Description',

        'image' => 'Image',

    ];

    public function index() {

        $coachings = Coaching::all();

        return view('admin.coaching.index', compact('coachings'));

    }



    public function create() {

        return view('admin.coaching.add');

    }



    public function store(Request $request) {



        $validator = Validator::make($request->all(), [

            'title' => 'required|string|max:255',

            'description' => 'required',

            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000000',

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator)->withInput();



        $coaching = new Coaching;

        $coaching->title = $request->title;

        $coaching->slug = str_slug($request->title);

        if ($request->short_description != null)

            $coaching->short_description = $request->short_description;

        $coaching->description = $request->description;

        if($request->hasfile('image'))

        {

            $file = $request->file('image');

            $extention = $file->getClientOriginalExtension();

            $filename = time() . '.' . $extention;

            $file->move(public_path('uploads/admin/coaching/'),$filename);

            $coaching->image_path = 'uploads/admin/coaching/'.$filename;

        }

        $coaching->save();

        return redirect('/admin/coaching')->with('message', 'New Coaching Info is inserted!');

    }



    public function edit($id) {

        $coaching_detail = Coaching::findOrFail($id);

        return view('admin.coaching.edit', compact('coaching_detail'));

    }



    public function show($id) {

        $coaching_detail = Coaching::findOrFail($id);

        return view('admin.coaching.view', compact('coaching_detail'));

    }



    public function update(Request $request , $id) {

        $title = $request->title;

        $short_description = $request->short_description;

        $description = $request->description;



        $imagetable = Coaching::where('id', $id)->first();

        if($request->hasfile('image'))

        {

            $destination2 = 'uploads/admin/coaching/'.$imagetable->image_path;

            if (File::exists($destination2)) {

                File::delete($destination2);

            }

            $file2 = $request->file('image');

            $extention2 = $file2->getClientOriginalExtension();

            $filename2 = time().'1.'.$extention2;

            $file2->move(public_path('uploads/admin/coaching/'),$filename2);

            Coaching::where('id', $id)

            ->update([

                'image_path' => 'uploads/admin/coaching/'.$filename2,

                'title'=> $title,

                'short_description' => $short_description,

                'description' => $description

            ]);

        }

        else{

            Coaching::where('id', $id)

            ->update([

                'title'=> $title,

                'short_description' => $short_description,

                'description' => $description

            ]);

        }

        return redirect('admin/coaching')->with('message', 'Coaching detail has been Updated!');

    }



    public function destroy($id) {

        Coaching::destroy($id);

        Session::flash('flash_message', 'Coaching Has Been Deleted!');

        return redirect('admin/coaching');

    }

}

