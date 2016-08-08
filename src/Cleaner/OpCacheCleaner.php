<?php

namespace Etten\App\Cleaner;

class OpCacheCleaner implements Cleaner
{

	public function clean(callable $container)
	{
		if (function_exists('opcache_reset')) {
			opcache_reset();
		}
	}

}
