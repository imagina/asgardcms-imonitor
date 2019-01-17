<?php

namespace Modules\Imonitor\Entities;


use Illuminate\Database\Eloquent\Model;

class Record extends Model
{

    protected $table = 'imonitor__records';

    protected $fillable = ['variable_id', 'product_id', 'value','client_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variable()
    {
        return $this->belongsTo(Variable::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function client()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }
}
