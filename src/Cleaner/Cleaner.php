<?php

namespace Etten\App\Cleaner;

interface Cleaner
{

	public function clean(callable $container);

}
