<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class test extends Controller
{
    public function index()
    {
        return view('test');
    }

    public function store(Request $request)
    {
        Storage::put("/test", $request->file('file'));
        return view('test');
    }
}
