<?php

namespace Etten\App\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

	public function __get($name)
	{
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return $this->$getter();
		}

		throw new \RuntimeException(sprintf('Property %s does not found.', $name));
	}

}
