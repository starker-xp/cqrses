<?php

namespace Starkerxp\CQRSESBundle\Services\Domain;

abstract class DomainEvents
{

    protected $version = 1;
    private $recordedEvents = [];

    /**
     * On retourne la liste des évènements enregistrés.
     */
    public function getEvenementsEnregistres()
    {
        return $this->recordedEvents;
    }

    /**
     * On supprime la liste des evenements enregistrés.
     *
     * @return void
     */
    public function suppressionEvenementsEnregistres()
    {
        $this->recordedEvents = [];
    }

    /**
     * On enregistre un nouvel évènement.
     *
     * @param \Starkerxp\CQRSESBundle\Domain\EventInterface $event
     */
    public function enregistrementEvenement(EventInterface $event)
    {
        $this->recordedEvents[] = $event;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function getUpdateVersion()
    {
        $this->version++;
        return $this->version;
    }

    abstract public function getAggregateId();

    /**
     * Permet de reconstruire un objet depuis sa collection d'évènements.
     *
     * @param type $eventStore
     * @param type $aggregateHistorique
     * @param type $domain
     *
     * @return type
     */
    public static function reconstitutionDepuis($eventStore, $aggregateHistorique, $domain = null)
    {
        if (!$domain) {
            $domain = static::creeVide($aggregateHistorique->getAggregateId());
        }
        $events = $aggregateHistorique->getEvents();
        foreach ($events as $event) {
            $version = $event->getVersion();
            $domain->apply($event);
            $domain->setVersion($version);
            if ($version % 5 == 0) {
                $domain->creerSnapshot($eventStore);
            }
        }
        return $domain;
    }

    public function creerSnapshot($eventStore)
    {
        $eventStore->creerSnapshot($this);
    }

    abstract public static function creeVide($aggregateId);

    /**
     * Permet d'appliquer un event à l'objet en cours.
     *
     * @param type $event
     */
    public function apply($event)
    {
        $explodeEvent = explode("\\", get_class($event));
        $method = 'apply' . $explodeEvent[count($explodeEvent) - 1];
        $this->$method($event);
    }

    public function applyEvents($events)
    {
        foreach ($events as $event) {
            $this->apply($event);
            $this->setVersion($event->getVersion());
        }
    }

}
