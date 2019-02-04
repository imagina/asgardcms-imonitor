<?php

namespace Modules\Imonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Imonitor\Repositories\AlertRepository;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\VariableRepository;
use Modules\User\Contracts\Authentication;
use Route;

class PublicController extends AdminBaseController
{
    public $product;
    public $variable;
    public $auth;
    public $alert;


    public function __construct(Authentication $auth, ProductRepository $product, VariableRepository $variable, AlertRepository $alert)
    {

        $this->product = $product;
        $this->variable = $variable;
        $this->auth = $auth;
        $this->alert = $alert;
    }

    public function index()
    {
        $user = $this->auth->user();
        $alerts=count($this->alert->getItemsBy((object)['filter'=>['status'=>0],'include'=>[],'take'=>null]));
        $roleoperator=config('asgard.imonitor.config.roles.operator');

        if ($this->auth->hasAccess('imonitor.products.create')) {
            $products = $this->product->paginate(12);
        }
        else if ($user->inRole($roleoperator)){
            $products = $this->product->whereOperator($user->id);

        }else{
            $products = $this->product->whereUser($user->id);
        }
        return view('imonitor::frontend.products.index', compact('products','alerts'));

    }

    public function show($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        if (($this->auth->hasAccess('imonitor.products.index')) || ($product->user_id == $user->id || $product->operator_id == $user->id)) {
            return view('imonitor::frontend.products.show', compact('product'));
        } else {
            return abort(404);
        }
    }

    public function historic($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        if (($this->auth->hasAccess('imonitor.products.index')) || ($product->user_id == $user->id || $product->operator_id == $user->id)) {
            return view('imonitor::frontend.products.historic', compact('product'));
        } else {
            return abort(404);
        }
    }

    public function alertProduct($id)
    {
        $product = $this->product->find($id);
        $alerts = $this->alert->WhereByProduct($id);

        if ($this->auth->hasAccess('imonitor.products.index')) {
            return view('imonitor::frontend.alerts.index', compact('alerts', 'product'));
        } else {
            return abort(404);
        }
    }

    public function alerts()
    {
        $alerts = $this->alert->paginate(20);
        if ($this->auth->hasAccess('imonitor.products.index')) {
            return view('imonitor::frontend.alerts.index', compact('alerts'));
        } else {
            return abort(404);
        }
    }

    public function completeAlert($id)
    {
        $alert = $this->alert->find($id);

        $this->alert->update($alert, ['status' => 1]);
        return redirect()->back()
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('imonitor::alerts.title.alerts')]));

    }

    public function unique($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        if ($this->auth->hasAccess('imonitor.products.unique')) {
            return view('imonitor::frontend.products.unique', compact('product'));
        } else {
            return abort(404);
        }
    }

}