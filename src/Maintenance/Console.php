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

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function run(string $command, array $parameters = [])
	{
		$container = $this->app->createContainer();

		/** @var SConsole\Application $console */
		$console = $container->getByType(SConsole\Application::class);

		// Correctly set a command name.
		$parameters['command'] = $command;

		return $console->run(
			new SConsole\Input\ArrayInput($parameters),
			new SConsole\Output\NullOutput()
		);
	}

}
