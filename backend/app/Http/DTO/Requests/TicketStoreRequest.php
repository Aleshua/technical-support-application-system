<?php

namespace App\Http\DTO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ];
    }
}
