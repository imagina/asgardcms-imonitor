<?php


namespace Modules\Imonitor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Imonitor\Http\Requests\CreateEventRequest;
use Modules\Imonitor\Repositories\EventRepository;
use Modules\Imonitor\Transformers\EventTransformer;
use Route;

class EventController extends BaseApiController
{

    public $event;

    public function __construct(EventRepository $event)
    {
        $this->event=$event;
    }

    /**
     * GET ITEMS
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        try {

            $params = $this->getParamsRequest($request);                                                                  //Get Parameters from URL.


            $dataEntity = $this->event->getItemsBy($params);                                                         //Request to Repository


            $response = ["data" => EventTransformer::collection($dataEntity)];                                            //Response


            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($dataEntity)] : false;                  //If request pagination add meta-page
        } catch (\Exception $e) {
            \Log::error($e);
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }


        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);                         //Return response
    }

    /**
     * GET A ITEM
     *
     * @param $criteria
     * @return mixed
     */
    public function show($criteria, Request $request)
    {
        try {

            $params = $this->getParamsRequest($request);                                                                //Get Parameters from URL.


            $dataEntity = $this->event->getItem($criteria, $params);                                               //Request to Repository


            if (!$dataEntity) throw new Exception('Item not found', 204);                                               //Break if no found item


            $response = ["data" => new EventTransformer($dataEntity)];                                                  //Response


            $params->page ? $response["meta"] = ["page" => $this->pageTransformer($dataEntity)] : false;                //If request pagination add meta-page
        } catch (\Exception $e) {
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }


        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);                         //Return response
    }

    /**
     * CREATE A ITEM
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $data = $request->all() ?? [];                                                                //Get data

            $this->validateRequestApi(new CreateEventRequest($data));                                                        //Validate Request


            $dataEntity = $this->event->create($data);                                                             //Create item


            $response = ["data" => new EventTransformer($dataEntity)];                                                  //Response
            \DB::commit();                                                                                              //Commit to Data Base
        } catch (\Exception $e) {
            \DB::rollback();                                                                                            //Rollback to Data Base
            $status = $this->getStatusError($e->getCode());
            $response = ["errors" => $e->getMessage()];
        }

        return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);                         //Return response
    }


}