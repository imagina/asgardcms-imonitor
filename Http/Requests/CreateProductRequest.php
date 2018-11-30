<?php

namespace Modules\Imonitor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateProductRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'password'=>'required|min:8'
        ];
    }

    public function translationRules()
    {
        return [
            'title'=>'required|min:2'
        ];
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
            'password.required' => trans('imonitor::products.messages.password is required'),
            'password.min:2'=> trans('imonitor::products.messages.password min 8 '),
        ];
    }
}
