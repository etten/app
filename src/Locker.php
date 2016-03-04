<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

class Locker
{

	/** @var string */
	public $lockFile = '../temp/.maintenance-lock';

	public function isLocked()
	{
		return is_file($this->lockFile);
	}

	public function lock()
	{
		if (!$this->isLocked()) {
			file_put_contents($this->lockFile, NULL);
			echo "App Locked.\n";
		}
	}

	public function unlock()
	{
		if ($this->isLocked()) {
			unlink($this->lockFile);
			echo "App Unlocked.\n";
		}
	}

}
