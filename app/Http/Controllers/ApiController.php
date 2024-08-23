<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesPagination;
use App\Models\Continent;
use App\Models\Countries;
use App\Models\InfoType;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ScientificDomain;
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

    public function continents_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new Continent());
    }

    public function countries_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new Countries());
    }

    public function info_types_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new InfoType());
    }

    public function organisation_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new Organisation());
    }

    public function scientific_domains_index(Request $request): JsonResponse
    {
        return $this->paginateModel($request, new ScientificDomain());
    }

    public function show_project($id): JsonResponse
    {
        $project = Project::with($this->getFilteredModelRelationships(new Project()))
            ->findOrFail($id);
        return response()->json($project);
    }

    public function show_continent($id): JsonResponse
    {
        $continent = Continent::with($this->getFilteredModelRelationships(new Continent()))
            ->findOrFail($id);
        return response()->json($continent);
    }

    public function show_country($id): JsonResponse
    {
        $country = Countries::with($this->getFilteredModelRelationships(new Countries()))
            ->findOrFail($id);
        return response()->json($country);
    }

    public function show_info_type($id): JsonResponse
    {
        $infoType = InfoType::with($this->getFilteredModelRelationships(new InfoType()))
            ->findOrFail($id);
        return response()->json($infoType);
    }

    public function show_organisation($id): JsonResponse
    {
        $organisation = Organisation::with($this->getFilteredModelRelationships(new Organisation()))
            ->findOrFail($id);
        return response()->json($organisation);
    }

    public function show_scientific_domain($id): JsonResponse
    {
        $scientificDomain = ScientificDomain::with($this->getFilteredModelRelationships(new ScientificDomain()))
            ->findOrFail($id);
        return response()->json($scientificDomain);
    }
}
