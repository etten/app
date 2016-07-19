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
	private $tempDir;

	/** @var string */
	private $lockFile = '/.maintenance-lock';

	public function __construct(App $app)
	{
		$this->tempDir = $app->getConfig()['parameters']['tempDir'];
	}

	public function isLocked(): bool
	{
		return is_file($this->tempDir . $this->lockFile);
	}

	public function lock()
	{
		if (!$this->isLocked()) {
			file_put_contents($this->tempDir . $this->lockFile, NULL);
		}
	}

	public function unlock()
	{
		if ($this->isLocked()) {
			unlink($this->tempDir . $this->lockFile);
		}
	}

}
