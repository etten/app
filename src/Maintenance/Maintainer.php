<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

use Etten\App\AccessManager;

class Maintainer
{

	/** @var array */
	private $config = [
		'jobParameter' => 'etten-maintainer-job',
		'namespace' => 'maintainer',
	];

	/** @var array */
	private $parameters;

	/** @var AccessManager */
	private $accessManager;

	/** @var array */
	private $jobs = [];

	public function __construct(AccessManager $accessManager, array $config = [])
	{
		$this->config = array_merge($this->config, $config);
		$this->parameters = $_GET;
		$this->accessManager = $accessManager;
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

	public function isJob(string $name): bool
	{
		if (php_sapi_name() === 'cli') {
			return $this->isCliJob($name);
		} else {
			return $this->isHttpJob($name);
		}
	}

	private function isCliJob(string $name): bool
	{
		$argv = $_SERVER['argv'];

		// strip the script name
		array_shift($argv);

		if (empty($argv)) {
			return FALSE;
		}

		$command = array_shift($argv);
		$parts = explode(':', $command);

		$namespace = array_shift($parts);
		if ($namespace === $this->config['namespace']) {
			$job = array_shift($parts);
			return $job === $name;
		}

		return FALSE;
	}

	private function isHttpJob(string $name): bool
	{
		if (!$this->accessManager->isDeveloper()) {
			return FALSE;
		}

		return $this->getParameter($this->config['jobParameter']) === $name;
	}

	private function getParameter(string $name): string
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
