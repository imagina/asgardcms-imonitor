<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 21/11/2018
 * Time: 6:23 PM
 */

namespace Modules\Imonitor\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\User\Transformers\UserProfileTransformer;

class ProductTransformer extends Resource
{

    public function toArray($request)
    {

        $options = $this->options;
        unset($options->metatitle, $options->metadescription);
        $data = [
            'id' => $this->when($this->id, $this->id),
            'title' => $this->when($this->title, $this->title),
            'description' => $this->when($this->description, $this->description),
            'created_at' => $this->when($this->created_at, ($this->created_at)),
            'updated_at' => $this->when($this->updated_at, ($this->updated_at)),
            'variables' =>VariableTransformers::collection($this->whenLoaded('variables')),
            'records' => RecordTransformers::collection($this->whenLoaded('records')),
            //'alerts' => A::collections($this->whenLoaded('alerts')),

        ];


        return $data;

    }


}