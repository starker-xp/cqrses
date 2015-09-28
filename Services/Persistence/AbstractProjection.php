<?php

namespace Starkerxp\CQRSESBundle\Services\Persistence;

use Doctrine\DBAL\Connection;

abstract class AbstractProjection
{

    private $pdo;

    public function __construct(Connection $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function project($events)
    {
        foreach ($events as $event) {
            $className = get_class($event);
            $arrayClass = explode("\\", $className);
            $projectMethod = 'project' . $arrayClass[count($arrayClass) - 1];
            $this->$projectMethod($event);
        }
    }

}
