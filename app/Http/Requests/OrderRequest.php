<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderRequest extends FormRequest
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
            'customer.firstname' => ['required', 'string', 'max:255'],
            'customer.lastname' => ['required', 'string', 'max:255'],
            'customer.phone' => ['required', 'numeric', 'min:11'],
            'customer.email' => ['required', 'string', 'email', 'max:255'],
            'delivery.method' => ['required', 'string', 'max:255'],
            'delivery.charge' => ['required_if:delivery.method,delivery'],
            'delivery.hour' => ['required_if:delivery.method,delivery'],
            'delivery.address' => ['required_if:delivery.method,delivery'],
            'delivery.city' => ['required_if:delivery.method,delivery'],
            'delivery.landmark' => ['required_if:delivery.method,delivery'],
            'payment.method' => ['required', 'string', 'max:255'],
            'store' => ['required', 'string', 'max:255'],
        ];
    }
    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'delivery.hour.required' => 'Please choose a delivery hour',
            'delivery.method.required' => 'Please choose a delivery method',
            'delivery.address.required_if' => 'Please input an address',
            'delivery.city.required_if' => 'Please input a city',
            'delivery.landmark.required_if' => 'Please enter a closest landmark',
            'payment.method.required' => 'Please choose a payment method',
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
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
