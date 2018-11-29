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
use Modules\Iplaces\Http\Controllers\Api\BaseApiController;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\VariableRepository;
use Modules\Imonitor\Transformers\VariableTransformers;
use Modules\Imonitor\Entities\Variable;

use Route;

class VariableController extends BaseApiController
{
    private $variable;

    public function __construct(VariableRepository $variable)
    {
        parent::__construct();
        $this->variable = $variable;
      //  $this->status = $status;
    }

    public function index(Request $request){
        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(1, 12, false, []);

            //Request to Repository
            $variables = $this->variable->index($p->page, $p->take, $p->filter, $p->include);

            //Response
            $response = ["data" => VariableTransformers::collection($variables)];

            //If request pagination add meta-page
            $p->page ? $response["meta"] = ["page" => $this->pageTransformer($variables)] : false;
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
            $variable = $this->variable->show($slug, $p->include);

            //Response
            $response = [
                "data" => is_null($variable) ? false : new VariableTransformers($variable)];
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function variables(Request $request)
    {
        //dd($request)
        try {
            if (isset($request->include)) {
                $includes = explode(",", $request->include);
            } else {
                $includes = null;
            }
            if (isset($request->filters) && !empty($request->filters)) {
                $filters = json_decode($request->filters);
                $results = $this->variable->whereFilters($filters, $includes);

                if (isset($filters->take)) {
                    $response = [
                        'meta' => [
                            "take" => $filters->take ?? 5,
                            "skip" => $filters->skip ?? 0,
                        ],
                        'data' => VariableTransformers::collection($results),
                    ];
                } else {
                    $response = [
                        'meta' => [
                            "total-pages" => $results->lastPage(),
                            "per_page" => $results->perPage(),
                            "total-item" => $results->total()
                        ],
                        'data' => VariableTransformers::collection($results),
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
                $paginate = $request->paginate ?? 12;

                $results = $this->variable->paginate($paginate);
                $response = [
                    'meta' => [
                        "total-pages" => $results->lastPage(),
                        "per_page" => $results->perPage(),
                        "total-item" => $results->total()
                    ],
                    'data' => VariableTransformers::collection($results),
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
                "title" => "Error Query Categories",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);

    }

//get
    public function variable(Variable $variable, Request $request)
    {
        // dd($request);
        try {

            if (isset($variable->id) && !empty($variable->id)) {

                $response = [
                    "meta" => [
                        "metatitle" => $variable->metatitle,
                        "metadescription" => $variable->metadescription
                    ],
                    "type" => "variable",
                    "id" => $variable->id,
                    "attributes" => new VariableTransformers($variable),
                ];

                //   $includes = explode(",", $request->include);
                // is_array($request->include) ? true : $request->include = [$request->include];


                /*

                 if (in_array('parent', $includes)) {
                     if ($variable->parent) {
                         $response["relationships"]["parent"] = new VariableTransformers($variable->parent);
                     } else {
                         $response["relationships"]["parent"] = array();
                     }
                 }
                 if (in_array('children', $includes)) {

                     $response["relationships"]["children"] = VariableTransformers::collection($variable->children()->paginate(12));
                 }
 */

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
                "title" => "Error Query Variable",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

//get
    public function posts(Variable $variable, Request $request)
    {
        try {
            $includes = explode(",", $request->include);
            if (isset($request->filters) && !empty($request->filters)) {
                $filters = json_decode($request->filters);
                $filters->variables = $variable->id;

                $results = $this->post->whereFilters($filters, $includes);

                if (isset($filters->take)) {
                    $response = [
                        'meta' => [
                            "take" => $filters->take ?? 5,
                            "skip" => $filters->skip ?? 0,
                        ],
                        'data' => VariableTransformers::collection($results),
                    ];
                } else {
                    $response = [
                        'meta' => [
                            "total-pages" => $results->lastPage(),
                            "per_page" => $results->perPage(),
                            "total-item" => $results->total()
                        ],
                        'data' => VariableTransformers::collection($results),
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

                $results = $this->post->whereFilters((object)$filter = ['variables' => $variable->id, 'paginate' => $request->paginate ?? 12], $request->includes ?? false);
                $response = [
                    'meta' => [
                        "total-pages" => $results->lastPage(),
                        "per_page" => $results->perPage(),
                        "total-item" => $results->total()
                    ],
                    'data' => VariableTransformers::collection($results),
                    'links' => [
                        "self" => $results->currentPage(),
                        "first" => $results->hasMorePages(),
                        "prev" => $results->previousPageUrl(),
                        "next" => $results->nextPageUrl(),
                        "last" => $results->lastPage()
                    ]

                ];
            }
            if (isset($request->category_id)) {

            } else {

            }

        } catch (\Exception $e) {
            $status = 500;
            $response = ['errors' => [
                "code" => "501",
                "source" => [
                    "pointer" => url($request->path()),
                ],
                "title" => "Error Query Categories",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

//post
    public function store(Request $request)
    {
        try {
            $variable = $this->variable->create($request->all());
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('iplace::common.singular')]),
                    "detail" => [
                        'id' => $variable->id
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
                "title" => "Error Query Categories",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }

    //put
    public function update(Variable $variable, Request $request)
    {

        try {

            if (isset($variable->id) && !empty($variable->id)) {
                $options = (array)$request->options ?? array();
                isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
                isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
                $request['options'] = json_encode($options);
                $variable = $this->variable->update($variable, $request->all());

                $status = 200;
                $response = [
                    'susses' => [
                        'code' => '201',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => trans('core::core.messages.resource updated', ['name' => trans('iplace::variables.singular')]),
                        "detail" => [
                            'id' => $variable->id
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
                "title" => "Error Query Variable",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

//delete
    public function delete(Variable $variable, Request $request)
    {
        try {

            $this->variable->destroy($variable);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('iplace::variables.singular')]),
                    "detail" => [
                        'id' => $variable->id
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
                "title" => "Error Query Variable",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }
}