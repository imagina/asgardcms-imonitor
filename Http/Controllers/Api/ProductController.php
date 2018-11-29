<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 29/11/2018
 * Time: 12:29 PM
 */

namespace Modules\Imonitor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Imonitor\Http\Controllers\Api\BaseApiController;
use Modules\Imonitor\Entities\Product;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Entities\Variable;
use Modules\Imonitor\Transformers\ProductTransformers;
use Modules\Imonitor\Transformers\VariableTransformers;

use Route;


class ProductController extends BaseApiController
{
    private $product;

    public function __construct(ProductRepository $product)
    {
        parent::__construct();
        $this->product = $product;

    }
    public function index(Request $request){
        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(1, 12, false, []);

            //Request to Repository
            $products = $this->product->index($p->page, $p->take, $p->filter, $p->include);

            //Response
            $response = ["data" => ProductTransformers::collection($products)];

            //If request pagination add meta-page
            $p->page ? $response["meta"] = ["page" => $this->pageTransformer($products)] : false;
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }
    public function show($slug, Request $request)
    {
        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(false, false, false, []);

            //Request to Repository
            $product = $this->product->show($slug, $p->include);

            //Response
            $response = [
                "data" => is_null($product) ? false : new ProductTransformers($product)];
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function products(Request $request)
    {
        try {
            if (isset($request->include)) {
                $includes = explode(",", $request->include);
            } else {
                $includes=null;
            }
            if (isset($request->filters) && !empty($request->filters)) {
                $filters = json_decode($request->filters);
                $results = $this->product->whereFilters($filters, $includes);

                if (isset($filters->take)) {
                    $response = [
                        'meta' => [
                            "take" => $filters->take ?? 5,
                            "skip" => $filters->skip ?? 0,
                        ],
                        'data' => ProductTransformers::collection($results),
                    ];
                } else {
                    $response = [
                        'meta' => [
                            "total-pages" => $results->lastPage(),
                            "per_page" => $results->perPage(),
                            "total-item" => $results->total()
                        ],
                        'data' => ProductTransformers::collection($results),
                        'links' => [
                            "self" => $results->currentPage(),
                            "first" => $results->hasMorePages(),
                            "prev" => $results->previousPageUrl(),
                            "next" => $results->nextPageUrl(),
                            "last" => $results->lastPage()
                        ]

                    ];
                }
            } else {

                $results = $this->product->paginate($request->paginate ?? 12);
                $response = [
                    'meta' => [
                        "total-pages" => $results->lastPage(),
                        "per_page" => $results->perPage(),
                        "total-item" => $results->total()
                    ],
                    'data' => ProductTransformers::collection($results),
                    'links' => [
                        "self" => $results->currentPage(),
                        "first" => $results->hasMorePages(),
                        "prev" => $results->previousPageUrl(),
                        "next" => $results->nextPageUrl(),
                        "last" => $results->lastPage()
                    ]

                ];
            }

        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Products",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);

    }

    public function product(Product $product, Request $request)
    {// dd($product);
        try {
            if (isset($product->id) && !empty($product->id)) {
                $response = [
                /*
                    "meta" => [
                        "metatitle" => $product->metatitle,
                        "metadescription" => $product->metadescription
                    ],
                */
                    "type" => "articles",
                    "id" => $product->id,
                    "attributes" => new ProductTransformers($product),

                ];

                $includes = explode(",", $request->include);

                if (in_array('author', $includes)) {
                    $response["relationships"]["author"] = new UserProfileTransformer($product->user);

                }
                if (in_array('product', $includes)) {
                    $response["relationships"]["product"] = new ProductTransformers($product->product);
                }



            } else {
                $status = 404;
                $response = ['errors' => [
                    "code" => "404",
                    "source" => [
                        "pointer" => url($request->path()),
                    ],
                    "title" => "Not Found",
                    "detail" => 'Query empty'
                ]
                ];
            }
        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Products",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function store(Request $request)
    {//dd($request);
        try {

           // $request['options'] = $options;
            $product = $this->product->create($request->all());
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('imonitor::common.singular')]),
                    "detail" => [
                        'id' => $product->id
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Products",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }

    public function update(Product $product, Request $request)
    {

        try {

            if (isset($product->id) && !empty($product->id)) {
                $options = (array)$request->options ?? array();
               // isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
               // isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
               $request['options'] = json_encode($options);
                $product = $this->product->update($product, $request->all());

                $status = 200;
                $response = [
                    'susses' => [
                        'code' => '201',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => trans('core::core.messages.resource updated', ['name' => trans('imonitor::products.singular')]),
                        "detail" => [
                            'id' => $product->id
                        ]
                    ]
                ];


            } else {
                $status = 404;
                $response = ['errors' => [
                    "code" => "404",
                    "source" => [
                        "pointer" => url($request->path()),
                    ],
                    "title" => "Not Found",
                    "detail" => 'Query empty'
                ]
                ];
            }
        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function delete(Product $product, Request $request)
    {
        try {
            $this->product->destroy($product);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('imonitor::products.singular')]),
                    "detail" => [
                        'id' => $product->id
                    ]
                ]
            ];

        } catch (\Exception $e) {
            Log::error($e);
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Post",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

}
