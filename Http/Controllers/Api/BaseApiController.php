<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 23/10/2018
 * Time: 10:23 AM
 */

namespace Modules\Imonitor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Route;


class BaseApiController extends BasePublicController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function parametersUrl($page = false, $take = false, $filter = [], $include = [])
    {
        $request = request();

        return (object)[
            "page" => is_numeric($request->input('page')) ? $request->input('page') : $page,
            "take" => is_numeric($request->input('take')) ? $request->input('take') : $take,
            "filter" => $request->input('filter') ? json_decode($request->input('filter')) : $filter,
            "include" => $request->input('include') ? explode(",", $request->input('include')) : $include
        ];
    }
    public function pageTransformer($data)
    {
        return [
            "total" => $data->total(),
            "lastPage" => $data->lastPage(),
            "perPage" => $data->perPage(),
            "currentPage" => $data->currentPage()
        ];
    }



}