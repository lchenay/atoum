<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class extension extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('\recursiveFilterIterator');
	}

	public function test__construct()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php')))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
				->object($depedencies = $filter->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($depedencies['directory\iterator']))->isTrue()
			->if($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php'), $depedencies = new atoum\depedencies()))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\extension'])
				->boolean(isset($filterDepedencies['directory\iterator']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\filters\recursives\extension']['directory\iterator'] = $directoryIteratorInjector = function($path) use (& $directoryIterator) { return $directoryIterator = new \mock\recursiveDirectoryIterator($path); })
			->and($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $acceptedExtensions = array('php'), $depedencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\extension'])
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
			->if($filter = new recursives\extension($path = uniqid(), $acceptedExtensions = array('php'), $depedencies))
			->then
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\extension'])
				->array($filter->getAcceptedExtensions())->isEqualTo($acceptedExtensions)
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
				->object($filter->getInnerIterator())->isEqualTo($directoryIterator)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($filter = new recursives\extension($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), array('php')))
			->then
				->object($filter->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($filter)
				->object($filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\extension'])
				->boolean(isset($depedencies['mageekguy\atoum\iterators\filters\recursives\extension']['directory\iterator']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\filters\recursives\extension']['directory\iterator'] = $directoryIteratorInjector = function($path) use (& $directoryIterator) { return $directoryIterator = new \mock\recursiveDirectoryIterator($path); })
			->then
				->object($filter->setDepedencies($depedencies))->isIdenticalTo($filter)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\extension'])
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
		;
	}

	public function testAccept()
	{
		$this
			->if($filter = new recursives\extension($innerIterator = new \mock\recursiveIterator(), array('php')))
			->and($innerIterator->getMockController()->current = uniqid() . '.php')
			->then
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid() . DIRECTORY_SEPARATOR . uniqid() . '.php')
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid())
				->boolean($filter->accept())->isTrue()
			->if($innerIterator->getMockController()->current = uniqid() . '.' . uniqid())
				->boolean($filter->accept())->isFalse()
		;
	}
}

?>
