<?php

use App\Http\Controllers\WEB\MainSiteController;
use Illuminate\Support\Facades\Route;

Route::get('site/home', [MainSiteController::class, 'index']);
