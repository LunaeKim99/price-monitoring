<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePriceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commodity_id' => 'required|integer|exists:commodities,id',
            'region_id' => 'required|integer|exists:regions,id',
            'price' => 'required|numeric|min:0',
            'recorded_date' => 'required|date',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ];
    }
}
