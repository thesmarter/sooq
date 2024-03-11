<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;

class MainSiteController extends Controller
{
    public function index()
    {
        return view('website.home.index');
    }
}
