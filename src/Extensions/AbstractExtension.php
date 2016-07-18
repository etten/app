<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Extensions;

use Nette;

abstract class AbstractExtension implements Extension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator, array $config)
	{
	}

	public function onConfiguratorCompile(Nette\Configurator $sender, Nette\DI\Compiler $compiler)
	{
	}

	public function onContainerCreate(Nette\DI\Container $container)
	{
	}

}
