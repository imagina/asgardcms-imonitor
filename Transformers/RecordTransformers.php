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

class RecordTransformers extends Resource
{

    public function toArray($request){

        return [
            'id' => $this->id,
            'variable_id' => $this->variable_id,
            'product_id' => $this->product_id,
            'value' => $this->value,
            'created_at' => ($this->created_at),
            'updated_at' => ($this->updated_at)
        ];
    }




}