<?php

namespace Modules\Imonitor\Http\Controllers;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Imonitor\Emails\ExportExcel;
use Modules\Imonitor\Emails\SendAlertClient;
use Modules\Imonitor\Http\Controllers\Export\ProductsExport;
use Modules\Imonitor\Repositories\AlertRepository;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\RecordRepository;
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
        $alerts = count($this->alert->getItemsBy((object)['filter' => ['status' => 0], 'include' => [], 'take' => null]));
        $roleoperator = config('asgard.imonitor.config.roles.operator');

        if ($this->auth->hasAccess('imonitor.products.create')) {
            $products = $this->product->paginate(12);
        } else if ($user->inRole($roleoperator)) {
            $products = $this->product->whereOperator($user->id);

        } else {
            $products = $this->product->whereUser($user->id);
        }
        return view('imonitor::frontend.products.index', compact('products', 'alerts'));

    }

    public function show($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        $event=$product->events->last();

        if (($this->auth->hasAccess('imonitor.products.index')) || ($product->user_id == $user->id || $product->operator_id == $user->id)) {
            return view('imonitor::frontend.products.show', compact('product','event'));
        } else {
            return abort(404);
        }
    }

    public function historic($id)
    {
        $user = $this->auth->user();

        $product = $this->product->find($id);
        $event=$product->events->last();
        if (($this->auth->hasAccess('imonitor.products.index')) || ($product->user_id == $user->id || $product->operator_id == $user->id)) {
            return view('imonitor::frontend.products.historic', compact('product','event'));
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
        $event=$product->events->last();
        if ($this->auth->hasAccess('imonitor.products.unique')) {
            return view('imonitor::frontend.products.unique', compact('product', 'event'));
        } else {
            return abort(404);
        }
    }

    /**
     * Exportar el historial del producto a excel
     * @param  Request $request
     * @param  Integer $id Id del producto
     * @param  RecordRepository $records Repositorio del records
     * @return Excel             Documento de excel
     * @author Carlos Asnelmi <carlos@anselmi.com>
     */
    public function historicExport(Request $request, $id, RecordRepository $records)
    {
        $product =  $this->product->find($request->product_id);

        $user =  request()->user();

        $path = 'exportExcelProduct/'.$product->title.'_'.date('Y-M-D H:M:S').'.xlsx';

        try {

            (new ProductsExport($request, $product))->store($path,'publicmedia');

            \Mail::to($user)->send(new ExportExcel($user,$path));

            return Response()->json(['path' => url($path)], 200);

        } catch (Exception $e) {

            return Response()->json(['error' => $e], 500);

        }
    }

    /**
     * Se genera una notificación via email de la alerta ocurrida.
     * @param  Request $request
     * @return Resonse::json
     */
    public function alertClient(Request $request)
    {
        $response = array();

        try {

            $product = $this->product->find($request->product_id);

            $user = $product->user;

            $subject = trans("imonitor::alerts.messages.subject Clent", ['product' => $product->title]);

            $view = "imonitor::frontend.emails.alert_clinet";

            \Mail::to($user->email)->send(new SendAlertClient($product, $subject, $view));

            $response['status'] = 'success';

        } catch (\Throwable $t) {

            $response['status'] = 'error';

            $response['message'] = $t->getMessage();

            Log::error($t);

        }

        return response()->json($response);
    }

}