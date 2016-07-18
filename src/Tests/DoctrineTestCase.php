<?php

namespace Etten\App\Tests;

use Doctrine\ORM;

/**
 * @property-read ORM\EntityManager $em
 */
abstract class DoctrineTestCase extends ContainerTestCase
{

	/** @var ORM\EntityManager */
	private $em;

	public function getEm() :ORM\EntityManager
	{
		if (!$this->em) {
			$this->em = $this->container
				->getByType(ORM\EntityManager::class);
		}

		return $this->em;
	}

	protected function loadFixture(string $file)
	{
		$this->dropDatabase();

		$connection = $this->getEm()->getConnection();
		$statement = $connection->prepare(file_get_contents($file));
		$statement->execute();
	}

	protected function tearDown()
	{
		parent::tearDown();

		if ($this->em) {
			$this->em->clear();
		}
	}

	private function dropDatabase()
	{
		$schemaTool = new ORM\Tools\SchemaTool($this->getEm());
		$schemaTool->dropDatabase();
	}

}
