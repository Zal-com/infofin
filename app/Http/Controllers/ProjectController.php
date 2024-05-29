<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request){
        return view('projects.index');
    }

    public function show(Int $id){
      $project = Project::find($id);

      return view('projects.show', compact('project'));
    }

}

