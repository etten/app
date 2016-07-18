<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

use Etten\App\App;
use Etten\App\Console;
use Symfony\Component\Console as SConsole;

class Cleaner
{

	/** @var App */
	private $app;

	/** @var array */
	private $config;

	public function __construct(App $app)
	{
		$this->app = $app;

		// Do it this way because we don't need initialize DIC now.
		$this->config = $this->app->getConfig()['configurator']['cleaner'];
	}

	public function clean()
	{
		$command = new Console\CleanerCommand([$this->app, 'createContainer']);
		$command->setPurge($this->config['purge']);
		$command->setIgnore($this->config['ignore']);

		return $command->run(
			new SConsole\Input\ArrayInput([]),
			new SConsole\Output\NullOutput()
		);
	}

}
