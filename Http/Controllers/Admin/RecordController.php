<?php

namespace Modules\Imonitor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Imonitor\Entities\Record;
use Modules\Imonitor\Http\Requests\CreateRecordRequest;
use Modules\Imonitor\Http\Requests\UpdateRecordRequest;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\RecordRepository;

class RecordController extends AdminBaseController
{
    /**
     * @var RecordRepository
     */
    private $record;
    private $product;

    public function __construct(RecordRepository $record, ProductRepository $product)
    {
        parent::__construct();
        $this->product = $product;
        $this->record = $record;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($product)
    {
        $records = $this->record->whereProduct($product);
        $product = $this->product->find($product);

        return view('imonitor::admin.records.index', compact('records', 'product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('imonitor::admin.records.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRecordRequest $request
     * @return Response
     */
    public function store(CreateRecordRequest $request)
    {
        $this->record->create($request->all());

        return redirect()->route('admin.imonitor.record.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('imonitor::records.title.records')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Record $record
     * @return Response
     */
    public function edit(Record $record)
    {
        return view('imonitor::admin.records.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Record $record
     * @param  UpdateRecordRequest $request
     * @return Response
     */
    public function update(Record $record, UpdateRecordRequest $request)
    {
        $this->record->update($record, $request->all());

        return redirect()->route('admin.imonitor.record.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('imonitor::records.title.records')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Record $record
     * @return Response
     */
    public function destroy(Record $record)
    {
        $this->record->destroy($record);

        return redirect()->route('admin.imonitor.record.index')
            ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('imonitor::records.title.records')]));
    }
}
