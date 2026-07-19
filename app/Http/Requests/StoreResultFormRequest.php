<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->staff !== null;
    }

    public function rules(): array
    {
        return [
            'interpretation'         => 'nullable|string',
            'parameters'             => 'required|array|min:1',
            'parameters.*.name'      => 'required|string',
            'parameters.*.value'     => 'required|string',
            'parameters.*.status'    => 'required|in:normal,high,low',
            'parameters.*.range'     => 'nullable|string',
            'consumables'            => 'nullable|array',
            'consumables.*.id'       => 'required|exists:consumables,id',
            'consumables.*.quantity' => 'required|integer|min:1',
            'equipment'              => 'nullable|array',
            'equipment.*'            => 'required|exists:equipment,id',
        ];
    }
}
