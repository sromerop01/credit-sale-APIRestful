<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends ApiFormRequest
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
            'identification' => 'required|numeric|unique:customers,identification',
            'name'           => 'required|string|max:255',
            'address'        => 'required|string',
            'phone'          => 'required|string',
            'detail'         => 'nullable|string',
            'delinquent'     => 'required|boolean',
            'quota'          => 'required|integer|min:1',
            'interest'       => 'required|numeric|min:0',
            'order'          => 'required|integer',
            'loan_road_id'   => 'required|exists:loan_roads,id',
        ];
    }
}
