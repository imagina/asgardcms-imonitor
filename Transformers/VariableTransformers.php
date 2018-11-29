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

class VariableTransformers extends Resource
{

    public function toArray($request)
    {

        //  $dateformat= config('asgard.iplace.config.dateformat');
        $options=$this->options;
        unset($options->metatitle,$options->metadescription);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'mainimage' => $this->mainimage,
            'metatitle'=>$this->metatitle??$this->title,
            'metadescription'=>$this->metadescription,
            'metakeywords'=>$this->metakeywords,
            'options' => $options,
            'created_at' => ($this->created_at),
            'updated_at' => ($this->updated_at)
        ];
}

}