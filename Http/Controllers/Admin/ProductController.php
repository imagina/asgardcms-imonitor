<?php

namespace Modules\Imonitor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Imonitor\Entities\Product;
use Modules\Imonitor\Entities\Variable;
use Modules\Imonitor\Http\Requests\CreateProductRequest;
use Modules\Imonitor\Http\Requests\UpdateProductRequest;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\VariableRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\User\Repositories\UserRepository;
use Modules\User\Transformers\UserProfileTransformer;

class ProductController extends AdminBaseController
{
    /**
     * @var ProductRepository
     */
    private $product;
    private $variable;
    private $user;

    public function __construct(ProductRepository $product, VariableRepository $variable, UserRepository $user)
    {
        parent::__construct();

        $this->product = $product;
        $this->variable = $variable;
        $this->user=$user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $products = $this->product->paginate(20);

        return view('imonitor::admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $variables= $this->variable->all();
        $users = $this->user->all();

        return view('imonitor::admin.products.create', compact('variables','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateProductRequest $request
     * @return Response
     */
    public function store(CreateProductRequest $request)
    {
        try{

            $this->product->create($request->all());

            return redirect()->route('admin.imonitor.product.index')
                ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('imonitor::products.title.products')]));
        }catch (\Exception $e){
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::products.title.products')]))->withInput($request->all());

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product $product
     * @return Response
     */
    public function edit($id)
    {
        $product= $this->product->find($id);
        $variables= $this->variable->all();
        $users = $this->user->all();

        return view('imonitor::admin.products.edit', compact('product','variables','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Product $product
     * @param  UpdateProductRequest $request
     * @return Response
     */
    public function update(Product $product, UpdateProductRequest $request)
    {
        try{
            $this->product->update($product, $request->all());

            return redirect()->route('admin.imonitor.product.index')
                ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('imonitor::products.title.products')]));

        }catch (\Exception $e){
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::products.title.products')]))->withInput($request->all());

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return Response
     */
    public function destroy(Product $product)
    {
        try{
            $this->product->destroy($product);

            return redirect()->route('admin.imonitor.product.index')
                ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('imonitor::products.title.products')]));

        }catch (\Exception $e){
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('imonitor::products.title.products')]));

        }

    }
}
