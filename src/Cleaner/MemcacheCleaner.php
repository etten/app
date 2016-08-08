<?php

namespace Etten\App\Cleaner;

class MemcacheCleaner extends ServiceCleaner
{

	public function clean(callable $container)
	{
		$closure = function (\Memcache $memcache) {
			$memcache->flush();
		};

		$this->doCleanByType(call_user_func($container), 'Memcache', $closure);
	}

}
