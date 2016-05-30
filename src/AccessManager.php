<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

class AccessManager
{

	/** @var array */
	private $config = [
		'force' => NULL,
		'cli' => NULL,
		'ips' => [],
		'token' => '',
		'tokenParameter' => 'etten-maintainer-token',
	];

	/** @var array */
	private $server = [];

	/** @var array */
	private $parameters = [];

	/**
	 * @param array|bool $config
	 * @param array|null $server
	 * @param array|null $parameters
	 */
	public function __construct($config = [], array $server = NULL, array $parameters = NULL)
	{
		if (is_array($config)) {
			$this->config = array_merge($this->config, $config);
		} else {
			$this->config['force'] = !!$config;
		}

		$this->server = ($server === NULL) ?
			$_SERVER :
			$server;

		$this->parameters = ($parameters === NULL) ?
			$_GET :
			$parameters;
	}

	public function isDeveloper():bool
	{
		// Allow "force" option.
		if (isset($this->config['force'])) {
			return $this->config['force'];
		}

		// Special option for CLI-mode.
		if (php_sapi_name() === 'cli' && isset($this->config['cli'])) {
			return $this->config['cli'];
		}

		// Find out from secret token
		if ($this->isTokenOk()) {
			return TRUE;
		}

		// Find out from IP address.
		$whiteList = (array)$this->config['ips'];
		$remoteIp = $this->server['REMOTE_ADDR'] ?? '';
		return in_array($remoteIp, $whiteList);
	}

	private function isTokenOk():bool
	{
		return $this->config['token'] && $this->getParameter($this->config['tokenParameter']) === $this->config['token'];
	}

	private function getParameter(string $name):string
	{
		return $this->parameters[$name] ?? '';
	}

}
