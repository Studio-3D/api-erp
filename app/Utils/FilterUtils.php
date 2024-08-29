<?php
namespace App\Utils;

class FilterUtils
{
    public static function fromRequest($request, array $allowedFilters)
    {
        $filters = [];
        foreach ($allowedFilters as $filter) {
            if ($request->filled($filter)) {
                $filters[$filter] = $request->input($filter);
            }
        }
        return $filters;
    }
}
