<?php

namespace Etten\App\Extensions;

use Nette;

class PhpStormSymfonyConsoleFix implements Extension
{

	public function onConfiguratorCreate(Nette\Configurator $configurator)
	{
		// PhpStorm 10 & Symfony Console hotfix
		// See https://youtrack.jetbrains.com/issue/WI-29627

		if (php_sapi_name() === 'cli') {
			$argv = $_SERVER['argv'];
			if (isset($argv[1]) && $argv[1] === '-V') {
				die('Symfony version 2.8.0');
			}
		}
	}

	public function onContainerCreate(Nette\DI\Container $container)
	{

	}

}
