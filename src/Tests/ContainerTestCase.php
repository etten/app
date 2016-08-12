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

	public function getContainer(): Nette\DI\Container
	{
		// Suppress DIC warnings (headers already sent etc.).
		return self::ignoreWarnings(function () {
			return self::$app->getContainer();
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
