<?php

namespace Etten\App\Tests;

use Etten;
use Nette;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

	/** @var Etten\App\App */
	public static $app;

	/** @var Nette\DI\Container */
	private $container;

	protected function getContainer():Nette\DI\Container
	{
		if (!$this->container) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}

	private function createContainer():Nette\DI\Container
	{
		// Suppress DIC warnings (headers already sent etc.).
		return $this->ignoreWarnings(function () {
			return self::$app->createContainer();
		});
	}

	private function ignoreWarnings(callable $callback)
	{
		$errorReporting = error_reporting(E_ERROR);
		$result = call_user_func($callback);
		error_reporting($errorReporting);

		return $result;
	}

}
