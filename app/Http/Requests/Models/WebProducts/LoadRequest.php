<?php

namespace OzSpy\Http\Requests\Models\WebProducts;

use Illuminate\Foundation\Http\FormRequest;

class LoadRequest extends FormRequest
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
            'offset' => 'numeric|min:0',
            'length' => 'numeric|min:1|max:100',
        ];
    }
}
