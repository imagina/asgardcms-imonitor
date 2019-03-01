<?php

namespace Modules\Imonitor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Imonitor\Entities\Alert;
use Modules\Imonitor\Http\Requests\CreateAlertRequest;
use Modules\Imonitor\Http\Requests\UpdateAlertRequest;
use Modules\Imonitor\Repositories\AlertRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Imonitor\Repositories\ProductRepository;

class AlertController extends AdminBaseController
{
    /**
     * @var AlertRepository
     */
    private $alert;
    private $product;

    public function __construct(AlertRepository $alert, ProductRepository $product)
    {
        parent::__construct();

        $this->alert = $alert;
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $product
     * @return Response
     */
    public function index($product)
    {
        $alerts = $this->alert->getItemsBy((object)['product_id'=>$product,'take'=>400, 'include'=>[], 'order'=>(object)['field' =>'created_at','way'=>'desc']]);
        $product = $this->product->find($product);
        return view('imonitor::admin.alerts.index', compact('alerts','product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response

    public function create()
    {
        return view('imonitor::admin.alerts.create');
    }
*/
    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAlertRequest $request
     * @return Response

    public function store(CreateAlertRequest $request)
    {
        $this->alert->create($request->all());

        return redirect()->route('admin.imonitor.alert.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('imonitor::alerts.title.alerts')]));
    }
*/
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Alert $alert
     * @return Response

    public function edit(Alert $alert)
    {
        return view('imonitor::admin.alerts.edit', compact('alert'));
    }
*/
    /**
     * Update the specified resource in storage.
     *
     * @param  Alert $alert
     * @param  UpdateAlertRequest $request
     * @return Response
     */
    public function update(Alert $alert, UpdateAlertRequest $request)
    {
        $this->alert->update($alert, $request->all());

        return redirect()->route('admin.imonitor.alert.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('imonitor::alerts.title.alerts')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Alert $alert
     * @return Response

    public function destroy(Alert $alert)
    {
        $this->alert->destroy($alert);

        return redirect()->route('admin.imonitor.alert.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('imonitor::alerts.title.alerts')]));
    } */
}
