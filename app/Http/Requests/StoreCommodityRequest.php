<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommodityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $commodityId = $this->route('commodity');

        return [
            'name' => 'required|string|max:255|unique:commodities,name,' . ($commodityId ?? 'NULL'),
            'category' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ];
    }
}
