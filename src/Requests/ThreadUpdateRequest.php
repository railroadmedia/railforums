<?php

namespace Railroad\Railforums\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Railroad\Railforums\Services\ConfigService;

class ThreadUpdateRequest extends FormRequest
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
            'title' =>'min:1|string|max:255',
            'category_id' => 'required|numeric|exists:' .
                config('railforums.database_connection_name') .
                '.'.ConfigService::$tableCategories.',id',
        ];
    }
}
