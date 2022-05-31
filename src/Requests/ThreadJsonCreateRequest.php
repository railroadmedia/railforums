<?php

namespace Railroad\Railforums\Requests;

use Railroad\Railforums\Services\ConfigService;

class ThreadJsonCreateRequest extends FormRequest
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
            'title' =>'required|string|max:255',
            'first_post_content' => 'required|string',
            'category_id' => 'required|numeric|exists:' .
                config('railforums.database_connection_name') .
                '.'.ConfigService::$tableCategories.',id'
        ];
    }
}
