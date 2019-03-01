<?php


namespace Modules\Imonitor\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EventTransformer extends Resource
{
    public function toArray($request){

        $data=[
            'id'=>$this->when($this->id,$this->id),
            'name'=>$this->when($this->name,$this->name),
            'value'=>$this->when($this->id,$this->present()->valueLabel),
            'label_class'=>$this->when($this->id,$this->present()->valueLabelClass),
            'created_at'=>$this->when($this->created_at,$this->created_at),
            'product'=>new ProductTransformer($this->whenLoaded('product')),
        ];

        return $data;
    }

}