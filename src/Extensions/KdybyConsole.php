<?php

namespace Etten\App\Extensions;

use Kdyby\Console\DI\BootstrapHelper;
use Nette;

class KdybyConsole extends AbstractExtension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator, array $config)
	{
		BootstrapHelper::setupMode($configurator);
	}

}
