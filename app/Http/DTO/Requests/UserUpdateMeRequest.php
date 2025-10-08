<?php

namespace App\Http\DTO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateMeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }
}
