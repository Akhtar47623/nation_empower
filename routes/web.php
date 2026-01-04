<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\NotificationController;
use Cartalyst\Stripe\Laravel\StripeServiceProvider;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




// WEB ROUTES
Route::get('/', 'Web\HomeController@index')->name('webIndexPage');
Route::any('/about', 'Web\HomeController@about')->name('webAboutPage');
Route::any('/programs', 'Web\HomeController@Programs')->name('webProgramsPage');
Route::any('/program-detail', 'Web\HomeController@ProgramDetail')->name('webProgramDetailPage');
Route::any('/program-detail/{slug}', 'Web\HomeController@ProgramDetail');
Route::any('/events', 'Web\HomeController@Events')->name('webEventsPage');
Route::any('/events-detaill/', 'Web\HomeController@EventsDetails')->name('webEventsDetailPage');
Route::any('/events-detaill/{slug}', 'Web\HomeController@EventsDetails');
Route::any('/donate', 'Web\HomeController@Donate')->name('webDonatePage');
Route::any('/donate/{slug}', 'Web\HomeController@Donate');
Route::any('/schedule', 'Web\HomeController@Schedule')->name('webSchedulePage');
Route::any('/user-request', 'Web\HomeController@UserRequest')->name('userRequest');
Route::any('/booking', 'Web\HomeController@Booking')->name('webBookingPage');
Route::any('/contact', 'Web\HomeController@Contact')->name('webContactPage');
Route::any('/service-detail', 'Web\HomeController@serviceDetail')->name('webServiceDetailPage');
Route::any('/service-detail/{id}', 'Web\HomeController@serviceDetail');
Route::any('newsletter', 'Web\HomeController@newsletter')->name('newsletter');
Route::get('account/login', 'Web\HomeController@login')->name('accountLogin');

// WEB ROUTES
// Stripe

Route::post('donate','CheckoutController@checkout');
Route::any('checkout','CheckoutController@afterpayment')->name('checkout');
// Stripe
// Paypal
Route::get('create-transaction', 'PayPalController@createTransaction')->name('createTransaction');
Route::post('process-transaction', 'PayPalController@processTransaction')->name('processTransaction');
Route::get('success-transaction', 'PayPalController@successTransaction')->name('successTransaction');
Route::get('cancel-transaction', 'PayPalController@cancelTransaction')->name('cancelTransaction');

// Paypal




Route::get('logout','Auth\LoginController@logout')->name('logout');
Auth::routes();
Route::group(['middleware' => ['auth', 'roles'],'roles' => 'admin','prefix'=>'admin'], function (){
    Route::resource('channel', 'Admin\ChannelController');
    // Route::get('','Auth\LoginController@login')->name('UserLogin');
    Route::get('/','Admin\HomeController@dashboard');
    Route::any('config','Admin\ConfigController@update')->name('config_settings_update');
    Route::any('favicon','Admin\HomeController@faviconUpload')->name('favicon');
    Route::any('header-logo', 'Admin\HomeController@updateLogo')->name('StoreWebLogo');
    Route::any('footer-logo', 'Admin\HomeController@footerLogo')->name('StoreWebLogo');
    Route::any('account/profile','Admin\HomeController@profile')->name('profile');
    Route::get('event_slug', 'Admin\EventController@event_slug')->name('event_slug');
    Route::get('check_slug', 'Admin\HelpUsController@check_slug')->name('help_slug');
    Route::resource('banner', 'Admin\BannerController');
    Route::resource('home-slider', 'Admin\HomeSliderController');
    Route::resource('donation', 'Admin\DonationController');
    Route::resource('applications-content', 'Admin\ApplicationPageContentController');
    Route::resource('job-application', 'Admin\JobApplicationController'); 
    Route::resource('job-posting', 'Admin\JobPostingController'); 
    Route::resource('hiring-process','Admin\HiringProcessController');
    Route::resource('main-banner', 'Admin\MainBannerController');
    Route::resource('homepage','Admin\HomePageController');
    Route::resource('events-content','Admin\EventsContentController');
    Route::resource('homepage-content','Admin\HomepageContentController');
    Route::resource('services-content','Admin\ServicesContentController');
    Route::resource('services','Admin\ServicesController');
    Route::get('service_slug','Admin\ServicesController@serviceSlug')->name('service_slug');
    Route::post('trailer/upload','Admin\TrailerController@upload');
    Route::resource('trailer/delete','Admin\TrailerController@destroy');
    Route::resource('about-us','Admin\AboutUsController');
    Route::resource('contact-us','Admin\ContactUsController');
    Route::resource('application-content','Admin\ApplicationContentController');
    Route::resource('newsletters','Admin\NewsController');
    Route::any('newsletter/{id}','Admin\NewsController@destroy');
    Route::resource('inquiry','Admin\InquiryController');
    Route::resource('booking','Admin\BookingController');
    Route::resource('event','Admin\EventController');
    Route::resource('development-content','Admin\DevelopmentContentController');
    Route::resource('help-us','Admin\HelpUsController');
    Route::resource('schedule','Admin\ScheduleController');
    Route::resource('schedule-inquiry','Admin\ScheduleInquiryController');
    Route::resource('footer-menu','Admin\FooterController');
    Route::resource('users','Admin\UserController');
    Route::resource('counter-content','Admin\CounterContentController');

    Route::get('/users','Admin\HomeController@userDisplay');
    Route::get('/user-edit/{id}','Admin\HomeController@UserEdit');
    Route::post('/user/delete/{id}','Admin\HomeController@UserDelete');
    Route::put('/user-update/{id}','Admin\HomeController@UserUpdate');
    Route::get('/user/{id}','Admin\HomeController@ShowUser');

        Route::get('markAsRead/{id}', function($id){
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
         auth()->user()->notifications()->where('id', $id)->delete();
        return redirect()->back();
    });
    Route::get('markAsRead', function(){
        auth()->user()->unreadNotifications->take(5)->markAsRead();
         auth()->user()->notifications()->delete();
        return redirect()->back();
    });


});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['auth', 'roles'],'roles' => 'user','prefix'=>'panel/user'], function (){
    Route::resource('channel', 'Admin\ChannelController');

    Route::get('/','Admin\HomeController@dashboard');
    Route::any('account/profile','Admin\HomeController@profile')->name('profile');
    Route::any('invoice-pdf/{id}', array('as'=> 'generate.invoice.pdf', 'uses' => 'user\UserInvoiceController@generateInvoicePDF'))->name('invoice');

    Route::resource('orders', 'user\OrderController');
    Route::resource('fav-job', 'user\FavoriteJobController');

});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
