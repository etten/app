<?php

namespace Etten\App;

use Nette;

interface AppExtension
{

	public function load(Nette\Configurator $configurator);

	public function run(Nette\Configurator $configurator);

}
