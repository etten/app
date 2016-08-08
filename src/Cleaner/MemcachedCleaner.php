<?php

namespace Etten\App\Cleaner;

class MemcachedCleaner extends ServiceCleaner
{

	public function clean(callable $container)
	{
		$closure = function (\Memcached $memcached) {
			$memcached->flush();
		};

		$this->doCleanByType(call_user_func($container), \Memcached::class, $closure);
	}

}
