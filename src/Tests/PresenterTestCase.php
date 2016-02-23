<?php

namespace Etten\App\Tests;

use Nette;

/**
 * @property-read Nette\Application\IPresenter $presenter
 */
abstract class PresenterTestCase extends ContainerTestCase
{

	/** @var Nette\Application\IPresenter */
	private $presenter;

	/**
	 * @return string Fully Qualified Presenter name (Front:Homepage)
	 */
	abstract protected function getPresenterName():string;

	public function getPresenter():Nette\Application\IPresenter
	{
		if (!$this->presenter) {
			$this->presenter = $this->getPresenterFactory()
				->createPresenter($this->getPresenterName());

			if ($this->presenter instanceof Nette\Application\UI\Presenter) {
				$this->presenter->autoCanonicalize = FALSE;
			}
		}

		return $this->presenter;
	}

	public function runPresenter(
		string $method = 'GET',
		array $params = [],
		array $post = [],
		array $files = [],
		array $flags = []
	):Nette\Application\IResponse
	{
		$request = new Nette\Application\Request($this->getPresenterName(), $method, $params, $post, $files, $flags);
		return $this->getPresenter()->run($request);
	}

	public function runSignal(
		string $name,
		string $method = 'GET',
		array $params = [],
		array $post = []
	):Nette\Application\IResponse
	{
		return $this->runPresenter($method, ['do' => $name] + $params, $post);
	}

	public function runAction(
		string $action = 'default',
		string $method = 'GET',
		array $params = [],
		array $post = []
	):Nette\Application\IResponse
	{
		return $this->runPresenter($method, ['action' => $action] + $params, $post);
	}

	/**
	 * @return Nette\Application\IPresenterFactory
	 */
	private function getPresenterFactory()
	{
		return $this->container
			->getByType(Nette\Application\IPresenterFactory::class);
	}

}
