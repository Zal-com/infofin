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
    
        if ($request->has("by")) {
            if (in_array($queryParams["by"], ["asc", "desc"])) {
                $orderDirection = $queryParams["by"];
            } else {
                return redirect("/projects");
            }
        }
    
        if ($request->has("in")) {
            if (in_array($queryParams["in"], $arrayCheck)) {
                $orderByColumn = $queryParams["in"];
            } else {
                return redirect("/projects");
            }
        }
    
        $projects = Project::orderBy($orderByColumn, $orderDirection)->paginate($itemsPerPage);
    
        return view('list_projects', [
            'projects' => $projects
        ]);
    }
}

