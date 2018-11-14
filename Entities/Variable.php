<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use Translatable;

    protected $table = 'imonitor__variables';
    public $translatedAttributes = [];
    protected $fillable = [];
}
