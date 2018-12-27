<?php

namespace Modules\Imonitor\Entities;


use Illuminate\Database\Eloquent\Model;

class Record extends Model
{

    protected $table = 'imonitor__records';

    protected $fillable = ['variable_id', 'product_id', 'value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variable()
    {
        return $this->belongsTo(Variable::class);
    }

}
