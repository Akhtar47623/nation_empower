<?php
namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File; 
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\model\Inquiry;
use App\model\CMS;
use App\model\HelpUs;
use App\model\EventGallery;
use App\model\Event;
use App\model\Slider;
use App\model\Banner;
use App\model\Development;
use App\model\ServiceGallery;
use App\imagetable;
use App\model\Service;
use App\model\Donate;
use App\model\NewsLetter;use Session;
use Alert;
use Auth;
use App\User;
use Notification;
use App\Notifications\OffersNotification;
use App\Notifications\ApplicationNotification;
use App\Notifications\NewsletterNotification;

class HomeController extends Controller
{
    
	 private $messages = [
        'first_name.required' => ':attribute is required',
        'name.required' => ':attribute is required',
        'name.required' => ':attribute is required',
        'name.regex' => 'Invalid :attribute Format',
        'first_name.regex' => ':attribute cannot contain numbers or special Char',
        'last_name.required' => ':attribute is required',
        'last_name.regex' => ':attribute cannot contain numbers or special Char',
        'email.required' => ':attribute is required',
        'email.email' => ':attribute must be in correct format',
        'phone.required' => ':attribute is required',
        'phone.numeric' => ':attribute must be Numeric',
        'phone.regex' => ':attribute must contain atleast 10 digits',
        'phone.max' => ':attribute should not more than 11 digits',
        'newsletter_email.required' => 'Subscription Email is required',
        'newsletter_email.unique' => 'Subscription Email already exist',
        'email.regex' => 'Invalid Email Format',
       
    ];

    private $attributes = [
        'name' => 'Name',
        'fname' => 'Full Name',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'street_address1' => 'Street Address',
        'dob' => 'Date of Birth',
        'resume' => 'File',
        'city' => 'City',
        'state' => 'State',
        'email' => 'Email',

    ];

	public function index(){

		$sliders=Slider::where('status',1)->orderBy('id','DESC')->get();
		$development=CMS::where('page_name','Development')->where('page_section','Info Section')->first();
        $child_content=CMS::where('page_name','Development')->where('page_section','Children Section')->first();
        $child_development=Development::where('development_id',$child_content->id)->get();
        $women_content=CMS::where('page_name','Development')->where('page_section','Women Section')->first();
        $women_development=Development::where('development_id',$women_content->id)->get();
        $youth_content=CMS::where('page_name','Development')->where('page_section','Youth Section')->first();
        $youth_development=Development::where('development_id',$youth_content->id)->get();
        $banners=Banner::where('page_name','HomePage')->orderBy('id','DESC')->first();
        $events=Event::where('status',1)->orderBy('id','DESC')->take(2)->get();
        $about_contents = CMS::where('page_name','Home Page')->where('page_section','About Us Section')->first();
        $about_info = CMS::where('page_name','About Us')->where('page_section','Info Section')->first();
        $counter_content = CMS::where('page_name','Counter')->where('page_section','Counter Section')->first();
        $services=Service::orderBy('id','DESC')->where('status',1)->get();
        $charity_sec = CMS::where('page_name','Home Page')->where('page_section','Charity Section')->first();
        $services_content = CMS::where('page_name','Home Page')->where('page_section','Services Section')->first();
        $help_us_content = CMS::where('page_name','Home Page')->where('page_section','Help Us Section')->first();
        $help_us=HelpUs::where('status',1)->orderBy('id','DESC')->get();
        $events_content=CMS::where('page_name','Events')->first();
       
		return view('front.index',compact('banners','sliders','child_content','child_development','development','women_content','women_development','youth_content','youth_development','events','about_contents','about_info','services','counter_content','charity_sec','help_us','services_content','help_us_content','events_content'));
	}
	public function about(){

		$about_details=CMS::where('page_name','About')->first();
		$about_contents = CMS::where('page_name','About Us')->where('page_section','About Section')->first();
        $about_info = CMS::where('page_name','About Us')->where('page_section','Info Section')->first();
        $banners=Banner::where('page_name','AboutUs')->orderBy('id','DESC')->first();
        $counter_content = CMS::where('page_name','Counter')->where('page_section','Counter Section')->first();
        $events=Event::where('status',1)->orderBy('id','DESC')->get();
        $events_content=CMS::where('page_name','Events')->first();
		return view('front.about',compact('about_details','banners','about_contents','about_info','counter_content','events','events_content'));
	}
    public function Programs(){
        $development=CMS::where('page_name','Development')->where('page_section','Info Section')->first();
        $child_content=CMS::where('page_name','Development')->where('page_section','Children Section')->first();
        $child_development=Development::where('development_id',$child_content->id)->get();
         $women_content=CMS::where('page_name','Development')->where('page_section','Women Section')->first();
        $women_development=Development::where('development_id',$women_content->id)->get();
         $youth_content=CMS::where('page_name','Development')->where('page_section','Youth Section')->first();
        $youth_development=Development::where('development_id',$youth_content->id)->get();
        $banners=Banner::where('page_name','Programs')->orderBy('id','DESC')->first();
        $counter_content = CMS::where('page_name','Counter')->where('page_section','Counter Section')->first();
        $services=Service::orderBy('id','DESC')->where('status',1)->get();
        return view('front.programs',compact('banners','child_content','child_development','development','women_content','women_development','youth_content','youth_development','counter_content','services'));
    }
    public function Events(){
        $banners=Banner::where('page_name','Events')->orderBy('id','DESC')->first();
        $events=Event::where('status',1)->orderBy('id','DESC')->get();
        $events_content=CMS::where('page_name','Events')->first();
        return view('front.events',compact('banners','events','events_content'));
    }
    public function EventsDetails($slug){
        $banners=Banner::where('page_name','Events Details')->orderBy('id','DESC')->first();
        $events=Event::where('slug',$slug)->first();
        $related_events=Event::where('status',1)->orderBy('id','DESC')->get();
        $events_gallery=EventGallery::where('event_id',$events->id)->get();
        return view('front.events-detail',compact('banners','events','events_gallery','related_events'));
    }
     public function ProgramDetail($slug){
         $development=CMS::where('page_name','Development')->where('page_section','Info Section')->first();
        $child_content=CMS::where('page_name','Development')->where('page_section','Children Section')->first();
        $child_development=Development::where('development_id',$child_content->id)->get();
         $women_content=CMS::where('page_name','Development')->where('page_section','Women Section')->first();
        $women_development=Development::where('development_id',$women_content->id)->get();
         $youth_content=CMS::where('page_name','Development')->where('page_section','Youth Section')->first();
        $youth_development=Development::where('development_id',$youth_content->id)->get();
        $banners=Banner::where('page_name','Program Detail')->orderBy('id','DESC')->first();
         $counter_content = CMS::where('page_name','Counter')->where('page_section','Counter Section')->first();
         $services=Service::where('slug',$slug)->first();
        return view('front.program-detail',compact('banners','child_content','child_development','development','women_content','women_development','youth_content','youth_development','counter_content','services'));
    }
    public function Donate(Request $request){
        $banners=Banner::where('page_name','Donate')->orderBy('id','DESC')->first();
        return view('front.donate',compact('banners'));
    }
	public function Service(){
		$banner=Banner::where('page_name','Services')->first();
		// $services_content=CMS::where('page_section','Services')->first();
        $services=Service::orderBy('id','DESC')->get();
		return view('front.service',compact('banner','services'));
	}
    public function serviceDetail($id){
        $banner=Banner::where('page_name','Service Detail')->first();   
        $services=Service::where('id',$id)->first();
        $gallery_images=ServiceGallery::where('product_id',$id)->orderBy('id','DESC')->get();
		return view('front.service-detail',compact('banner','services','gallery_images'));
	}
	
