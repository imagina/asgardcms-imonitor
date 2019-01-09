<?php

namespace Modules\Imonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\VariableRepository;
use Modules\User\Contracts\Authentication;
use Route;

class PublicController extends AdminBaseController
{
    public $product;
    public $variable;
    public $auth;


    public function __construct(Authentication $auth, ProductRepository $product, VariableRepository $variable)
    {

        $this->product = $product;
        $this->variable = $variable;
        $this->auth = $auth;

    }

    public function index()
    {
        $user = $this->auth->user();
        if ($this->auth->hasAccess('imonitor.products.index')) {
            $products = $this->product->paginate(12);
        } else {
            $products = $this->product->whereUser($user->id);

        }
        return view('imonitor::frontend.products.index', compact('products'));

    }

    public function show($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        if (($this->auth->hasAccess('imonitor.products.index')) ||($product->user_id == $user->id) ) {
            return view('imonitor::frontend.products.show', compact('product'));
        }
         else {
            return abort(404);
        }
    }

    public function historic($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        if (($this->auth->hasAccess('imonitor.products.index')) ||($product->user_id == $user->id)) {
            return view('imonitor::frontend.products.historic', compact('product'));
        } else {
            return abort(404);
        }
    }

}