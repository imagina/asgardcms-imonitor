<?php


namespace Modules\Imonitor\Entities;


class Status
{
    const ACTIVE = 0;
    const COMPLETE = 1;


    /**
     * @var array
     */
    private $statuses = [];

    public function __construct()
    {
        $this->statuses = [
            self::ACTIVE => trans('imonitor::status.active'),
            self::COMPLETE => trans('imonitor::status.complete'),

        ];
    }

    /**
     * Get the available statuses
     * @return array
     */
    /*listar*/
    public function lists()
    {
        return $this->statuses;
    }

    /**
     * Get the post status
     * @param int $statusId
     * @return string
     */
    public function get($statusId)
    {
        if (isset($this->statuses[$statusId])) {
            return $this->statuses[$statusId];
        }

        return $this->statuses[self::ACTIVE];
    }
}

