<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExchangeRequest extends FormRequest
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
            'survivor1_id'                 => 'required|integer|exists:survivors,id',
            'items_to_trade_s1'            => 'required|array|min:1',
            'items_to_trade_s1.*.item'     => 'required|exists:inventory_items,id',
            'items_to_trade_s1.*.quantity' => 'required|integer|min:1',
            'survivor2_id'                 => 'required|integer|exists:survivors,id',
            'items_to_trade_s2'            => 'required|array|min:1',
            'items_to_trade_s2.*.item'     => 'required|exists:inventory_items,id',
            'items_to_trade_s2.*.quantity' => 'required|integer|min:1',
        ];
    }
}
