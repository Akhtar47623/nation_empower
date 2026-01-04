<?php



namespace App\Http\Controllers\user;



use App\Http\Controllers\Controller;

use App\model\FavoriteJob;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;

use Validator;

use Session;



class FavoriteJobController extends Controller
{
    public function index() {

        $favorite = FavoriteJob::where('user_id',auth()->user()->id)->get();

        return view('admin.FavoriteList.index', compact('favorite'));

    }



    public function show($id) {
        $favorite = FavoriteJob::findOrFail($id);

        return view('admin.FavoriteList.view', compact('favorite'));

    }



    public function create() {

        return view('admin.FavoriteList.add');

    }



    public function store(Request $request) {

        $validator = Validator::make($request->all(), [

            'question' => 'required|string|max:255',

            'answer' => 'required|string|max:255',

        ], $this->messages, $this->attributes);



        if($validator->fails())

            return redirect()->back()->withErrors($validator)->withInput();

        $FAQ = new FAQ;

        $FAQ->question=$request->question;

        $FAQ->answer=$request->answer;

        $FAQ->save();

        return redirect('/admin/faq')->with('message', 'New FAQ is inserted!');

    }



    public function edit($id) {

        $FAQ_detail = FavoriteJob::findOrFail($id);

        return view('admin.FavoriteList.edit', compact('FAQ_detail'));

    }



    public function update(Request $request , $id) {

        $question = $request->question;

        $answer = $request->answer;

        FavoriteJob::where('id', $id)

        ->update([

            'question'=> $question,

            'answer'=> $answer,

        ]);

        return redirect('admin/faq')->with('message', 'FAQ Record is Updated!');

    }



    public function destroy($id) {

        FavoriteJob::destroy($id);

        Session::flash('flash_message', 'Removed From The Favorite List!');

        return redirect('panel/user/fav-job');

    }

}

