<?php

namespace Etten\App\Cleaner;

class ApcuCleaner implements Cleaner
{

	public function clean(callable $container)
	{
		if (function_exists('apcu_clear_cache')) {
			apcu_clear_cache();
		}
	}

}
