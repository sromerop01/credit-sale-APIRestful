<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'           => 'sometimes|required|string|max:255',
            'email'          => 'sometimes|required|string|email|max:255|unique:users,email',
            'phone'          => 'sometimes|required|string',
            'password'       => 'sometimes|required|string|min:8|confirmed',
            'level'          => 'sometimes|required|string|in:administrador,vendedor,supervisor',
        ];
    }
}
