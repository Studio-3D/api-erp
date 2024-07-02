<?php
namespace App\Traits;

trait PaginatesResults
{
    public function paginate($query, int $size, int $page): array
    {
        $results = $query->orderBy('created_at', 'desc')
            ->paginate($size, ['*'], 'page', $page);

        return [
            'items' => $results->items(),
            'pagination' => [
                'currentPage' => $results->currentPage(),
                'totalItems' => $results->total(),
                'totalPages' => $results->lastPage(),
            ]
        ];
    }
}