<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'barcode'            => ['nullable', 'string', 'max:50'],   // ✅
            'default_quantity'   => ['nullable', 'numeric', 'min:0'],
            'default_unit'       => ['nullable', 'string', 'max:20'],
            'default_pack_size'  => ['nullable', 'integer', 'min:1'],
            'location_id'        => ['nullable', 'exists:locations,id'],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ];
    }
}
