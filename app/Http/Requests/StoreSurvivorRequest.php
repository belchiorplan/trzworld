<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurvivorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                 => 'required|min:3',
            'age'                  => 'required|integer',
            'gender_id'            => 'required|integer|exists:genders,id',
            'latitude'             => 'required|numeric',
            'longitude'            => 'required|numeric',
            'inventory'            => 'required|array',
            'inventory.*.item'     => 'required|integer|exists:inventory_items,id',
            'inventory.*.quantity' => 'required|integer',
        ];
    }
}
