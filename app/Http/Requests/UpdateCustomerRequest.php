<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends ApiFormRequest
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
            'identification' => ['sometimes', 'required', 'numeric', Rule::unique('customers', 'identification')->ignore($this->route('id'))],
            'name'           => 'sometimes|required|string|max:255',
            'address'        => 'sometimes|required|string',
            'phone'          => 'sometimes|required|string',
            'detail'         => 'sometimes|nullable|string',
            'delinquent'     => 'sometimes|required|boolean',
            'quota'          => 'sometimes|required|integer|min:1',
            'interest'       => 'sometimes|required|numeric|min:0',
            'order'          => 'sometimes|required|integer',
            'loan_road_id'   => 'sometimes|required|exists:loan_roads,id',
        ];
    }
}
