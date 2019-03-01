<?php

namespace Modules\Imonitor\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Imonitor\Entities\Event;
use Modules\Imonitor\Http\Requests\CreateEventRequest;
use Modules\Imonitor\Http\Requests\UpdateEventRequest;
use Modules\Imonitor\Repositories\EventRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class EventController extends AdminBaseController
{
    /**
     * @var EventRepository
     */
    private $event;

    public function __construct(EventRepository $event)
    {
        parent::__construct();

        $this->event = $event;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($product_id)
    {
        $events = $this->event->whereProduct($product_id);

        return view('imonitor::admin.events.index', compact('events'));
    }



}
