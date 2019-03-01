<?php

namespace Modules\Imonitor\Presenters;


use Laracasts\Presenter\Presenter;


class EventPresenter extends Presenter
{
    protected $status;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->alert = app('Modules\Imonitor\Repositories\EventRepository');
    }

    public function valueLabel()
    {
        switch ($this->entity->value) {
            case 0:
                return trans('imonitor::events.labels.off');
                break;

            case 1:
                return trans('imonitor::events.labels.on');
                break;

            default:
                return $this->entity->value;
                break;
        }
    }

    public function valueLabelClass()
    {
        switch ($this->entity->value) {
            case 0:
                return 'bg-red';
                break;

            case 1:
                return 'bg-green';
                break;

            default:
                return 'bg-blue';
                break;
        }
    }

}