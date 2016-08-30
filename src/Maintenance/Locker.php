<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

use Etten\App\App;

class Locker
{

	/** @var string */
	private $lockFile = '/.maintenance-lock';

	/** @var int Wait time in seconds after a lock. */
	private $lockDelay = 0;

	/** @var int Wait time in seconds before an unlock. */
	private $unlockDelay = 0;

	public function __construct(App $app)
	{
		$this->lockFile = $app->getConfig()['configurator']['locker']['lockFile'];
		$this->lockDelay = $app->getConfig()['configurator']['locker']['lockDelay'];
		$this->unlockDelay = $app->getConfig()['configurator']['locker']['unlockDelay'];
	}

	public function isLocked(): bool
	{
		return is_file($this->lockFile);
	}

	public function lock()
	{
		if (!$this->isLocked()) {
			file_put_contents($this->lockFile, NULL);
			sleep($this->lockDelay);
		}
	}

	public function unlock()
	{
		if ($this->isLocked()) {
			sleep($this->unlockDelay);
			unlink($this->lockFile);
		}
	}

}
