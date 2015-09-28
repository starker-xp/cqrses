<?php

namespace Starkerxp\CQRSESBundle\Services\Domain;

interface EventAdapterInterface
{

    public function run(AbstractEvent $event);
}
