<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\model\MainBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Session;
use Validator;
use App\imagetable;

class MainBannerController extends Controller
{
    private $messages = [
        'heading.required' => 'Provide a :attribute',
        'heading.max' => ':attribute can not exceed :max characters',
        'sub_title.required' => 'Provide a :attribute',
        'sub_title.max' => ':attribute can not exceed :max characters',
        'description.required' => 'Provide :attribute',
        'button_name.required' => 'Provide a :attribute',
        'button_name.string' => ':attribute must be in :string',
        'button_link.required' => 'Provide a :attribute',
        'button_link.url' => ':attribute  must be a URL',
        'image.required' => 'Provide :attribute',
        'image.mimes' => 'Provide:attribute of jpeg,jpg or png Type',
        'image.max' => 'Size of :attribute shold be less than 2 MBs',
    ];
    private $attributes = [
        'heading' => 'Slider Heading',
        'sub_title' => 'Sub Title',
        'description' => 'Description',
        'button_name' => 'Button Name',
        'button_link' => 'Button Link',
        'image' => 'Slider Image',
    ];

    public function index() {
        $banners = MainBanner::all();
        return view('admin.MainBanner.index',compact('banners'));
    }

    public function create() {
        return view('admin.MainBanner.add');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'banner_description' => 'required|string|max:255',
            'image' => 'required|mimes:jpeg,jpg,png|max:2000000'
        ], $this->messages, $this->attributes);

        if($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $banner = new MainBanner;

        $banner->title = $request->title;
        $banner->sub_title = $request->sub_title;
        $banner->banner_description = $request->banner_description;
        $banner->status=1;
        if($request->hasfile('image'))
        {
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('uploads/admin/MainBanner/'),$filename);
            $banner->image_path = 'uploads/admin/MainBanner/'.$filename;
        }
        $banner->save();
        return redirect('/admin/main-banner')->with('message', 'Main Banner Has been inserted!');
    }

    public function edit($id)
    {
        $banner = MainBanner::findOrFail($id);
        return view('admin.MainBanner.edit',compact('banner'));
    }

    public function update(Request $request , $id)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'image_path' => 'mimes:jpeg,jpg,png|max:2000000',
            'banner_description' => 'required|string|max:255',

        ], $this->messages, $this->attributes);

        if($validator->fails())
            return redirect()->back()->withErrors($validator->errors());
        $banner = MainBanner::find($id);
        if ($request->title != null) {
            $banner->title = $request->title;
        }
        if ($request->sub_title != null) {
            $banner->sub_title = $request->sub_title;
        }
        if ($request->banner_description != null) {
            $banner->banner_description = $request->banner_description;
        }
        if ($request->button_name != null) {
            $banner->button_name = $request->button_name;
        }
        if ($request->button_link != null) {
            $banner->button_link = $request->button_link;
        }
         if ($request->status != null) {
            $banner->status = $request->status;
        }

        if ($request->hasfile('image')) {
            $destination = public_path().'/'.$banner->image_path;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $file = $request->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move(public_path('uploads/admin/MainBanner/'),$filename);
            $banner->image_path = 'uploads/admin/MainBanner/'.$filename;

        }
        $banner->save();
        return redirect('admin/main-banner')->with('message', 'Main Banner Has Been Updated Successfully!');
    }


    public function destroy($id) {

        MainBanner::destroy($id);

        Session::flash('flash_message', 'Banner Has Been Deleted!');

        return redirect('/admin/main-banner');

    }

}
