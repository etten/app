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
	# configure directory paths
	appDir: %rootDir%/app
	logDir: %rootDir%/log
	tempDir: %rootDir%/temp
	wwwDir: %rootDir%/www
	
	developer:
		# Run in development state for specific IPs
		ips:
			- 127.0.0.1
			- ::1

		# Additionally you can set secret token which is read prior to IP address
		# Token is read from HTTP GET "etten-maintainer-token" parameter.
		token: ''


configurator:
	developer: %developer%

	# Directories which are controlled by Nette\Loaders\RobotLoader
	load:
		- %appDir%

```

## Maintenance

* `App` offers helper for maintaining your Application. You can get it via `App::createMaintainer()`.
* `App\Maintenance\Maintainer` allows you simply add event listeners for specific actions like application turn-off,
migrations launcher, application turn-on etc.

### Loading an App without Maintainer

```php
<?php
// web/index.php

namespace Etten\App;

// Uncomment following line for turn-off an App
//return require __DIR__ . '/.maintenance.php';

/** @var App $app */
$app = require __DIR__ . '/../app/bootstrap.php';
$app->run();
```

### Loading an App with Maintainer

```php
<?php
// web/index.php

namespace Etten\App;

use Etten\App\Maintenance;

/** @var App $app */
$app = require __DIR__ . '/../app/bootstrap.php';

$maintainer = $app->createMaintainer();
$locker = $app->createLocker();

// Lock the Application
$maintainer->addJob('disable', function () use ($locker) {
	$locker->lock();
	exit;
});

// Clean caches, setup, migrations, warm-up.
$maintainer->addJob('enable', function () use ($app) {
	// Clean all caches.
	(new Maintenance\Cleaner($app))->clean();
	
	// If you have Doctrine 2.
	(new Maintenance\Console($app))->run('orm:generate-proxies');
	
	// Run new migrations.
	(new Maintenance\Console($app))->run('migrations:continue');
});

// Unlock the Application - it's ready.
$maintainer->addJob('enable', function () use ($locker) {
	$locker->unlock();
	exit;
});

$maintainer->runJobs();

// If locked, show a Maintenance site, otherwise run the App.
if ($locker->isLocked()) {
	require __DIR__ . '/.maintenance.php';
} else {
	$app->run();
}
```

You can trigger Maintainer's jobs by two ways:

* CLI script like `php web/index.php maintainer:disable` where `disable` is above defined job.
* HTTP request like `https://example.com/?etten-maintainer-job=disable`.

So we've triggered `disable` job.
In our case, `Maintenance\Locker` creates a lock. And when lock exists, application is not started and returns STATUS 503.

When you trigger a job `enable` (CLI or HTTP), `Maintenance\Cleaner` cleans the all needed caches, `Maintenance\Console` runs
an [Symfony/Console](http://symfony.com/doc/current/components/console/introduction.html) command `migrations:continue`
(must be registered to the DI Container of our App).

**HTTP jobs** are triggered **only for whitelisted IPs OR request with a secret token**.
They can be defined with config file - a bootstrap of App. See `app/config/bootstrap.neon`.

When possible, prefer CLI jobs (i.e. over SSH).

## Nette DI Extensions

`Etten\App` provides also useful Nette DI Container Extensions.

### CleanerExtension

This allows you easily clean all caches via CLI.

Maintainer (above) uses internally the same Console\Command as the CleanerExtension.

So you can delete all your caches same way also via CLI.

You must register the Extension in a config file:

```yaml
# app/config.neon

extensions:
	etten.cleaner: Etten\App\DI\CleanerExtension

```

And then you are able to run the command via CLI, eg.:

```bash
php web/index.php cache:clean
```

*Concrete path depends on your real application where you use Etten\App.*


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

// Store created App instance for TestCase which provides Nette\DI\Container instance
Etten\App\Tests\ContainerTestCase::$app = $app;

// Set additional bootstrap configuration
$app->addBootstrapFile(__DIR__ . '/bootstrap.neon');

// Rewrite "local" configuration file (we don't need exactly the same DB, cache, ...)
$app->addConfigFile(__DIR__ . '/config.local.neon', 'local');

return $app;
```

```yaml
# tests/bootstrap.neon

parameters:
	# reconfigure some directory paths
	testDir: %rootDir%/tests
	logDir: %rootDir%/tests/log
	tempDir: %rootDir%/tests/temp


configurator:
	load:
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
