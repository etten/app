<?php

namespace Etten\App;

use Nette;

interface AppExtension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator);

	public function onContainerCreate(Nette\DI\Container $container);

}
