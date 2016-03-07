<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Maintenance;

use Etten\App\App;
use Etten\App\string;
use Nette\Caching;
use Nette\DI;

class Cleaner
{

	/** @var string[] */
	public $files = [
		'../temp/cache/Nette.Configurator', // Delete old Configurator - we've changed some files!
		'../temp/cache/latte', // Latte uses custom directory, so force delete it!
	];

	/** @var App */
	private $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function clean()
	{
		$this->cleanApc();
		$this->cleanApcu();
		$this->cleanFiles($this->files);
		$this->cleanStorage($this->app->createContainer());
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

	private function cleanFiles(array $files)
	{
		foreach ($files as $file) {
			$this->deleteFile($file);
		}
	}

	private function deleteFile(string $path)
	{
		if (is_file($path)) {
			unlink($path);

		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				$this->deleteFile($item);
			}
		}
	}

	private function cleanStorage(DI\Container $container)
	{
		/** @var Caching\IStorage $storage */
		$storage = $container->getByType(Caching\IStorage::class);

		$storage->clean([
			Caching\Cache::ALL => TRUE,
		]);
	}

}
