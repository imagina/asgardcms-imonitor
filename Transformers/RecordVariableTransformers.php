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

class RecordVariableTransformers extends Resource
{

    public function toArray($request)
    {

        $data = [
            ($this->created_at) => $this->value,
        ];

        return $data;

    }


}