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
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Imonitor\Entities\Record;
use Modules\Imonitor\Repositories\RecordRepository;
use Modules\Imonitor\Transformers\RecordTransformers;
use Route;

//Base API


class RecordController extends BaseApiController
{
    private $record;

    public function __construct(RecordRepository $record)
    {
        parent::__construct();
        $this->record = $record;

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

            // $request['options'] = $options;
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
