<?php

namespace Railroad\Railforums\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignatureCreateRequest extends FormRequest
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
            'signature' =>'required|string',
        ];
    }
}
