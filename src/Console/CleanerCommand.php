<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\Console;

use Etten\App\Cleaner\Cleaner;
use Nette\DI;
use Symfony\Component\Console as SConsole;

class CleanerCommand extends SConsole\Command\Command
{

	/** @var callable */
	private $containerFactory;

	/** @var string[] */
	private $purge = [];

	/** @var string[] */
	private $ignore = [];

	/** @var string[] */
	private $cleaners = [];

	/** @var DI\Container|null */
	private $container;

	public function __construct(callable $containerFactory)
	{
		parent::__construct('cache:clean');
		$this->containerFactory = $containerFactory;
	}

	public function setPurge(array $purge)
	{
		$this->purge = $purge;
	}

	public function setIgnore(array $ignore)
	{
		$this->ignore = $ignore;
	}

	public function setCleaners(array $cleaners)
	{
		$this->cleaners = $cleaners;
	}

	public function getContainer(): DI\Container
	{
		if ($this->container === NULL) {
			$this->container = call_user_func($this->containerFactory);
		}

		return $this->container;
	}

	protected function execute(SConsole\Input\InputInterface $input, SConsole\Output\OutputInterface $output)
	{
		$this->cleanDirectories();

		foreach ($this->cleaners as $cleaner) {
			$class = new $cleaner;
			if ($class instanceof Cleaner) {
				$class->clean([$this, 'getContainer']);
			} else {
				throw new \Exception(sprintf('%s expected, %s given.', Cleaner::class, get_class($class)));
			}
		}

		$output->writeln('Cache cleaned.');
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

}
