<?php

namespace Modules\Imonitor\Repositories\Eloquent;

use Modules\Imonitor\Repositories\VariableRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentVariableRepository extends EloquentBaseRepository implements VariableRepository
{

    public function index($page, $take, $filter, $include)
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
            //Filter by id
            if (isset($filter->id)) {
                $query->where('id', $filter->id);
            }

            //Filter excluding variables by ID
            if (isset($filter->excludeById) && is_array($filter->excludeById)) {
                $query->whereNotIn('id', $filter->excludeById);
            }

            //Get specific variables by ID
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

    public function show($param, $include)
    {
        $isID = (int)$param >= 1 ? true : false;

        //Initialize Query
        $query = $this->model->query();

        if ($isID) {//if is by ID
            $query = $this->model->where('id', $param);
        }

        /*== RELATIONSHIPS ==*/
        if (count($include)) {
            //Include relationships for default
            $includeDefault = [];
            $query->with(array_merge($includeDefault, $include));
        }

        /*=== REQUEST ===*/
        return $query->first();
    }

    public function create($data)
    {
        // dd($data);
        $variable = $this->model->create($data);
        return $this->find($variable->id);
    }
    
}
