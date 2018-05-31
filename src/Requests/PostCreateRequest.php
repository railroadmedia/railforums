<?php

namespace Railroad\Railforums\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
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
            'content' => 'required|string',
            'prompting_post_id' => 'nullable|numeric|exists:forum_posts,id',
            'thread_id' => 'required|numeric|exists:' .
                config('railforums.database_connection_name') .
                '.forum_threads,id',
        ];
    }
}
