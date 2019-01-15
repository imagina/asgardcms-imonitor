<?php

namespace Modules\Imonitor\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Imonitor\Events\RecordListEvent;
use Modules\Imonitor\Repositories\RecordRepository;

class EloquentRecordRepository extends EloquentBaseRepository implements RecordRepository
{
    public function wherebyFilter($page, $take, $filter, $include)
    {
        //Initialize Query
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (count($include)) {
            //Include relationships for default
            $includeDefault = [];
            $query->with(array_merge($includeDefault, $include));
        }
        /*== FILTER ==*/
        if ($filter) {
            //Filter by slug
            if (isset($filter->product)) {

                $query->where('product_id', $filter->product);
            }

            //Filter excluding performers by ID
            if (isset($filter->excludeById) && is_array($filter->excludeById)) {
                $query->whereNotIn('id', $filter->excludeById);
            }

            //Get specific performers by ID
            if (isset($filter->includeById) && is_array($filter->includeById)) {
                $query->whereIn('id', $filter->includeById);
            }

            //Search filter
            if (isset($filter->search) && !empty($filter->search)) {
                //Get the words separately from the criterion
                $words = explode(' ', trim($filter->search));

                //Add condition of search to query
                $query->where(function ($query) use ($words) {
                    foreach ($words as $index => $word) {
                        $query->where('title', 'like', "%" . $word . "%")
                            ->orWhere('description', 'like', "%" . $word . "%");
                    }
                });
            }
            //Add order by city
            if (isset($filter->cities) && is_array($filter->cities)) {
                is_array($filter->cities) ? true : $filter->cities = [$filter->cities];
                $query->whereIn('city_id', $filter->cities);

            }
            //Add order for genre
            if (isset($filter->variables) && is_array($filter->variables)) {

                is_array($filter->variables) ? true : $filter->vatiables = [$filter->variables];
                $query->whereIn('variable_id', $filter->variables);
            }
            if (isset($filter->client)) {
                $query->where('client_id', $filter->client);
            }
            //Add order for genre
            if (isset($filter->range) && is_array($filter->range)) {

                $query->whereBetween('created_at', $filter->range);
            }
            //Add order By
            $orderBy = isset($filter->orderBy) ? $filter->orderBy : 'created_at';
            $orderType = isset($filter->orderType) ? $filter->orderType : 'desc';
            $query->orderBy($orderBy, $orderType);
        }

        /*=== REQUEST ===*/
        if ($page) {//Return request with pagination
            $take ? true : $take = 12; //If no specific take, query default take is 12
            return $query->paginate($take);
        } else {//Return request without pagination
            $take ? $query->take($take) : false; //Set parameter take(limit) if is requesting
            return $query->get();
        }
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $record = $this->model->create($data);
        event(new RecordListEvent($record, $data));
        return $record;
    }
    public   function whereProduct($id){
        $query = $this->model->query();
        $query->with('product','variable');
        $query->where('product_id',$id);
        $query->orderBy('created_at','desc');
        return $query->paginate(400);
    }

}
