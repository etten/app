<?php

/**
 * This file is part of etten/app.
 * Copyright © 2016 Jaroslav Hranička <hranicka@outlook.com>
 */

namespace Etten\App\DI;

use Etten\App\Console;
use Nette\DI as NDI;

class CleanerExtension extends NDI\CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('cleanerCommand'))
			->setClass(Console\CleanerCommand::class, [
				new NDI\Statement('function () { return ?; }', ['@Nette\DI\Container']),
			])
			->addSetup('setPurge', [
				new NDI\Statement('?->getConfig()[\'cleaner\'][\'purge\']', ['@Etten\App\Configurator']),
			])
			->addSetup('setIgnore', [
				new NDI\Statement('?->getConfig()[\'cleaner\'][\'ignore\']', ['@Etten\App\Configurator']),
			])
			->addSetup('setCleaners', [
				new NDI\Statement('?->getConfig()[\'cleaner\'][\'cleaners\']', ['@Etten\App\Configurator']),
			])
			->addTag('kdyby.console.command');
	}

}
