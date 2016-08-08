<?php

namespace Etten\App\Cleaner;

use Kdyby\Doctrine;

class DoctrineCleaner extends ServiceCleaner
{

	public function clean(callable $container)
	{
		$closure = function (Doctrine\Tools\CacheCleaner $cleaner) {
			$cleaner->invalidate();
		};

		$this->doCleanByType(call_user_func($container), Doctrine\Tools\CacheCleaner::class, $closure);
	}

}
