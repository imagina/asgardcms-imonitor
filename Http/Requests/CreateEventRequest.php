<?php

namespace Modules\Imonitor\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreateEventRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name'=>'required',
            'product_id'=>'required'
        ];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'name.required' => trans('imonitor::events.messages.name is required'),
            'produtc_id.required' => trans('imonitor::events.messages.product is required'),
        ];
    }

    public function translationMessages()
    {
        return [];
    }
}
