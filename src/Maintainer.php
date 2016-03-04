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

	/** @var string */
	public $deploymentUrlRegExp = '';

	/** @var string */
	public $deploymentJobParameter = 'etten-maintainer-job';

	public function __construct(array $server = [], array $parameters = [])
	{
		$this->server = $server ?: $_SERVER;
		$this->parameters = $parameters ?: $_GET;
	}

	public function isJob(string $name):bool
	{
		if ($this->isDeveloper() && $this->isDeployment()) {
			return $name === $this->getCurrentJob();
		}

		return FALSE;
	}

	public function isDeveloper():bool
	{
		return in_array($this->server['REMOTE_ADDR'], $this->developers);
	}

	public function isDeployment():bool
	{
		return preg_match('~' . $this->deploymentUrlRegExp . '~', $this->server['REQUEST_URI']);
	}

	private function getCurrentJob():string
	{
		return $this->parameters[$this->deploymentJobParameter] ?? '';
	}

}
