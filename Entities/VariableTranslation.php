<?php

namespace Modules\Imonitor\Entities;

use Illuminate\Database\Eloquent\Model;

class VariableTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title','description'];
    protected $table = 'imonitor__variable_translations';
}
