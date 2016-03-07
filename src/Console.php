<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

use Symfony\Component\Console as SConsole;

class Console
{

	/** @var App */
	private $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function run(string $commandName, array $argv = [])
	{
		$container = $this->app->createContainer();

		/** @var SConsole\Application $console */
		$console = $container->getByType(SConsole\Application::class);

		$command = $console->find($commandName);

		return $command->run(
			new SConsole\Input\ArgvInput($argv),
			new SConsole\Output\NullOutput()
		);
	}

}
