<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Console;

use Nette\Caching;
use Nette\DI;
use Symfony\Component\Console as SConsole;

class CleanerCommand extends SConsole\Command\Command
{

	/** @var string */
	private $tempPath;

	/** @var callable */
	private $containerFactory;

	/** @var array */
	private $directories = [
		'/cache', // Standard cache directory.
		'/proxies', // Kdyby/Doctrine proxies default directory must be purged.
	];

	public function __construct(string $tempPath, callable $containerFactory)
	{
		parent::__construct('cache:clean');
		$this->tempPath = $tempPath;
		$this->containerFactory = $containerFactory;
	}

	/**
	 * @param string $path
	 * @return $this
	 */
	public function addDirectory(string $path)
	{
		$this->directories[] = $path;
		return $this;
	}

	protected function execute(SConsole\Input\InputInterface $input, SConsole\Output\OutputInterface $output)
	{
		$this->cleanApc();
		$this->cleanApcu();
		$this->cleanDirectories();
		$this->cleanNetteStorage();

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

	private function cleanDirectories()
	{
		$directories = array_map(function (string $file) :string {
			return $this->tempPath . $file;
		}, $this->directories);

		foreach ($directories as $directory) {
			$this->cleanFile($directory);
		}
	}

	private function cleanFile(string $path)
	{
		if (is_file($path)) {
			unlink($path);

		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				$this->cleanFile($item);
			}
		}
	}

	private function cleanNetteStorage()
	{
		$container = $this->createContainer();

		/** @var Caching\IStorage $storage */
		$storage = $container->getByType(Caching\IStorage::class);

		$storage->clean([
			Caching\Cache::ALL => TRUE,
		]);
	}

	private function createContainer():DI\Container
	{
		return call_user_func($this->containerFactory);
	}

}
