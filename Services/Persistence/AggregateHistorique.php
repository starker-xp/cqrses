<?php

namespace Starkerxp\CQRSESBundle\Services\Persistence;

class AggregateHistorique
{

    private $aggregateId;
    private $events;

    public function __construct($aggregateId, $events)
    {
        $this->aggregateId = $aggregateId;
        $this->events = $events;
    }

    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    public function getEvents()
    {
        return $this->events;
    }

}
