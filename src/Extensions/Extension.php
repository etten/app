<?php

namespace Etten\App\Extensions;

use Nette;

interface Extension
{

    public function onAppLoad(array $config);

	public function onConfiguratorCreate(Nette\Configurator $configurator, array $config);

	public function onConfiguratorCompile(Nette\Configurator $sender, Nette\DI\Compiler $compiler);

	public function onContainerCreate(Nette\DI\Container $container);

}
