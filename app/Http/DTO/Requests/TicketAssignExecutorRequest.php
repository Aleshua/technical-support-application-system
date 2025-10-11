<?php

namespace App\Http\DTO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketAssignExecutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'operatorId' => 'required|integer|min:1',
        ];
    }
}
