<?php

namespace Etten\App;

use Nette;

class ConfiguratorExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('configurator'))
			->setClass(Configurator::class, [$this->getConfig()]);
	}

}
