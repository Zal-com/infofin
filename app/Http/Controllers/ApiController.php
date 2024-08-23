<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesPagination;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use HandlesPagination;

    public function index(): JsonResponse
    {
        $apiVersion = config('app.api_version');
        $appName = config('app.name');

        $apiInfo = [
            'version' => $apiVersion,
            'name' => $appName,
            'description' => 'API to get standardized informations from the Infofin database. Infofin is a database gathering all the information on calls for projects, awards and distinctions, conference funding, and other opportunities within the Université libre de Bruxelles.',
            'documentation_url' => 'todo',
            'contact_email' => 'guillaume.stordeu@ulb.be',
            'status' => 'online',
        ];

        return response()->json($apiInfo);
    }

    public function projects_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new Project());
    }
}
