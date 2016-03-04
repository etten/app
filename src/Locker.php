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
			echo "App Locked.\n";
			file_put_contents($this->lockFile, NULL);
		}
	}

	public function unlock()
	{
		if ($this->isLocked()) {
			echo "App Unlocked.\n";
			unlink($this->lockFile);
		}
	}

}
