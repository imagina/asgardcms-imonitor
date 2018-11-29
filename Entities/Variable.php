<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\NamespacedEntity;
use Laracasts\Presenter\PresentableTrait;

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
      return $this->belongsToMany(Product::class,  'imonitor_product_variable');
  }
  
}
