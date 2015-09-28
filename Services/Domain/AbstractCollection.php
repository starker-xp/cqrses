<?php

namespace Starkerxp\CQRSESBundle\Services\Domain;

use ArrayObject;
use Countable;

class AbstractCollection implements Countable
{

    protected $collection;

    public function __construct()
    {
        $this->collection = new ArrayObject([]);
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function count()
    {
        return count($this->collection);
    }

}
