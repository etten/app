<?php

namespace Etten\App\Cleaner;

use Nette\Caching;

class StorageCleaner extends ServiceCleaner
{

	public function clean(callable $container)
	{
		$closure = function (Caching\IStorage $storage) {
			$storage->clean([
				Caching\Cache::ALL => TRUE,
			]);
		};

		$this->doCleanByType(call_user_func($container), Caching\IStorage::class, $closure);
	}

}
