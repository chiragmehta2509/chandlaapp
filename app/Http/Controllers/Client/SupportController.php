<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    public function plans()
    {
        return view('client.plans');
    }

    public function faq()
    {
        return view('client.faq');
    }

    public function contact()
    {
        return view('client.contact');
    }
}
