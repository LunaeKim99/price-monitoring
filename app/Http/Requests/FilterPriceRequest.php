<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commodity_id' => 'nullable|integer|exists:commodities,id',
            'region_id' => 'nullable|integer|exists:regions,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ];
    }
}
