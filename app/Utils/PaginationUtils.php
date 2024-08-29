<?php
namespace App\Utils;

use Illuminate\Http\Request;

class PaginationUtils
{
    public static function fromRequest(Request $request): array
    {
        $size = $request->input('size', config('app.default_item_number_perpage'));
        $page = $request->input('page', 1);

        return compact('size', 'page');
    }
}