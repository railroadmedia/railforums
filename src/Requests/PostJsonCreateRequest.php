<?php

namespace Railroad\Railforums\Requests;

class PostJsonCreateRequest extends FormRequest
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
            'content' =>'required|string',
            'prompting_post_id' => 'nullable|numeric|exists:forum_posts,id',
            'thread_id' => 'required|numeric|exists:forum_threads,id'
        ];
    }
}
