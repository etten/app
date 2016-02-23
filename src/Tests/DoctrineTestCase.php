<?php

namespace Etten\App\Tests;

use Doctrine\ORM;
use Nette;

/**
 * @property-read ORM\EntityManager $em
 */
class DoctrineTestCase extends ContainerTestCase
{

	/** @var ORM\EntityManager */
	private $em;

	public function getEm():ORM\EntityManager
	{
		if (!$this->em) {
			$this->em = $this->container
				->getByType(ORM\EntityManager::class);
		}

		return $this->em;
	}

	protected function tearDown()
	{
		parent::tearDown();

		if ($this->em) {
			$this->em->close();
		}
	}

}
