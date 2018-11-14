<?php

namespace Modules\Imonitor\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'imonitor__product_translations';
}
