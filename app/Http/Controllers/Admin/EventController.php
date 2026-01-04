<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\model\Event;
use App\model\EventGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Validator;
use Session;

class EventController extends Controller
{
    private $messages = [
        'name.required' => 'Provide  :attribute',
        'designation.required' => 'Provide :attribute',
        'description.required' => 'Provide :attribute',
        'rating.required' => 'Provide :attribute',
        'slug.required' => 'Provide :attribute',
        'image_path.mimes' => 'Provide:attribute of jpeg,jpg or png Type',
        'image_path.required' => 'Provide :attribute',
        'image_path.mimes' => 'Provide:attribute of jpeg,jpg or png Type',
        
    ];
    private $attributes = [
        'name' => 'Name',
        'designation' => 'Designation',
        'description' => 'Description',
        'rating' => 'Rating',
        'image_path' => 'Image',
        'slug' => 'Event Slug',

        
    ];
    public function index() {
        $events = Event::orderBy('id','DESC')->get();
        return view('admin.Events.index', compact('events'));
    }

    public function show($id) {
        $events = Event::findOrFail($id);
        return view('admin.Events.view', compact('events'));
    }

    public function create() {
        return view('admin.Events.add');
    }
    public function event_slug(Request $request)
    {
        $slug = SlugService::createSlug(Event::class , 'slug', $request->title);
        return response()->json(['slug' => $slug]);
    }

    public function store(Request $request) {
           $validator = Validator::make($request->all(), [
            'title' => 'required',
            'event_description' => 'required',
            'organizer' =>'required',
            'start_date' =>'required',
            'end_date' =>'required',
            'day' => 'required',
            'time' => 'required',
            'service' => 'required',
            'location' => 'required',
            // 'image_path' => 'required',
        
        ], $this->messages, $this->attributes);

        if($validator->fails())
        return redirect()->back()->withErrors($validator)->withInput();
        $events = new Event;
        $events->title=$request->title;
        $events->slug = $request->slug;
        $events->event_description=$request->event_description;
        $events->additional_info=$request->additional_info;
        $events->organizer=$request->organizer;
        $events->start_date=$request->start_date;
        $events->end_date=$request->end_date;
        $events->day=$request->day;
        $events->time=$request->time;
        $events->location=$request->location;
        $events->service=$request->service;
        $events->status='1';
        if($request->hasfile('image_path'))
        {
            $file = $request->file('image_path');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move(public_path('uploads/admin/events/'),$filename);
            $events->image_path = $request->image_path;
            $events->image_path = 'uploads/admin/events/'.$filename;
        }
        $events->save();
         if($request->hasfile('gallery_image'))
         {

            foreach($request->file('gallery_image') as $image)
            {
                $gallery_extension=$image->getClientOriginalExtension();
                $imageName = rand() . '.' . $gallery_extension;
                $image->move(public_path('uploads/admin/events/gallery/'), $imageName);  
                $event_gallery['image_path'] = "$imageName";
                  EventGallery::create([
                'event_id'=>$events->id,
                'image_path' => 'uploads/admin/events/gallery/'.$imageName,
            ]);
            }
         }
        return redirect('/admin/event')->with('message', 'Event Has Been Added!');
    }

    public function edit($id) {
        $events = Event::findOrFail($id);
        $event_gallery = EventGallery::where('event_id',$id)->orderBy('id','DESC')->take(4)->get();
        return view('admin.Events.edit', compact('events','event_gallery'));
    }

    public function update(Request $request , $id) {
           $validator = Validator::make($request->all(), [
            'title' => 'required',
            'day' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'organizer' => 'required',
            'time' => 'required',
            'event_description' => 'required',
            'service' => 'required',
            'location' => 'required',
            // 'image_path' => 'required',
        
        ], $this->messages, $this->attributes);

        if($validator->fails())
            return redirect()->back()->withErrors($validator->errors());
        $title = $request->title;
        $slug = $request->slug;
        $event_description = $request->event_description;
        $event_description = $request->event_description;
        $additional_info= $request->additional_info;
        $organizer = $request->organizer;
        $organizer = $request->organizer;
        $start_date = $request->start_date;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $day = $request->day;
        $service = $request->service;
        $time = $request->time;
        $location = $request->location;
        $status = $request->status;
        
        $validArr = array();
        if($request->file('image_path')) {
            $validArr['image_path'] = 'required|mimes:jpeg,jpg,png,gif|required|max:10000';
        }
        $this->validate($request, $validArr);
        $requestData = $request->all();
        $events = Event::where('id', $id)->first();
        if($request->hasfile('image_path'))
        {
            $destination2 = 'uploads/admin/events/'.$events->image_path;
            if (File::exists($destination2)) {
                File::delete($destination2);
            }
            $file2 = $request->file('image_path');
            $extention2 = $file2->getClientOriginalExtension();
            $filename2 = time().'1.'.$extention2;
            $file2->move(public_path('uploads/admin/events/'),$filename2);
            Event::where('id', $id)
                ->update(['image_path' => 'uploads/admin/events/'.$filename2,'slug'=>$slug,'title'=> $title,'day'=> $day,'service' => $service,'time' => $time,'location' => $location,'event_description' => $event_description,'additional_info' => $additional_info,'organizer' => $organizer,'start_date' => $start_date,'end_date' => $end_date,'status' => $status]);
        }
        else{
            Event::where('id', $id)
                ->update([
                    'title'=> $title,
                    'slug'=> $slug,
                    'event_description'=> $event_description,
                    'additional_info'=> $additional_info,
                    'organizer'=> $organizer,
                    'start_date'=> $start_date,
                    'end_date'=> $end_date,
                    'day'=> $day,
                    'service' => $service,
                    'time' => $time,
                    'location' => $location,
                    'status' => $status
                ]);
        }
         if($request->hasfile('gallery_image'))
         {
            $gallary_images = EventGallery::all()->where('event_id',$id);
            foreach($gallary_images as $image){
                $gallery_image_path = public_path().'/'.$image->image_path;
                if (File::exists($gallery_image_path)) {
                    File::delete($gallery_image_path);
                    $image->delete();
                }
            }  
            foreach($request->file('gallery_image') as $image)
            {
                $image_extention=$image->getClientOriginalExtension();
                $imageName=rand().'1.'.$image_extention;
                $image->move(public_path('uploads/admin/events/gallery/'), $imageName);  
                $service_gallery['image_path'] = "$imageName";
                EventGallery::create([
                'event_id'=>$id,
                'image_path' => 'uploads/admin/events/gallery/'.$imageName,
            ]);
            }
         }
        return redirect('admin/event')->with('message', 'Event Has Been Updated!');
    }

    public function destroy($id) {
        Event::destroy($id);
        Session::flash('flash_message', 'Event Has Been Deleted!');
        return redirect('admin/event');
    }
}
