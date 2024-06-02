<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nome é obrigadatorio',
            'email.required' => 'Email é obrigadatorio',
            'email.email' => 'Email invalido',
            'password.min' => 'Senha deve ter no minimo 6 caracteres',
            'password.confirmed' => 'Senhas não conferem',
            
        ];
    
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(response()->json(
            ['error' => array_values($validator->errors()->getMessages())[0][0] ]
        ));
    }
}
