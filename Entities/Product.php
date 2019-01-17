<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;

class Product extends Model
{
    use Translatable, PresentableTrait, NamespacedEntity;

    protected $table = 'imonitor__products';
    public $translatedAttributes = ['title','description'];
    protected $fillable = ['title','description','address','variable_id','user_id','product_user_id', 'operator_id'];
    protected $fakeColumns = ['options'];

    protected $cast = [
        'options' => 'array',
        'variable_id'=>'int'
        ];

    public function user()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }
    public function productUser()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User", 'product_user_id');
    }
    public function operator()
    {
        $driver = config('asgard.user.config.driver');
        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }
    public function variables()
    {
        return $this->belongsToMany(Variable::class, 'imonitor_product_variable')->withPivot('max_value', 'min_value');
    }
    public function records()
    {
        return $this->hasMany(Record::class);
    }
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
    public function getAlersativesAttribute(){
        return $this->alerts->where('status',0)->all();
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
