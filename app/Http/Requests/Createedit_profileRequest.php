<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Createedit_profileRequest extends FormRequest
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
            'firstname' => 'required|min:3|max:20|alpha ',     
            'lastname' => 'required|min:3|max:20|alpha',
            'phone' => 'required|unique:users|numeric|regex:/(01)[0-9]{9}/',//, digits:11 in egypt it doesn,t put 0 in front of the number
            'birthdate' => 'required|date', 

        ];
    }
}
