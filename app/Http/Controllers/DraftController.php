<?php

namespace App\Http\Controllers;

class DraftController extends Controller
{
    public function index()
    {
        return view('drafts.index');
    }
}
