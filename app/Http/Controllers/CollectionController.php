<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function create(Request $request)
    {
        return view('collection.create');

    }
}
