<?php namespace Streams\Platform\Support;

use Illuminate\Log\Writer;
use Illuminate\Events\Dispatcher;

class EventDispatcher
{
    /**
     * The Dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $event;

    /**
     * The writer instance.
     *
     * @var Writer
     */
    protected $log;

    /**
     * Create a new EventDispatcher instance.
     *
     * @param Dispatcher $event
     * @param Writer     $log
     */
    function __construct(Dispatcher $event, Writer $log)
    {
        $this->event = $event;
        $this->log   = $log;
    }

    /**
     * Dispatch all raised events.
     *
     * @param array $events
     */
    public function dispatch(array $events)
    {
        foreach ($events as $event) {
            $eventName = $this->getEventName($event);

            $this->event->fire($eventName, $event);

            $this->log->info("{$eventName} was fired.");
        }
    }

    /**
     * Make the fired event name look more object-oriented.
     *
     * @param $event
     * @return string
     */
    protected function getEventName($event)
    {
        $event = str_replace('\\', '.', get_class($event));

        if (substr($event, -5) == 'Event') {
            $event = substr($event, 0, -5);
        }

        return $event;
    }
}
