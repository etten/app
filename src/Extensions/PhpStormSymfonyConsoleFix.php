<?php

namespace Etten\App\Extensions;

use Etten\App;
use Nette;

class PhpStormSymfonyConsoleFix implements App\AppExtension
{

	public function load(Nette\Configurator $configurator)
	{
		// PhpStorm & Symfony Console hotfix
		// See https://youtrack.jetbrains.com/issue/WI-29627
		if (isset($argv[1]) && $argv[1] === '-V') {
			die('Symfony version 2.8.0');
		}
	}

	public function run(Nette\Configurator $configurator)
	{

	}

}
