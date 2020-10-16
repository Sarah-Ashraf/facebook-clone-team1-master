<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSign_UpRequest extends FormRequest
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
            'firstname' => 'required|between:3,10',
            'lastname' => 'required|between:3,10',
            'password' => 'required|min:8',
            'gender' => 'required',
            'birthdate' => 'required',
            'email' => 'required|unique:users',
            //'phone' => 'unique|between:11,11', //required
        ];
    }

    //message
    public function message()
    {
        return
        [
            'email.unique' => 'email has an account',
            'email.email' => 'The email needs to have a valid format.',
        ];
    }
}
