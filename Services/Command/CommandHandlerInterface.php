<?php

namespace Starkerxp\CQRSESBundle\Services\Command;

use Starkerxp\CQRSESBundle\Services\Bus\HandlerInterface;

interface CommandHandlerInterface extends HandlerInterface
{

    public function handle(CommandInterface $command);
}
