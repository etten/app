<?php

namespace Etten\App\Tests;

use Etten;
use Nette;

/**
 * @property-read Nette\DI\Container $container
 */
abstract class ContainerTestCase extends TestCase
{

	/** @var Etten\App\App */
	public static $app;

	/** @var Nette\DI\Container */
	private static $container;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		if (!self::$container) {
			self::$container = self::createContainer();
		}
	}

	public function getContainer():Nette\DI\Container
	{
		return self::$container;
	}

	private static function createContainer():Nette\DI\Container
	{
		// Suppress DIC warnings (headers already sent etc.).
		return self::ignoreWarnings(function () {
			return self::$app->createContainer();
		});
	}

	private static function ignoreWarnings(callable $callback)
	{
		$errorReporting = error_reporting(E_ERROR);
		$result = call_user_func($callback);
		error_reporting($errorReporting);

		return $result;
	}

}
