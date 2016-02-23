# Etten\App

This package gives you tools for [etten/sandbox](https://github.com/etten/sandbox).

## App

* Don't write long bootstrap code for your App - use App which helps you create a clear code.
* Configuration is set in config file instead of PHP code.
* Additional configuration of `Nette\Configurator` can be given by an Extension and not directly in a long boostrap PHP file.
* See an example bellow.

	```php
	<?php
	// app/bootstrap.php
	
	namespace App;
	
	use Etten;
	
	require __DIR__ . '/../vendor/autoload.php';
	
	// Create with a root directory path (for path mappings)
	$app = new Etten\App\App(__DIR__ . '/..');
	
	// Load boostrap configuration file
	$app->addBootstrapFile(__DIR__ . '/config/bootstrap.neon');
	
	// Load Nette application configuration
	$app->addConfigFile(__DIR__ . '/config/config.neon');
	
	// Local-specific config, eg. database credentials
	// You can redefine it when you add another config with the same name ("local")
	$app->addConfigFile(__DIR__ . '/config/config.local.neon', 'local');
	
	// Load optional Extension which helps you keep this bootstrap file clean
	$app->addExtension(new Etten\App\Extensions\SystemSetup());
	
	return $app;
	```

	```yaml
	# app/config/bootstrap.neon

	parameters:
    	appDir: %rootDir%/app # configure directory paths
    	logDir: %rootDir%/log
    	tempDir: %rootDir%/temp
    	wwwDir: %rootDir%/www
    
    configurator:
    	debug: # IP addresses where debug mode is ALWAYS ON
    		- 192.168.1.1
    
    	load: # Directories which are controlled by Nette\Loaders\RobotLoader
    		- %appDir%
	```

## Tests (code bellow is written for PHPUnit)

* In your apps you should create tests (not like this package which has no tests yet).
* When you create integration tests, you may need a `Nette\DI\Container` instance.
* In ideal situation it should be configured as-in a real application.
* With this packages you can create testing bootstrap file like bellow.

	```php
	<?php
	// tests/boostrap.php
    
    namespace Tests;
    
    use Etten;
    
    /** @var Etten\App\App $app */
    $app = require __DIR__ . '/../app/bootstrap.php';
    
    // Set additional bootstrap configuration
    $app->addBootstrapFile(__DIR__ . '/bootstrap.neon');
    
    // Rewrite "local" configuration file (we don't need exactly the same DB, cache, ...)
    $app->addConfigFile(__DIR__ . '/config.local.neon', 'local');
    
    // Store created App instance for TestCase which provides Nette\DI\Container instance
    Etten\App\Tests\ContainerTestCase::$app = $app;
	```

	```yaml
	# tests/bootstrap.neon

	parameters:
    	testDir: %rootDir%/tests # reconfigure some directory paths
    	logDir: %rootDir%/tests/log
    	tempDir: %rootDir%/tests/temp
    
    configurator:
    	debug: yes # Debugger is always on for tests
    	load: # Add additional directories for Nette\Loaders\RobotLoader
    		- %testDir%
	```

### Unit testing (DI\Container IS NOT required)

* Just create a test a way as usual.
* You can extend your TestCase directly by `\PHPUnit_Framework_TestCase` or `\Etten\App\Tests\TestCase`.

### Integration testing (DI\Container IS required)

* Create TestCase and extend it by `\Etten\App\Tests\ContainerTestCase`.
* It provides you `$container` property where `\Nette\DI\Container` instance is stored.

### Presenter testing (DI\Container IS required)

* Because [Nette Framework](https://nette.org) is a MVP framework, we have special classes for HTTP Request/Response handling - Presenters.
* You can test them easily if you extend you TestCase by `\Etten\App\Tests\PresenterTestCase`.
* Sample test may look like code bellow.

	```php
	<?php
    
    namespace Tests;
    
    use App;
    use Etten;
    use Nette;
    
    class HomepagePresenterTest extends Etten\App\Tests\PresenterContainerTestCase
    {
    
    	protected function getPresenterName():string
    	{
    		// You must configure FQN of currently tested Presenter
    		return 'Front:Homepage';
    	}
    
    	public function testHandleDelete()
    	{
    		// You can test signals
    		$response = $this->runSignal('delete');
    		$this->assertInstanceOf(Nette\Application\Responses\RedirectResponse::class, $response);
    	}
    
    	public function testRenderDefault()
    	{
    		// And actions too
    		$response = $this->runAction();
    		$this->assertInstanceOf(Nette\Application\Responses\TextResponse::class, $response);
    	}
    
    }
	```

### Doctrine testing (DI\Container IS required)

* Create TestCase and extend it by `\Etten\App\Tests\DoctrineTestCase`.
* It provides you `$em` property where `\Doctrine\ORM\EntityManager` instance is stored.
* You can also easily set up SQL fixtures - just call `$this->loadFixture(__DIR__ . '/fixture.sql')`.
