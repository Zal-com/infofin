<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionMethod;

trait HandlesPagination
{
    public function paginateModel(Request $request, Model $model): JsonResponse
    {
        $limit = $request->query('limit', 20);
        $offset = $request->query('offset', 0);
        $sort = $request->query('sort', 'ASC');

        $limit = is_numeric($limit) ? (int)$limit : 20;
        $offset = is_numeric($offset) ? (int)$offset : 0;
        $sort = strtoupper($sort) === 'DESC' ? 'DESC' : 'ASC';

        $relationships = $this->getFilteredModelRelationships($model);

        $results = $model->with($relationships)
            ->orderBy('created_at', $sort)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json($results);
    }

    protected function getFilteredModelRelationships(Model $model): array
    {
        $relationships = $this->getModelRelationships($model);

        return array_filter($relationships, function ($relationship) use ($model) {
            $relationInstance = $model->$relationship();
            return get_class($relationInstance->getRelated()) !== \App\Models\User::class;
        });
    }

    protected function getModelRelationships(Model $model): array
    {
        $reflection = new ReflectionClass($model);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $relationships = [];

        foreach ($methods as $method) {
            if ($method->class == get_class($model) && $method->getNumberOfParameters() === 0) {
                $returnType = $method->getReturnType();

                if ($returnType && is_subclass_of((string)$returnType, 'Illuminate\Database\Eloquent\Relations\Relation')) {
                    $relationships[] = $method->name;
                }
            }
        }

        return $relationships;
    }
}
