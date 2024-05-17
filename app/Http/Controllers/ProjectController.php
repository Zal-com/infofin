<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request){
        $itemsPerPage = 20;

        $queryParams = $request->query();

        $arrayCheck = ["Name", "Deadline", "Deadline2", "Organisation", "ShortDescription", "TimeStamp"];

        $orderByColumn = "TimeStamp";
        $orderDirection = "desc";

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

        $projects = Project::orderBy($orderByColumn, $orderDirection)->paginate($itemsPerPage);

        return view('projects.index', compact('projects', 'orderByColumn', 'orderDirection'), [
            'projects' => $projects,
        ]);
    }

    public function show(Int $id){
      return 0;
    }

}

