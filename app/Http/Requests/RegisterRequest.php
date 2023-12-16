<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|unique:admins,email',
            'username' => 'required|unique:admins,username',
            'address' => 'required',
            'phone_number' => 'required|max:16',
            'job' => 'required|max:14',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ];
    }
}
