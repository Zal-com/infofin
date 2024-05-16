<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $total = 20;

        $queryParams = $request->query();

        if(is_null($queryParams)){
            $projects = Project::orderBy("TimeStamp", 'desc')->limit($total)->get();
        }else{
            
        }

        return view('list_projects', [
            'projects' => $projects
        ]);
    }
}
