<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInfectedReportedRequest extends FormRequest
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
        $infectedSurvivorId = $this->input('infected_survivor_id');
        $reportingSurvivorId = $this->input('reporting_survivor_id');

        return [
            'infected_survivor_id' => [
                'required',
                'integer',
                Rule::exists('survivors', 'id'),
                Rule::unique('infected_reporteds')->where(function ($query) use ($infectedSurvivorId, $reportingSurvivorId) {
                    return $query->where('infected_survivor_id', $infectedSurvivorId)
                                 ->where('reporting_survivor_id', $reportingSurvivorId);
                }),
            ],
            'reporting_survivor_id' => 'required|integer|exists:survivors,id',
        ];
    }
}
