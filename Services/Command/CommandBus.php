<?php

namespace Starkerxp\CQRSESBundle\Services\Command;

use Starkerxp\CQRSESBundle\Services\Bus\AbstractBus;
use Starkerxp\CQRSESBundle\Services\Command\CommandHandlerInterface;

class CommandBus extends AbstractBus
{

    protected $handlers = [];

    public function handle(CommandInterface $action)
    {
        $actionHandler = $this->handlers[get_class($action)];
        $actionHandler->handle($action);
    }

    public function register(CommandHandlerInterface $handler)
    {
        $commandClass = $this->str_lreplace('Handler', 'Command', get_class($handler));
        $this->handlers[$commandClass] = $handler;
    }

}
