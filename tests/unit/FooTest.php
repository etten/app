<?php

namespace Hranicka\ComposerSandbox;

class FooTest extends \PHPUnit_Framework_TestCase
{

	public function testGetBar()
	{
		$foo = new Foo();
		$this->assertSame('bar', $foo->getBar());
	}

}
