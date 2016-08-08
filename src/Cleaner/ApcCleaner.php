<?php

namespace Etten\App\Cleaner;

class ApcCleaner implements Cleaner
{

	public function clean(callable $container)
	{
		if (function_exists('apc_clear_cache')) {
			apc_clear_cache();
			apc_clear_cache('user');
		}
	}

}
