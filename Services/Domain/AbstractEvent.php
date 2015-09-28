<?php

namespace Starkerxp\CQRSESBundle\Services\Domain;

abstract class AbstractEvent implements EventInterface
{

    protected $version;

    public function getEvent()
    {
        return $this;
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

    public function setVersionSiNull($version)
    {
        if (empty($this->version)) {
            $this->version = $version;
        }
        return $this;
    }

}
