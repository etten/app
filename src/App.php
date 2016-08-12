<?php

namespace Etten\App;

use Etten\App\Extensions\Extension;
use Etten\App\Maintenance;
use Etten\App\Cleaner;
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
			'developer' => [
				'force' => NULL,
				'cli' => NULL,
				'ips' => [
					'127.0.0.1',
					'::1',
				],
				'token' => '',
				'tokenParameter' => 'etten-maintainer-token',
			],
			'maintainer' => [
				'jobParameter' => 'etten-maintainer-job',
				'namespace' => 'maintainer',
			],
			'load' => [],
			'cleaner' => [
				'purge' => [
					'%tempDir%/cache',
					'%tempDir%/proxies',
				],
				'ignore' => [
					'.gitignore',
					'.gitkeep',
				],
				'cleaners' => [
					Cleaner\OpCacheCleaner::class,
					Cleaner\StorageCleaner::class,
					Cleaner\DoctrineCleaner::class,
				],
			],
		],
	];

	/** @var string[] */
	private $configFiles = [];

	/** @var Extension[] */
	private $extensions = [];

	/** @var bool */
	private $loaded = FALSE;

	/** @var Nette\DI\Container|null */
	private $container;

	public function __construct(string $rootDir)
	{
		$this->rootDir = $rootDir;
	}

	public function addBootstrapFile(string $file, string $name = ''): App
	{
		return $this->addFile($this->bootstrapFiles, $file, $name);
	}

	public function addConfigFile(string $file, string $name = ''): App
	{
		return $this->addFile($this->configFiles, $file, $name);
	}

	public function addExtension(Extension $extension): App
	{
		$this->extensions[] = $extension;
		return $this;
	}

	public function getContainer(): Nette\DI\Container
	{
		if ($this->container === NULL) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}

	public function createMaintainer(): Maintenance\Maintainer
	{
		return new Maintenance\Maintainer($this->createAccessManager(), $this->config['configurator']['maintainer']);
	}

	public function createLocker(): Maintenance\Locker
	{
		return new Maintenance\Locker($this);
	}

	public function createAccessManager(): AccessManager
	{
		$this->load();
		return new AccessManager($this->config['configurator']['developer']);
	}

	public function createConfigurator(): Nette\Configurator
	{
		$this->load();

		$configurator = new Nette\Configurator();
		$configurator->defaultExtensions['configurator'] = ConfiguratorExtension::class;

		foreach ($this->configFiles as $file) {
			$configurator->addConfig($file);
		}

		$configurator->addParameters($this->config['parameters']);
		$configurator->setDebugMode($this->createAccessManager()->isDeveloper());
		$configurator->enableDebugger($this->config['parameters']['logDir']);
		$configurator->setTempDirectory($this->config['parameters']['tempDir']);

		$this->registerRobotLoader($configurator, $this->config['configurator']['load']);

		foreach ($this->extensions as $extension) {
			$extension->onConfiguratorCreate($configurator, $this->config);
		}

		return $configurator;
	}

	public function createContainer(): Nette\DI\Container
	{
		$configurator = $this->createConfigurator();

		$configurator->onCompile[] = function (Nette\Configurator $sender, Nette\DI\Compiler $compiler) {
			$compiler->addDependencies($this->bootstrapFiles);
			$compiler->addConfig($this->config);
		};

		foreach ($this->extensions as $extension) {
			$configurator->onCompile[] = [$extension, 'onConfiguratorCompile'];
		}

		$container = $configurator->createContainer();

		foreach ($this->extensions as $extension) {
			$extension->onContainerCreate($container);
		}

		return $container;
	}

	public function getConfig(): array
	{
		$this->load();
		return $this->config;
	}

	public function run()
	{
		return $this
			->getContainer()
			->getByType(Nette\Application\Application::class)
			->run();
	}

	private function addFile(& $array, $file, $name): App
	{
		if ($name) {
			$array[$name] = $file;
		} else {
			$array[] = $file;
		}

		return $this;
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

		// Back-compatibility
		if (isset($this->config['configurator']['debug'])) {
			$debug = &$this->config['configurator']['debug'];
			$developer = &$this->config['configurator']['developer'];

			if (is_bool($debug)) {
				$developer = $debug;
			} else {
				$debug = array_merge($developer['ips'], $debug);
				$developer['ips'] = $debug;
			}
		}

		foreach ($this->extensions as $extension) {
			$extension->onAppLoad($this->config);
		}

		$this->loaded = TRUE;
	}

	private function expandParameters(string $key)
	{
		return Nette\DI\Helpers::expand($this->config[$key], $this->config['parameters']);
	}

	private function registerRobotLoader(Nette\Configurator $configurator, array $paths)
	{
		if (!$paths) {
			return;
		}

		$configurator->createRobotLoader()
			->addDirectory($paths)
			->register();
	}

}
