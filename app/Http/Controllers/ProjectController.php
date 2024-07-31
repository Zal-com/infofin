<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\Project;
use App\Models\VisitsRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return view('projects.index');
    }

    public function preview()
    {
        return view('projects.preview');
    }

    public function show(int $id)
    {
        $project = Project::find($id);

        VisitsRate::create(["project_id" => $id]);

        $project->visit_count = $project->visit_count + 1;

        $project->save();

        return view('projects.show', compact('project'));
    }

    public function create(Request $request)
    {
        if ($request->query('record')) {
            $draft = Draft::find($request->query('record'));
            if ($draft === null || $draft->poster_id != Auth::id()) {
                session()->flash('error-layout', "Vous n'êtes pas autorisé à effectuer cette action.");
                return redirect()->route('profile.show');
            };
            return view('projects.create', compact('draft'));
        }
        return view('projects.create');

    }

    public function store(Request $request)
    {
        $validate = request()->validate([]);
        dd($validate);
    }

    public function edit(int $id)
    {
        $project = Project::find($id);

        return view('projects.edit', compact('project'));
    }

    public function archive()
    {
        return view('projects.archives');
    }

}

