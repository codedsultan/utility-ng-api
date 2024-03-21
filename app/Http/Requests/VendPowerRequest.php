<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendPowerRequest extends FormRequest
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
            'meter_number' => 'required',
            'amount' => 'required|numeric|min:500',
            'disco' => 'required',
            'access_token' => 'required'
        ];
    }
}
