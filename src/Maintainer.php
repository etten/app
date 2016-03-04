<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

class Maintainer
{

	/** @var string[] */
	private $developers = [];

	/** @var array */
	private $server = [];

	/** @var array */
	private $parameters = [];

	/** @var string */
	private $deploymentJobParameter = 'etten-maintainer-job';

	public function __construct(array $developers = [])
	{
		$this->developers = $developers;
		$this->server = $_SERVER;
		$this->parameters = $_GET;
	}

	public function isJob(string $name):bool
	{
		return $this->isDeveloper() && $name === $this->getCurrentJob();
	}

	private function isDeveloper():bool
	{
		return in_array($this->server['REMOTE_ADDR'], $this->developers);
	}

	private function getCurrentJob():string
	{
		return $this->parameters[$this->deploymentJobParameter] ?? '';
	}

}
