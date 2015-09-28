<?php

namespace Starkerxp\CQRSESBundle\Services\Persistence;

abstract class AbstractEventStore
{

    protected $eventStore;
    protected $projection;

    abstract public function get($aggregateId);

    public function __construct($eventStore, $projection)
    {
        $this->eventStore = $eventStore;
        $this->projection = $projection;
    }

    /**
     * Permet de jouer un évènement dans l'Event Store
     *
     * @param type $aggregate
     *
     * @return void
     */
    public function ajouter($aggregate)
    {
        $events = $aggregate->getEvenementsEnregistres();
        if (empty($events)) {
            return;
        }
        $this->eventStore->commit($events, $aggregate);
        $this->projection->project($events);
        $aggregate->suppressionEvenementsEnregistres();
    }

}
