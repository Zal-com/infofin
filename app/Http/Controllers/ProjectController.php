<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request){

        $queryParams = $request->query();

        $arrayCheck = ["Name", "Deadline", "Deadline2", "Organisation", "ShortDescription", "TimeStamp"];

        if ($request->has("order")) {
            if (in_array($queryParams["order"], ["asc", "desc"])) {
                $orderDirection = $queryParams["order"];
            } else {
                return redirect("/projects");
            }
        }

        if ($request->has("field")) {
            if (in_array($queryParams["field"], $arrayCheck)) {
                $orderByColumn = $queryParams["field"];
            } else {
                return redirect("/projects");
            }
        }


        return view('projects.index');
    }

    public function show(Int $id){
      $project = Project::find($id);

      return view('projects.show', compact('project'));
    }

}

