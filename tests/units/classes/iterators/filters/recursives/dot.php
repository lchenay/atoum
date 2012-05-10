<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class dot extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('\recursiveFilterIterator');
	}

	public function test__construct()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid())))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->object($filterDepedencies = $filter->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($filterDepedencies['directory\iterator']))->isTrue()
			->if($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $depedencies = new atoum\depedencies()))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\dot'])
				->boolean(isset($filterDepedencies['directory\iterator']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\filters\recursives\dot']['directory\iterator'] = $directoryIteratorInjector = function($path) use (& $directoryIterator) { return $directoryIterator = new \mock\recursiveDirectoryIterator($path); })
			->and($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid()), $depedencies))
			->then
				->object($filter->getInnerIterator())->isIdenticalTo($recursiveIterator)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\dot'])
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
			->and($filter = new recursives\dot($path = uniqid(), $depedencies))
			->then
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\dot'])
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
				->object($filter->getInnerIterator())->isEqualTo($directoryIterator)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($filter = new recursives\dot($recursiveIterator = new \mock\recursiveDirectoryIterator(uniqid())))
			->then
				->object($filter->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($filter)
				->object($filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\dot'])
				->boolean(isset($depedencies['mageekguy\atoum\iterators\filters\recursives\dot']['directory\iterator']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\filters\recursives\dot']['directory\iterator'] = $directoryIteratorInjector = function($path) use (& $directoryIterator) { return $directoryIterator = new \mock\recursiveDirectoryIterator($path); })
			->then
				->object($filter->setDepedencies($depedencies))->isIdenticalTo($filter)
				->object($filterDepedencies = $filter->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\filters\recursives\dot'])
				->object($filterDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
		;
	}

	public function test__accept()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($iteratorController = new mock\controller())
			->and($iteratorController->__construct = function() {})
			->and($filter = new recursives\dot(new \mock\recursiveDirectoryIterator(uniqid())))
			->and($iteratorController->current = new \splFileInfo(uniqid()))
			->then
				->boolean($filter->accept())->isTrue()
			->if($iteratorController->current = new \splFileInfo('.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
			->if($iteratorController->current = new \splFileInfo(uniqid() . DIRECTORY_SEPARATOR . '.' . uniqid()))
			->then
				->boolean($filter->accept())->isFalse()
		;
	}
}

?>
