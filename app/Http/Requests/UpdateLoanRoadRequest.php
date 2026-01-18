<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRoadRequest extends ApiFormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'detail' => 'sometimes|nullable|string',
            'start_date' => 'sometimes|required|date',
            'sales_commission' => 'sometimes|required|numeric',
            'length' => 'sometimes|required|numeric',
            'latitude' => 'sometimes|required|numeric',
            'inactive' => 'sometimes|required|boolean',
            'user_id' => 'sometimes|required|exists:users,id',
            'supervisor_id' => 'sometimes|required|exists:users,id',

        ];
    }
}
