<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\model\Donate;
use Illuminate\Http\Request;
use Session;

class DonationController extends Controller
{
    public function index()
    {
        $donations =Donate::orderBy('id', 'DESC')->get();
        return view('admin.Donation.index', compact('donations'));
    }

    public function show($id)
    {
        $donations =Donate::findOrFail($id);
        return view('admin.Donation.view',compact('donations'));
    }

    public function destroy($id)
    {
        Donate::destroy($id);
        Session::flash('flash_message', 'Donation Record Has Been Deleted!');
        return redirect('admin/donation');
    }
}

