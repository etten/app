<?php

namespace Etten\App;

class Configurator
{

	/** @var array */
	private $config;

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

}
