<?php

namespace App\Http\DTO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketCloseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'nullable|string|min:1',
        ];
    }
}
