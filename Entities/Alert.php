<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Imonitor\Presenters\AlertPresenter;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;

class Alert extends Model
{

    use PresentableTrait, NamespacedEntity;

    protected $table = 'imonitor__alerts';
    protected $fillable = ['product_id', 'record_id', 'status', 'user_id'];
    protected $cast = [
        'status' => 'int'
    ];
    protected $presenter =AlertPresenter::class;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    /**
     * Check if the post is in draft
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->whereStatus(Status::ACTIVE);
    }

    /**
     * Check if the post is in draft
     * @param Builder $query
     * @return Builder
     */
    public function scopeComplete(Builder $query)
    {
        return $query->whereStatus(Status::COMPLETE);
    }

    /**
     * Magic Method modification to allow dynamic relations to other entities.
     * @var $value
     * @var $destination_path
     * @return string
     */
    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['asgard.imonitor.config.relations.alert', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }
}
