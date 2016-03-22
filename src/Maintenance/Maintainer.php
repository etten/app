<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

class Maintainer
{

	/** @var array */
	private $server = [];

	/** @var array */
	private $parameters = [];

	/** @var array */
	private $config = [
		'ips' => [],
		'token' => '',
		'jobParameter' => 'etten-maintainer-job',
		'tokenParameter' => 'etten-maintainer-token',
	];

	/** @var array */
	private $jobs = [];

	public function __construct(array $config = [])
	{
		$this->config = array_merge($this->config, $config);
		$this->server = $_SERVER;
		$this->parameters = $_GET;
	}

	public function addJob(string $name, \Closure $func)
	{
		$this->jobs[] = [$name, $func];
		return $this;
	}

	public function runJobs()
	{
		foreach ($this->jobs as $job) {
			list($name, $func) = $job;

			if ($this->isJob($name)) {
				$this->runLongScript($func);
			}
		}
	}

	public function isJob(string $name):bool
	{
		return
			$this->isDeveloper()
			&& $this->isTokenOk()
			&& $this->isJobOk($name);
	}

	private function isDeveloper():bool
	{
		$whiteList = (array)$this->config['ips'];
		return in_array($this->server['REMOTE_ADDR'] ?? '', $whiteList);
	}

	private function isTokenOk():bool
	{
		return $this->getParameter($this->config['tokenParameter']) === $this->config['token'];
	}

	private function isJobOk(string $name):bool
	{
		return $this->getParameter($this->config['jobParameter']) === $name;
	}

	private function getParameter(string $name):string
	{
		return $this->parameters[$name] ?? '';
	}

	private function runLongScript(callable $callback)
	{
		$maxExecTime = ini_set('max_execution_time', 12 * 60); // max 12 hours

		try {
			$callback();
		} finally {
			ini_set('max_execution_time', $maxExecTime);
		}
	}

}
