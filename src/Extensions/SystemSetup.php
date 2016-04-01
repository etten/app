<?php

namespace Etten\App\Extensions;

use Etten\App;
use Nette;

class SystemSetup implements App\AppExtension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator)
	{
		umask(0); // 0666 file, 0777 folder
		mb_internal_encoding('UTF-8'); // forces UTF-8 internal encoding
	}

	public function onContainerCreate(Nette\DI\Container $container)
	{

	}

}
