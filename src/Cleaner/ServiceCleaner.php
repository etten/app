<?php

namespace Etten\App\Cleaner;

use Nette\DI\Container;

abstract class ServiceCleaner implements Cleaner
{

	protected function doCleanByType(Container $container, string $type, \Closure $closure)
	{
		$names = $container->findByType($type);
		foreach ($names as $name) {
			$service = $container->getService($name);
			$closure($service);
		}
	}

}
