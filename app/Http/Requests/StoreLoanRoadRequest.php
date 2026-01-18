<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRoadRequest extends ApiFormRequest
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
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'start_date' => 'required|date',
            'sales_commission' => 'required|numeric',
            'length' => 'required|numeric',
            'latitude' => 'required|numeric',
            'inactive' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
            'supervisor_id' => 'required|exists:users,id',
        ];
    }
}
