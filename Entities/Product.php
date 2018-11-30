<?php

namespace Modules\Imonitor\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Imonitor\Entities\Variable;
use Modules\Core\Traits\NamespacedEntity;
use Laracasts\Presenter\PresentableTrait;

class Product extends Model
{
    use Translatable, PresentableTrait, NamespacedEntity;

    protected $table = 'imonitor__products';
    public $translatedAttributes = ['title','description'];
    protected $fillable = ['title','description','address','variable_id','user_id','product_user_id'];
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
    public function variables()
    {
        return $this->belongsToMany(Variable::class, 'imonitor_product_variable');
    }

}
