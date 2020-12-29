<?php

namespace Kitpages\DataGridBundle\Event;


class DataGridEvent extends AbstractEvent
{
    public function __construct(DataGridEvent $event = null)
    {
        if ($event) {
            $this->data = $event->data;
            $this->isDefaultPrevented = $event->isDefaultPrevented;
            $this->isPropagationStopped = $event->isPropagationStopped;
        }
    }
}
