<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id'    => ['required', 'exists:products,id'],
            'location_id'   => ['nullable', 'exists:locations,id'],
            'quantity'      => ['required', 'numeric', 'min:0'],
            'unit'          => ['nullable', 'string', 'max:20'],
            'min_quantity'  => ['nullable', 'numeric', 'min:0'],
            'expires_at'    => ['nullable', 'date'],
            'is_open'       => ['boolean'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ];
    }
}
