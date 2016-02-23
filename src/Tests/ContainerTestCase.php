<?php

namespace Etten\App\Tests;

use Etten;
use Nette;

/**
 * @property-read Nette\DI\Container $container
 */
abstract class ContainerTestCase extends \PHPUnit_Framework_TestCase
{

	/** @var Etten\App\App */
	public static $app;

	/** @var Nette\DI\Container */
	private $container;

	public function __get($name)
	{
		$getter = 'get' . ucfirst($name);
		if (method_exists($this, $getter)) {
			return $getter;
		}

		throw new \RuntimeException(sprintf('Property %s does not found.', $name));
	}

	public function getContainer():Nette\DI\Container
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
