<?php

namespace App\Http\DTO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'required|in:user,operator,admin',
        ];
    }
}
