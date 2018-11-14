<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use Translatable;

    protected $table = 'imonitor__records';
    public $translatedAttributes = [];
    protected $fillable = [];
}
