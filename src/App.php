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
			'maintainer' => [
				'token' => '',
			],
		],
	];

	/** @var string[] */
	private $configFiles = [];

	/** @var AppExtension[] */
	private $extensions = [];

	/** @var bool */
	private $loaded = FALSE;

	/** @var Maintainer|null */
	private $maintainer;

	public function __construct(string $rootDir)
	{
		$this->rootDir = $rootDir;
	}

	public function addBootstrapFile(string $file, string $name = ''):App
	{
		return $this->addFile($this->bootstrapFiles, $file, $name);
	}

	public function addConfigFile(string $file, string $name = ''):App
	{
		return $this->addFile($this->configFiles, $file, $name);
	}

	public function addExtension(AppExtension $extension):App
	{
		$this->extensions[] = $extension;
		return $this;
	}

	public function isMaintainerJob(string $name):bool
	{
		return $this->getMaintainer()->isJob($name);
	}

	public function createContainer():Nette\DI\Container
	{
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

	private function addFile(& $array, $file, $name):App
	{
		if ($name) {
			$array[$name] = $file;
		} else {
			$array[] = $file;
		}

		return $this;
	}

	private function getMaintainer():Maintainer
	{
		if (!$this->maintainer) {
			$this->load();

			$config = [];
			$config['ips'] = $this->config['configurator']['debug'];
			$config += $this->config['configurator']['maintainer'];

			$this->maintainer = new Maintainer($config);
		}

		return $this->maintainer;
	}

	private function load()
	{
		if ($this->loaded) {
			return;
		}

		$loader = new Nette\DI\Config\Loader();

		foreach ($this->bootstrapFiles as $file) {
			$this->config = Nette\DI\Config\Helpers::merge($loader->load($file), $this->config);
		}

		$this->config['parameters']['rootDir'] = $this->rootDir;
		$this->config['parameters'] = $this->expandParameters('parameters');
		$this->config['configurator'] = $this->expandParameters('configurator');

		$this->loaded = TRUE;
	}

	private function expandParameters(string $key)
	{
		return Nette\DI\Helpers::expand($this->config[$key], $this->config['parameters']);
	}

	private function createConfigurator()
	{
		$this->load();

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
