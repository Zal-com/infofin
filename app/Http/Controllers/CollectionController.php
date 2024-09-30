<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function show(string $id)
    {
        $collection = Collection::find($id);
        return view('collections.show', compact('collection'));
    }
}
