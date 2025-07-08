<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use App\Models\Project;
use App\Models\VisitsRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function show(int $id, Request $request)
    {
        $project = Project::find($id) ?? abort(404);

        if (!Auth::user()->hasRole(['admin', 'contributor']) && $project->status === -1) {
            abort(404);
        }
        $project->timestamps = false;

        $qs = $request->query('from_email');

        if ($qs == "true") {
            $project->visit_count_email = $project->visit_count_email + 1;
        } else {
            $project->visit_count = $project->visit_count + 1;
        }

        VisitsRate::create(["project_id" => $id]);


        $project->saveQuietly();
        $project->timestamps = true;


        VisitsRate::create(["project_id" => $id]);

        return view('projects.show', [
            'project' => $project,
            'og_title' => $project->title,
            'og_description' => Str::limit($project->short_description, 150),
            'og_image' => asset('img/ulb_logo_simple.png'),
            'og_url' => route('projects.show', $project->id),
            'og_type' => 'website'
        ]);
    }

    public function create(Request $request)
    {
        if ($request->query('record')) {
            $draft = Draft::find($request->query('record'));
            if ($draft === null) {
                session()->flash('error-layout', "Vous n'êtes pas autorisé à effectuer cette action.");
                return redirect()->route('profile.show');
            }
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

