<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorPagesController extends Controller
{
    public function notFound(){
        return view('errors.404');
    }
}
