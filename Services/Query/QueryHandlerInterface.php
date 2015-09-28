<?php

namespace Starkerxp\CQRSESBundle\Services\Query;

use Starkerxp\CQRSESBundle\Services\Bus\HandlerInterface;

interface QueryHandlerInterface extends HandlerInterface
{

    public function handle(QueryInterface $command);
}
