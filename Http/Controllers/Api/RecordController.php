<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 29/11/2018
 * Time: 12:29 PM
 */

namespace Modules\Imonitor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Imonitor\Entities\Record;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Imonitor\Repositories\RecordRepository;
use Modules\Imonitor\Transformers\RecordTransformers;
use Route;

//Base API


class RecordController extends BaseApiController
{
    private $record;
    private $product;

    public function __construct(RecordRepository $record, ProductRepository $product)
    {
        parent::__construct();
        $this->record = $record;
        $this->product = $product;

    }


    public function index(Request $request)
    {

        try {
            //Get Parameters from URL.
            $p = $this->parametersUrl(false, 12, false, []);
            //Request to Repository
            $records = $this->record->wherebyFilter($p->page, $p->take, $p->filter, $p->include);

            //Response
            $response = ["data" => RecordTransformers::collection($records)];

            //If request pagination add meta-page
            $p->page ? $response["meta"] = ["page" => $this->pageTransformer($records)] : false;
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function historic(Request $request)
    {

        try {
            //Get Parameters from URL.
            $p = $this->getParamsRequest($request);
            //Request to Repository
            $records = $this->record->wherebyFilter($p->page, $p->take, $p->filter, $p->include);
            $data = array();
            $variable = array();
            if (count($records)) {
                foreach ($records as $record) {
                    $val = ['date' => ($record->created_at),
                        'value' => floatval($record->value)];
                    if (!array_key_exists($record->variable_id, $variable)) {
                        $variable{$record->variable_id} = $val;
                    } else {
                        array_push($variable{$record->variable_id}, $val);
                    }
                }
            }
            //Response
            $response = ["data" => $variable];
            //If request pagination add meta-page
            $p->page ? $response["meta"] = ["page" => $this->pageTransformer($variable)] : false;
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            Log::error($e);
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
            $record = $this->record->show($slug, $p->include);

            //Response
            $response = [
                "data" => is_null($record) ? false : new RecordTransformers($record)];
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                "errors" => $e->getMessage()
            ];
        }

        return response()->json($response, $status ?? 200);
    }


    public function store(Request $request)
    {
        try {
            $product = $this->product->find($request->product_id);
            $productVariables = array();
            foreach ($product->variables as $index => $variable) {
                $productVariables[$index] = $variable->id;
            }
            if (in_array($request->variable_id, $productVariables)) {
                if ($product->productUser->id == Auth::user()->id) {


                    $request['client_id'] = $product->user->id;
                    $record = $this->record->create($request->all());
                    $status = 200;
                    $response = [
                        'susses' => [
                            'code' => '201',
                            "source" => [
                                "pointer" => url($request->path())
                            ],
                            "title" => trans('core::core.messages.resource created', ['name' => trans('imonitor::common.singular')]),
                            "detail" => [
                                'id' => $record->id
                            ]
                        ]
                    ];
                } else {
                    $status = 401;
                    $response = [
                        'susses' => [
                            'code' => '401',
                            "source" => [
                                "pointer" => url($request->path())
                            ],
                            "title" => '401 Unauthorized',
                        ]
                    ];
                }
            } else {
                $status = 404;
                $response = [
                    'susses' => [
                        'code' => '404',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => 'Variable not Fount',
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
                "title" => "Error Query Records",
                "detail" => $e->getMessage()
            ]
            ];
        }
        return response()->json($response, $status ?? 200);

    }

    public function register(Request $request)
    {
        \Log::info($request->all());
        try {
            $items = $request->all();
            $regiter = array();
            foreach ($items as $index => $item) {
                $product = $this->product->find($item['product_id']);
                $item['client_id'] = $product->user->id;
                $data = new \Illuminate\Http\Request();
                $data->setMethod('POST');
                $data->request->add($item);
                $regiter[$index] = $this->store($data);
            }
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "source" => [
                        "pointer" => url($request->path())
                    ],
                    "title" => trans('core::core.messages.resource created', ['name' => trans('imonitor::common.singular')]),
                    "detail" => [
                        $regiter
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
                "title" => "Error Query Records",
                "detail" => $e->getMessage()
            ]
            ];
        }

        return response()->json($response, $status ?? 200);
    }

    public function update(Record $record, Request $request)
    {

        try {

            if (isset($record->id) && !empty($record->id)) {
                $options = (array)$request->options ?? array();
                // isset($request->metatitle) ? $options['metatitle'] = $request->metatitle : false;
                // isset($request->metadescription) ? $options['metadescription'] = $request->metatitle : false;
                $request['options'] = json_encode($options);
                $record = $this->record->update($record, $request->all());

                $status = 200;
                $response = [
                    'susses' => [
                        'code' => '201',
                        "source" => [
                            "pointer" => url($request->path())
                        ],
                        "title" => trans('core::core.messages.resource updated', ['name' => trans('imonitor::records.singular')]),
                        "detail" => [
                            'id' => $record->id
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

    public function delete(Record $record, Request $request)
    {
        try {
            $this->record->destroy($record);
            $status = 200;
            $response = [
                'susses' => [
                    'code' => '201',
                    "title" => trans('core::core.messages.resource deleted', ['name' => trans('imonitor::records.singular')]),
                    "detail" => [
                        'id' => $record->id
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
