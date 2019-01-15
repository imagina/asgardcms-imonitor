<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;

class Variable extends Model
{
    use Translatable,PresentableTrait, NamespacedEntity;

    protected $table = 'imonitor__variables';
    public $translatedAttributes = ['title','description'];
    protected $fillable = ['title','description'];

    /**
     * @return array
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'imonitor_product_variable')->withPivot('max_value', 'min_value');
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
