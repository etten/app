<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App;

class Cleaner
{

	/** @var string[] */
	private $files = [];

	public function __construct(array $files)
	{
		$this->files = $files;
	}

	public function clean()
	{
		$this->cleanApc();
		$this->cleanApcu();
		$this->cleanFiles();
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

	private function cleanFiles()
	{
		foreach ($this->files as $file) {
			$this->deleteFile($file);
		}
	}

	private function deleteFile($path)
	{
		if (is_file($path)) {
			unlink($path);

		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				$this->deleteFile($item);
			}
		}
	}

}
