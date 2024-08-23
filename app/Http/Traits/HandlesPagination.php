<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $results = $model->orderBy('created_at', $sort)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json($results);
    }
}
