<?php

namespace App\Http\Controllers;
use App\Models\Draft;

class DraftController extends Controller
{
    public function index()
    {
        return view('drafts.index');
    }

    public function show(int $id){
        $draft = Draft::find($id);

        if(!$draft){
            redirect()->back();
        }

        $project = $draft->content;
        $project["id"] = $draft->id;

        return view('drafts.show', compact('project'));
    }
}
