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

	/** @var string */
	private $tempDir;

	public function __construct(App $app)
	{
		$this->app = $app;
		$this->tempDir = $app->getConfig()['parameters']['tempDir'];
	}

	public function clean()
	{
		$command = new Console\CleanerCommand($this->tempDir, [$this->app, 'createContainer']);
		return $command->run(
			new SConsole\Input\ArrayInput([]),
			new SConsole\Output\NullOutput()
		);
	}

}
