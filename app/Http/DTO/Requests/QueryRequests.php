<?php

namespace App\Http\DTO\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QueryRequests
{
    public static function validatePagination(Request $request): array
    {
        return Validator::make($request->query(), [
            'page' => ['required', 'integer', 'min:1'],
            'limit' => ['required', 'integer', 'min:1', 'max:50'],
        ])->validate();
    }
}