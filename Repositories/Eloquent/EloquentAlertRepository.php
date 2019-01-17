<?php

namespace Modules\Imonitor\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Imonitor\Entities\Status;
use Modules\Imonitor\Events\AlertWasCreated;
use Modules\Imonitor\Repositories\AlertRepository;

/**
 * Class EloquentAlertRepository
 * @package Modules\Imonitor\Repositories\Eloquent
 */
class EloquentAlertRepository extends EloquentBaseRepository implements AlertRepository
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $alert = $this->model->create($data);
        event(new AlertWasCreated($alert, $data));
        return $this->find($alert->id);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function WhereByProduct($id)
    {
        $query = $this->model
            ->with('product', 'record')
            ->where('product_id', $id)
            ->whereStatus(Status::ACTIVE)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return $query;

    }

    /**
     * @param bool $params
     * @return mixed
     */
    public function getItemsBy($params = false)
    {
        /*== initialize query ==*/
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (in_array('*', $params->include)) {//If Request all relationships
            $query->with([]);
        } else {//Especific relationships
            $includeDefault = ['product', 'record'];//Default relationships
            if (isset($params->include))//merge relations with default relationships
                $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
        }

        /*== FILTERS ==*/
        if (isset($params->filter)) {
            $filter = $params->filter;//Short filter

            //Filter by date
            if (isset($filter->date)) {
                $date = $filter->date;//Short filter date
                $date->field = $date->field ?? 'created_at';
                if (isset($date->from))//From a date
                    $query->whereDate($date->field, '>=', $date->from);
                if (isset($date->to))//to a date
                    $query->whereDate($date->field, '<=', $date->to);
            }
            //Filter by Product
            if (isset($filter->product)) {
                $productId = $filter->product;//Short filter date
                $query->where('product_id', $productId);
            }
            if (isset($filter->record)) {
                $recordId = $filter->record;//Short filter date
                $query->where('product_id', $recordId);
            }
            //Order by
            if (isset($filter->order)) {
                $orderByField = $filter->order->field ?? 'created_at';//Default field
                $orderWay = $filter->order->way ?? 'desc';//Default way
                $query->orderBy($orderByField, $orderWay);//Add order to query
            }


            if (isset($filter->status)) {
                if ($filter->status == 1) {
                    $query->whereStatus(Status::COMPLETE);
                } else {
                    $query->whereStatus(Status::ACTIVE);
                }
            }


        }

        /*== FIELDS ==*/
        if (isset($params->fields) && count($params->fields))
            $query->select($params->fields);

        /*== REQUEST ==*/
        if (isset($params->page) && $params->page) {
            return $query->paginate($params->take);
        } else {
            $params->take ? $query->take($params->take) : false;//Take
            return $query->get();
        }
    }
}
