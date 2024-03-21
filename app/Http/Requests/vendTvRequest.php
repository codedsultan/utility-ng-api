<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class vendTvRequest extends FormRequest
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
            'smartcard_number' => 'required',
            'tv_network' => 'required',
            'service_code' => 'required',
            'access_token' => 'required',
            'reference_id'  => 'required'
        ];
    }
}
