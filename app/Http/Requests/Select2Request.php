<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class Select2Request extends FormRequest
{
    public function authorize(): bool
    {
        // return $this->user() !== null;
        return true;
    }

    public function rules(): array
    {
        $allowedModels = array_keys(config('select2.models'));

        return [
            'model' => ['required', 'string', 'max:50', Rule::in($allowedModels)],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'model' => strtolower($this->route('model')),
        ]);
    }
}
