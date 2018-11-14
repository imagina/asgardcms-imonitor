<?php

namespace Modules\Imonitor\Entities;

use Illuminate\Database\Eloquent\Model;

class RecordTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'imonitor__record_translations';
}
