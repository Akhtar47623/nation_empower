<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\model\NewsLetter;
use App\model\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Validator;
use Session;

class NewsController extends Controller
{
    private $messages = [
        'news_title.required' => 'Provide a :attribute',
        'news_title.max' => ':attribute can not exceed :max characters',
        'author.required' => 'Provide an :attribute',
        'news_description.required' => 'Provide :attribute',
        'news_image.required' => 'Provide :attribute',
        'news_image.mimes' => 'Provide:attribute of jpeg,jpg or png Type',
        'news_image.max' => 'Size of :attribute shold be less than 2 MBs',
    ];
    private $attributes = [
        'news_title' => 'News Title',
        'news_description' => 'News Description',
        'author' => 'Author Name',
        'service' => 'Service',
        'news_image' => 'News Image',
    ];
    public function index()
    {
        $news_details =NewsLetter::all();
        return view('admin.News.index',compact('news_details'));
    }

    public function create() {
        $services = Service::all();
        return view('admin.News.add',compact('services'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'news_title' => 'required|string|max:255',
            'news_description' => 'required',
            'author' => 'required|string|max:255',
            'service' => 'required',
            'news_image' => 'required|mimes:jpeg,jpg,png|max:2000000'
        ], $this->messages, $this->attributes);

        if($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $news = new NewsLetter;
        $news->title = $request->news_title;
        $news->description = $request->news_description;
        $news->author = $request->author;
        $news->service_id = $request->service;

        if($request->hasfile('news_image'))
        {
            $file = $request->file('news_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('uploads/admin/news/'),$filename);
            $news->news_image = 'uploads/admin/news/'.$filename;
        }
        $news->save();
        return redirect('/admin/news')->with('message', 'News Has been inserted!');
    }

    public function show($id)
    {
        $news = NewsLetter::findOrFail($id);
        $service = Service::where('id',$news->id)->first();
        return view('admin.News.view', compact('news','service'));
    }

    public function edit($id)
    {
        $news = NewsLetter::findOrFail($id);
        $services = Service::all();
        return view('admin.News.edit', compact('news','services'));
    }

    public function update(Request $request , $id)
    {
        $news = NewsLetter::find($id);
        $news->title = $request->news_title;
        $news->description = $request->news_description;
        $news->author = $request->author;
        $news->service_id = $request->service;
        if ($request->hasfile('news_image')) {
            $destination = 'uploads/admin/news/'.$news->news_image;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $file = $request->file('news_image');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move(public_path('uploads/admin/news/'),$filename);
            $news->news_image = 'uploads/admin/news/'.$filename;

        }
        $news->save();
        return redirect('admin/news')->with('message', 'News Info Has Been Updated Successfully!');
    }

    public function destroy($id)
    {
        Newsletter::destroy($id);
        Session::flash('flash_message', 'News Has Been Deleted!');
        return redirect('admin/newsletters');
    }
}

