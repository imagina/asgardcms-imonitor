<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Imonitor\Presenters\EventPresenter;

class Event extends Model
{
    use PresentableTrait, NamespacedEntity;

    protected $table = 'imonitor__events';
    protected $fillable = ['product_id','name','value'];

    protected $presenter = EventPresenter::class;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
