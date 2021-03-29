<?php

namespace Railroad\Railforums\Requests;

class DiscussionJsonIndexRequest extends FormRequest
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
            'amount' =>'nullable|numeric',
            'page' => 'nullable|numeric|min:1'
        ];
    }
}
