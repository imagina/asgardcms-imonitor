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

class ProductTransformers extends Resource
{

    public function toArray($request){

        $options = $this->options;
        unset($options->metatitle, $options->metadescription);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'variables' => $this->variables,
            'created_at' => ($this->created_at),
            'updated_at' => ($this->updated_at)
        ];
    }




}