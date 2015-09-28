<?php

namespace Starkerxp\CQRSESBundle\Services\Persistence;

use PDO;
use Starkerxp\CQRSESBundle\Services\Domain\DomainEvents;

class EventStore
{

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * On insère le nouvel évènements dans son espace de stockage.
     *
     * @param type $events
     */
    public function commit($events, $aggregate)
    {
        $stmt = $this->pdo->prepare(
                'INSERT INTO events (aggregate_id, `type`, created_at, `data`, `version`)
             VALUES (:aggregate_id, :type, :created_at, :data, :version)'
        );
        foreach ($events as $key => $event) {
            $version = $event->getVersion();
            $stmt->execute([
                ':aggregate_id' => (string) $event->getAggregateId(),
                ':type' => get_class($event),
                ':created_at' => date('Y-m-d H:i:s'),
                ':data' => base64_encode(serialize($event)),
                ':version' => $version,
            ]);
        }
    }

    /**
     * Récupère l'ensemble des évènements d'un objet, afin de le reconstruire.
     *
     * @param type $id
     *
     * @return \Starkerxp\CQRSESBundle\Services\Persistence\AggregateHistory
     */
    public function getHistoriqueAggregat($id, $version = null)
    {
        $sql = "SELECT * FROM events WHERE aggregate_id = :aggregate_id" . ($version ? " AND version > :version" : "");
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("aggregate_id", (string) $id);
        if ($version) {
            $stmt->bindValue(":version", $version);
        }
        $stmt->execute();
        $events = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $unserialized = unserialize(base64_decode($row['data']));
            // Permet d'inclure la compatibilité avant l'implémentation des versions dans les events.
            $unserialized->setVersionSiNull($row['version']);
            $events[] = $unserialized;
        }
        $stmt->closeCursor();
        return new AggregateHistorique($id, $events);
    }

    public function getSnapshotAggregat($id)
    {
        $sql = "SELECT * FROM events_snapshots WHERE aggregate_id = :aggregate_id ORDER BY version DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("aggregate_id", (string) $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return;
        }
        $domain = unserialize(base64_decode($row['data']));
        return $domain;
    }

    public function creerSnapshot(DomainEvents $aggregate)
    {
        $stmt = $this->pdo->prepare(
                'INSERT INTO events_snapshots (aggregate_id, created_at, `data`, `version`)
             VALUES (:aggregate_id, :created_at, :data, :version)'
        );
        $version = $aggregate->getVersion();
        $stmt->execute([
            ':aggregate_id' => (string) $aggregate->getAggregateId(),
            ':created_at' => date('Y-m-d H:i:s'),
            ':data' => base64_encode(serialize($aggregate)),
            ':version' => $version,
        ]);
    }

}
