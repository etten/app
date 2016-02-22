<?php

namespace Etten\App;

use Nette;

class App
{

	/** @var string */
	private $rootDir = '';

	/** @var string[] */
	private $bootstrapFiles = [];

	/** @var array */
	private $config = [
		'parameters' => [
			'rootDir' => NULL,
			'appDir' => NULL,
			'logDir' => NULL,
			'tempDir' => NULL,
		],
		'configurator' => [
			'debug' => [
				'127.0.0.1',
				'::1',
			],
			'load' => [],
		],
	];

	/** @var string[] */
	private $configFiles = [];

	/** @var AppExtension[] */
	private $extensions = [];

	public function __construct(string $rootDir)
	{
		$this->rootDir = $rootDir;
	}

	public function addBootstrapFile(string $file):App
	{
		$this->bootstrapFiles[] = $file;
		return $this;
	}

	public function addConfigFile(string $file):App
	{
		$this->configFiles[] = $file;
		return $this;
	}

	public function addExtension(AppExtension $extension):App
	{
		$this->extensions[] = $extension;
		return $this;
	}

	public function createContainer():Nette\DI\Container
	{
		$this->load();

		$configurator = $this->createConfigurator();

		$configurator->onCompile[] = function (Nette\Configurator $sender, Nette\DI\Compiler $compiler) {
			$compiler->addDependencies($this->bootstrapFiles);
			$compiler->addConfig($this->config);
		};

		foreach ($this->extensions as $extension) {
			$extension->run($configurator);
		}

		return $configurator->createContainer();
	}

	public function run()
	{
		return $this
			->createContainer()
			->getByType(Nette\Application\Application::class)
			->run();
	}

	private function load()
	{
		$loader = new Nette\DI\Config\Loader();

		foreach ($this->bootstrapFiles as $file) {
			$this->config = Nette\DI\Config\Helpers::merge($loader->load($file), $this->config);
		}

		$this->config['parameters']['rootDir'] = $this->rootDir;
		$this->config['parameters'] = $this->expandParameters('parameters');
		$this->config['configurator'] = $this->expandParameters('configurator');
	}

	private function expandParameters(string $key)
	{
		return Nette\DI\Helpers::expand($this->config[$key], $this->config['parameters']);
	}

	private function createConfigurator()
	{
		$configurator = new Nette\Configurator();
		$configurator->defaultExtensions['configurator'] = ConfiguratorExtension::class;

		foreach ($this->configFiles as $file) {
			$configurator->addConfig($file);
		}

		$configurator->addParameters($this->config['parameters']);

		$configurator->setDebugMode($this->config['configurator']['debug']);
		$configurator->enableDebugger($this->config['parameters']['logDir']);
		$configurator->setTempDirectory($this->config['parameters']['tempDir']);

		$configurator->createRobotLoader()
			->addDirectory($this->config['configurator']['load'])
			->register();

		foreach ($this->extensions as $extension) {
			$extension->load($configurator);
		}

		return $configurator;
	}

}