	public function contact(Request $request){
        if($request->method() == 'POST'){
            $validator = Validator::make($request->all(),[

                'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'last_name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
                'last_name' => 'required',
                'phone' => 'required|numeric|regex:/[0-9]{10}/',
                'service' => 'required',
                 'email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',

            ],$this->messages, $this->attributes);

            if($validator->fails()){

                session::flash('error',$validator->errors()->first());

                return Redirect()->back()->withErrors($validator)->withInput();
            }

           $inquiry =new Inquiry();
            $inquiry->first_name=$request->first_name;
			$inquiry->last_name=$request->last_name;
            $inquiry->email=$request->email;
            $inquiry->phone=$request->phone;
			$inquiry->service=$request->service;
            $inquiry->message=$request->message;
			$inquiry->save();

                $user=User::first();

            Notification::send($user, new OffersNotification($inquiry));
            
            // return response()->json(['success'=>'Successfully']);

            return redirect('contact')->with('success', 'Your Inquiry Has Been Submitted Successfully!');
        }
        $banners=Banner::where('page_name','Contact Us')->orderBy('id','DESC')->first();
        $contact_cms=CMS::where('page_name','Contact Us')->where('page_section','Contact Section')->first();
        return view('front.contact',compact('banners','contact_cms'));
    }

   
	public function register(){

		return view('front.register');
	}
	
	public function newsletter(Request $request){
		   if($request->method() == 'POST'){
            $request->validate([
                'newsletter_email' => 'required|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:newsletters,newsletter_email',
      ]);
                $newsletter=new NewsLetter();
                $newsletter->newsletter_email=$request->newsletter_email;
                $newsletter->save();
                  $user=User::first();
                Notification::send($user, new NewsletterNotification($newsletter));
                return response()->json([
                    'success'=> true,
                    'message' => 'Thanks for Your Subscription!'
                ]);
            } else {
                return response()->json([
                    'error'=> true,
                    'message'=> 'Visit Failed distance is too long'
                ]);
                     // return response()->json(['success'=>'Thanks for Your Subscription!']);
                // return redirect()->back()->with('success','Thanks for Your Subscription!');
        }
                return redirect()->back();
    }

        public function login(){

            return view('front.login');
        }
   


}