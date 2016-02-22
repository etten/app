<?php

namespace Etten\App\Tests;

use Nette;

abstract class PresenterTestCase extends TestCase
{

	/** @var Nette\Application\IPresenter */
	protected $presenter;

	/**
	 * @return string Fully Qualified Presenter name (Front:Homepage)
	 */
	abstract protected function getPresenterName():string;

	protected function setUp()
	{
		parent::setUp();

		$this->presenter = $this->getPresenterFactory()
			->createPresenter($this->getPresenterName());

		$this->presenter->autoCanonicalize = FALSE;
	}

	protected function runPresenter(
		string $method = 'GET',
		array $params = [],
		array $post = [],
		array $files = [],
		array $flags = []
	):Nette\Application\IResponse
	{
		$request = new Nette\Application\Request($this->getPresenterName(), $method, $params, $post, $files, $flags);

		return $this->presenter->run($request);
	}

	protected function runSignal(
		string $name,
		string $method = 'GET',
		array $params = [],
		array $post = []
	):Nette\Application\IResponse
	{
		return $this->runPresenter($method, ['do' => $name] + $params, $post);
	}

	protected function runAction(
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
		return $this
			->getContainer()
			->getByType(Nette\Application\IPresenterFactory::class);
	}

}
