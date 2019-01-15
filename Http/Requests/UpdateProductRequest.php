<?php

namespace Modules\Imonitor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateProductRequest extends BaseFormRequest
{
    public function rules()
    {
        return [];
    }

    public function translationRules()
    {
        return ['title'=>'required|min:2'];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [
            'title.required' => trans('imonitor::common.messages.title is required'),
            'title.min:2'=> trans('imonitor::common.messages.title min 2 '),
        ];
    }
}
