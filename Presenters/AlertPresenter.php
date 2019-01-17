<?php

namespace Modules\Imonitor\Presenters;


use Modules\Imonitor\Entities\Status;
use Laracasts\Presenter\Presenter;

class AlertPresenter extends Presenter
{
    protected $status;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->alert = app('Modules\Imonitor\Repositories\AlertRepository');
        $this->status = app('Modules\Imonitor\Entities\Status');
    }

    /**
     * Get the alert status
     * @return string
     */
    public function status()
    {
        return $this->status->get($this->entity->status);
    }

    public function statusLabelClass()
    {
        switch ($this->entity->status) {
            case Status::ACTIVE:
                return 'bg-red';
                break;

            case Status::COMPLETE:
                return 'bg-green';
                break;

            default:
                return 'bg-red';
                break;
        }
    }

}