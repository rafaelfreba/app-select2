<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TesteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['required', 'numeric','min:5000','exists:products,id'],
        ];
    }
}
