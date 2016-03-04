<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

class Maintainer
{

	/** @var array */
	public $server = [];

	/** @var array */
	public $parameters = [];

	/** @var string[] */
	public $developers = [
		'127.0.0.1',
		'::1',
	];

	/** @var \Closure[][] */
	public $jobs = [[]];

	/** @var string */
	public $deploymentUrlRegExp = '';

	/** @var string */
	public $deploymentJobParameter = 'etten-app-job';

	public function __construct(array $server = [], array $parameters = [])
	{
		$this->server = $server ?: $_SERVER;
		$this->parameters = $parameters ?: $_GET;
	}

	public function run()
	{
		if ($this->isDeveloper() && $this->isDeployment()) {
			foreach ($this->getJobs() as $job) {
				$job($this);
			}
		}
	}

	public function isDeveloper():bool
	{
		return in_array($this->server['REMOTE_ADDR'], $this->developers);
	}

	public function isDeployment():bool
	{
		return preg_match('~' . $this->deploymentUrlRegExp . '~', $this->server['REQUEST_URI']);
	}

	private function getJobs()
	{
		return $this->jobs[$this->getJobName()];
	}

	private function getJobName():string
	{
		return $this->parameters[$this->deploymentJobParameter];
	}

}
