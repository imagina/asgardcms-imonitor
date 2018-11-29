<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 15/11/2018
 * Time: 2:51 PM
 */

namespace Modules\Imonitor\Http\Controllers;

use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\VariableRepository;
use Illuminate\Http\Request;
use Route;

class PublicController extends BasePublicController
{
    public $product;
    public $variable;

    public function _construct(ProductRepository $product, VariableRepository $variable)
    {
        parent::__construct();
        $this->product=$product;
        $this->variable=$variable;
        
    }
    public function index(Request $request)
    {


        $oldVar=null;
        
        if ((isset($request->variables) && !empty($request->variables))){
            $filter=['variables'=>$request->variables];

            $products = $this->product->wherebyFilter($request->page,$take=12, json_decode(json_encode($filter)), $include=null);
            
            $oldVar=$request->variables;

        } else {
            $products = $this->place->paginate(12);
        }

        $variables = $this->variable->all();
        $tpl = 'imonitor::frontend.index';
        $ttpl = 'imonitor.frontend.index';

        if (view()->exists($ttpl)) $tpl = $ttpl;

        Return view($tpl, compact('products', 'variables','oldVar'));

    }
    
}