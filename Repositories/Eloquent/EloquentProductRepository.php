<?php

namespace Modules\Imonitor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Imonitor\Events\ProductListEvent;
use Modules\Imonitor\Events\ProductWasCreated;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\Iproducts\Repositories\Collection;


class EloquentProductRepository extends EloquentBaseRepository implements ProductRepository
{

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->with('translations')->findOrFail($id);
        }

        return $this->model->findOrFail($id);
    }

    public function wherebyFilter($page, $take, $filter, $include)
    {
        //Initialize Query
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if (count($include)) {
            //Include relationships for default
            $includeDefault = ['variables',];
            $query->with(array_merge($includeDefault, $include));
        }

        /*== FILTER ==*/
        if ($filter) {

            //Filter excluding products by ID
            if (isset($filter->excludeById) && is_array($filter->excludeById)) {
                $query->whereNotIn('id', $filter->excludeById);
            }

            //Get specific products by ID
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

            //Add order for variables

            if (isset($filter->variables) && is_array($filter->variables)) {
                is_array($filter->variables) ? true : $filter->variables = [$filter->variables];
                $query->whereHas('variables', function ($q) use ($filter) {
                    $q->whereIn('variable_id', $filter->variables);
                });
            }

            //Add order By
            $orderBy = isset($filter->orderBy) ? $filter->orderBy : 'created_at';
            $orderType = isset($filter->orderType) ? $filter->orderType : 'desc';
            $query->orderBy($orderBy, $orderType);
            $query->whereStatus(Status::ACTIVE);
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

    public function getItemsBy($params = false)
      {
        /*== initialize query ==*/
        $query = $this->model->query();

        /*== RELATIONSHIPS ==*/
        if(in_array('*',$params->include)){//If Request all relationships
          $query->with([]);
        }else{//Especific relationships
          $includeDefault = [];//Default relationships
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

          //Order by
          if (isset($filter->order)) {
            $orderByField = $filter->order->field ?? 'created_at';//Default field
            $orderWay = $filter->order->way ?? 'desc';//Default way
            $query->orderBy($orderByField, $orderWay);//Add order to query
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

      public function getItem($criteria, $params = false)
          {
            //Initialize query
            $query = $this->model->query();

          /*== RELATIONSHIPS ==*/
          if(in_array('*',$params->include)){//If Request all relationships
            $query->with([]);
          }else{//Especific relationships
            $includeDefault = [];//Default relationships
            if (isset($params->include))//merge relations with default relationships
              $includeDefault = array_merge($includeDefault, $params->include);
            $query->with($includeDefault);//Add Relationships to query
          }

            /*== FILTER ==*/
            if (isset($params->filter)) {
              $filter = $params->filter;

              if (isset($filter->field))//Filter by specific field
                $field = $filter->field;
            }

            /*== FIELDS ==*/
            if (isset($params->fields) && count($params->fields))
              $query->select($params->fields);

            /*== REQUEST ==*/
            return $query->where($field ?? 'id', $criteria)->first();
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
        return $query->firstOrFail();
    }

    public function create($data)
    {
        // dd($data);
        $product = $this->model->create($data);
        event(new ProductWasCreated($product, $data));
        $product->variables()->sync(array_get($data, 'variables', []));
        return $this->find($product->id);
    }

    public function update($model, $data)
    {//dd($data);
        if (!isset($data['maintenance'])) {
            $data['maintenance'] = false;
        }
        $model->update($data);
        event(new ProductListEvent($model,$data));
        $variables = array_get($data, 'variables', []);
        foreach ($variables as $index => $variable) {
            if (!array_key_exists('variable_id', $variable)) {
                unset($variables[$index]);
            }
        }

        $data['variables'] = $variables;
        $model->variables()->sync(array_get($data, 'variables', []));
        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereUser($id)
    {
        $query = $this->model->with('translations')->where('user_id', $id)->orWhere('operator_id', $id)->paginate(12);
        return $query;
    }

    public function whereOperator($id)
    {
        $query = $this->model->with('translations')->Where('operator_id', $id)->paginate(12);
        return $query;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whereVariable($id)
    {
        $query = $this->model->where('user_id', $id)->paginate(12);
        return $query;
    }
}
