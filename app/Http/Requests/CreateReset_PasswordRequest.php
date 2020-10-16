<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReset_PasswordRequest extends FormRequest
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
            'token' => 'required',
            'email' => 'required|email|exists:users|unique:users',
            'password' => 'required|min:8', //confirmed|
            //'password_confirmation' => 'required',
        ];
    }

    //message
    public function message()
    {
        return
        [
            'email.required' => 'The email is required.',
            'email.email' => 'The email needs to have a valid format.',
            'email.exists' => 'The email is not registered in the system.',
        ];
    }
}
