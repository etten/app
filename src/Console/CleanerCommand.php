<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Console;

use Kdyby\Doctrine;
use Nette\Caching;
use Nette\DI;
use Symfony\Component\Console as SConsole;

class CleanerCommand extends SConsole\Command\Command
{

	/** @var callable */
	private $containerFactory;

	/** @var array */
	private $purge = [];

	/** @var array */
	private $ignore = [];

	public function __construct(callable $containerFactory)
	{
		parent::__construct('cache:clean');
		$this->containerFactory = $containerFactory;
	}

	/**
	 * @param array $purge
	 * @return $this
	 */
	public function setPurge(array $purge)
	{
		$this->purge = $purge;
		return $this;
	}

	/**
	 * @param array $ignore
	 * @return $this
	 */
	public function setIgnore(array $ignore)
	{
		$this->ignore = $ignore;
		return $this;
	}

	protected function execute(SConsole\Input\InputInterface $input, SConsole\Output\OutputInterface $output)
	{
		$this->cleanApc();
		$this->cleanApcu();
		$this->cleanOpCache();
		$this->cleanDirectories();
		$this->cleanServices();

		$output->writeln('Cache cleaned.');
	}

	private function cleanApc()
	{
		if (function_exists('apc_clear_cache')) {
			apc_clear_cache();
			apc_clear_cache('user');
		}
	}

	private function cleanApcu()
	{
		if (function_exists('apcu_clear_cache')) {
			apcu_clear_cache();
		}
	}

	private function cleanOpCache()
	{
		if (function_exists('opcache_reset')) {
			opcache_reset();
		}
	}

	private function cleanDirectories()
	{
		foreach ($this->purge as $directory) {
			$this->cleanFile($directory);
		}
	}

	private function cleanFile(string $path)
	{
		if (in_array(basename($path), $this->ignore)) {
			return;
		}

		if (is_file($path)) {
			unlink($path);

		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				$this->cleanFile($item);
			}
		}
	}

	private function cleanServices()
	{
		$container = $this->createContainer();

		/** @var Caching\IStorage $storage */
		$storage = $container->getByType(Caching\IStorage::class, FALSE);
		if ($storage) {
			$storage->clean([
				Caching\Cache::ALL => TRUE,
			]);
		}

		/** @var Doctrine\Tools\CacheCleaner $cacheCleaner */
		$cacheCleaner = $container->getByType(Doctrine\Tools\CacheCleaner::class, FALSE);
		if ($cacheCleaner) {
			$cacheCleaner->invalidate();
		}
	}

	private function createContainer():DI\Container
	{
		return call_user_func($this->containerFactory);
	}

}
