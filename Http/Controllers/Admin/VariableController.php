<?php

namespace Modules\Imonitor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Imonitor\Entities\Variable;
use Modules\Imonitor\Http\Requests\CreateVariableRequest;
use Modules\Imonitor\Http\Requests\UpdateVariableRequest;
use Modules\Imonitor\Repositories\VariableRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class VariableController extends AdminBaseController
{
    /**
     * @var VariableRepository
     */
    private $variable;

    public function __construct(VariableRepository $variable)
    {
        parent::__construct();

        $this->variable = $variable;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $variables = $this->variable->all();

        return view('imonitor::admin.variables.index', compact('variables'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $variables = $this->variable->paginate(20);

        return view('imonitor::admin.variables.create', compact('variables'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateVariableRequest $request
     * @return Response
     */
    public function store(CreateVariableRequest $request)
    {
        try{
            $this->variable->create($request->all());

            return redirect()->route('admin.imonitor.variable.index')
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('imonitor::variables.title.variables')]));

        }catch (\Exception $e){
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::variables.title.variables')]))->withInput($request->all());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Variable $variable
     * @return Response
     */
    public function edit(Variable $variable)
    {
        $variables=$this->variable->paginate(20);
        return view('imonitor::admin.variables.edit', compact('variable','variables'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Variable $variable
     * @param  UpdateVariableRequest $request
     * @return Response
     */
    public function update(Variable $variable, UpdateVariableRequest $request)
    {
        try{
            $this->variable->update($variable, $request->all());

            return redirect()->route('admin.imonitor.variable.index')
                ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('imonitor::variables.title.variables')]));

        }catch (\Exception $e){
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::variables.title.variables')]))->withInput($request->all());

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Variable $variable
     * @return Response
     */
    public function destroy(Variable $variable)
    {
        try{
            $this->variable->destroy($variable);

            return redirect()->route('admin.imonitor.variable.index')
                ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('imonitor::variables.title.variables')]));

        }catch (\Exception $e){
            \Log::erro($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::variables.title.variables')]));



        }

    }
}
