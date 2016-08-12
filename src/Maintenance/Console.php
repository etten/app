<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

use Etten\App\App;
use Symfony\Component\Console as SConsole;

class Console
{

	/** @var App */
	private $app;

	/** @var bool */
	private $autoExit = TRUE;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function setAutoExit(bool $autoExit)
	{
		$this->autoExit = $autoExit;
	}

	public function run(string $command, array $parameters = [])
	{
		$container = $this->app->getContainer();

		/** @var SConsole\Application $console */
		$console = $container->getByType(SConsole\Application::class);

		// Correctly set a command name.
		$parameters['command'] = $command;

		$exitCode = $console->run(
			new SConsole\Input\ArrayInput($parameters),
			new SConsole\Output\NullOutput()
		);

		// Force auto-exit (it's true by default in Symfony/Console, but false in Kdyby/Console).
		if ($this->autoExit && $exitCode > 0) {
			exit($exitCode);
		}

		return $exitCode;
	}

}
