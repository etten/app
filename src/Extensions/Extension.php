<?php

namespace Etten\App\Extensions;

use Nette;

interface Extension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator);

	public function onContainerCreate(Nette\DI\Container $container);

}
