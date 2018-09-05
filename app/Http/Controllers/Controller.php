<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function decamelize($string)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    public function makeFilterAndSortingQuery(Request $request, $queryParent, $relations)
    {
        $relDecamelized = [];

        foreach ($relations as $relation) {
            $relDecamelized[] = $this->decamelize($relation);
        }


        $sort_column = isset($request['sort_column']) ? $request['sort_column'] != '' ? $request['sort_column'] : null : null;
        $sort_type = isset($request['sort_type']) ? $request['sort_type'] != '' ? $request['sort_type'] : "desc" : "desc";


        if (isset($request['filters'])) {

            foreach ($request['filters'] as $filter) {
                $filter_column = $filter['filter_column'] != '' ? $filter['filter_column'] : null;
                $childName = '';
                if ($filter_column != null) {
                    $exploded = explode(".", $filter_column);
                    $childName = $exploded[sizeof($exploded) - 1];
                    $filter_column = $exploded[0];
                    $relation_column = "";
                    for ($i = 0; $i < sizeof($exploded) - 1; $i++) {
                        $camelized = camel_case($exploded[$i]);
                        $relation_column = $relation_column == "" ? $camelized : $relation_column . "." . $camelized;
                    }
                }

                $filter_value = $filter['filter_value'] != '' ? $filter['filter_value'] : '';

                if (in_array($filter_column, $relDecamelized)) {
                    $queryParent = $queryParent->whereHas($relation_column, function ($query) use ($filter_value, $childName) {
                        $query->where($childName, 'like', '%' . $filter_value . '%');
                    });
                } else if ($filter_column != null) {
                    $queryParent = $queryParent->where($filter_column, 'like', '%' . $filter_value . '%');
                }
            }
        }
        if ($sort_column != null) {
            $exploded = explode(".", $sort_column);
            $childName = $exploded[sizeof($exploded) - 1];
            $sort_column = $exploded[0];
            $relation_column = "";

            for ($i = 0; $i < sizeof($exploded) - 1; $i++) {
                $camelized = camel_case($exploded[$i]);
                $relation_column = $relation_column == "" ? $camelized : $relation_column . "." . $camelized;

            }

            if (in_array($sort_column, $relDecamelized)) {
                $queryParent = $queryParent->orderByJoin($relation_column . '.' . $childName, $sort_type);
            } else {
                $queryParent = $queryParent->orderByJoin($sort_column, $sort_type);
            }
        }
        return $queryParent;
    }

    public function extractIdsFromObject(Request $request, $columns)
    {
        foreach ($columns as $column) {
            if (key_exists($column, $request->all()))
                if (is_array($request[$column]))
                    if (key_exists('id', $request[$column]))
                        $request[$column] = $request[$column]['id'];
        }
        return $request;
    }
}
