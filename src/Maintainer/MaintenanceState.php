<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintainer;

class MaintenanceState implements MaintainerExtension
{

	/** @var string */
	public $maintenanceFile = '../temp/.maintenance';

	public function attached(Maintainer $maintainer)
	{
		$maintainer->jobs['disable'][] = [$this, 'activate'];
		$maintainer->jobs['enable'][] = [$this, 'deactivate'];
	}

	public function isActivated()
	{
		return is_file($this->maintenanceFile);
	}

	public function activate()
	{
		if (!$this->isActivated()) {
			file_put_contents($this->maintenanceFile, NULL);
		}
	}

	public function deactivate()
	{
		if ($this->isActivated()) {
			unlink($this->maintenanceFile);
		}
	}

}
