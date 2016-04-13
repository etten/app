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
		'ips' => [],
		'token' => '',
		'tokenParameter' => 'etten-maintainer-token',
	];

	/** @var array */
	private $server = [];

	/** @var array */
	private $parameters = [];

	public function __construct(array $config = [])
	{
		$this->config = array_merge($this->config, $config);
		$this->server = $_SERVER;
		$this->parameters = $_GET;
	}

	public function isDeveloper():bool
	{
		if ($this->isTokenOk()) {
			return TRUE;
		}

		$whiteList = (array)$this->config['ips'];
		$remoteIp = $this->server['REMOTE_ADDR'] ?? '';
		return in_array($remoteIp, $whiteList);
	}

	private function isTokenOk():bool
	{
		return $this->getParameter($this->config['tokenParameter']) === $this->config['token'];
	}

	private function getParameter(string $name):string
	{
		return $this->parameters[$name] ?? '';
	}

}
