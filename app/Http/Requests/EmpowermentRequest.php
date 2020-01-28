<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmpowermentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'min:11', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            "company" => ['required', 'string', 'max:255'],
            "dob" => ['required', 'string', 'max:255'],
            "gender" => ['required', 'string', 'max:255'],
            "city" => ['required', 'string', 'max:255'],
            "address" => ['required', 'string', 'max:255'],
            "state" => ['required', 'string', 'max:255'],
            "education" => ['required', 'string', 'max:255'],
            "interest" => ['required', 'string', 'max:255'],
            "country" => ['required', 'string', 'max:255']
        ];
    }
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['status' => false, 'errors' => $validator->errors()], 422));
    }
}
