<?php

namespace mageekguy\atoum\tests\units\iterators\recursives;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\iterators\filters,
	mageekguy\atoum\iterators\recursives
;

class directory extends atoum\test
{
	public function beforeTestMethod($method)
	{
		$this->mockGenerator->shunt('__construct')->generate('recursiveDirectoryIterator');
	}

	public function test_class()
	{
		$this->testedClass->hasInterface('iteratorAggregate');
	}

	public function test__construct()
	{
		$this
			->if($iterator = new recursives\directory())
				->variable($iterator->getPath())->isNull()
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iteratorDepedencies = $iterator->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($iteratorDepedencies['directory\iterator']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\dot']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\extension']))->isTrue()
			->if($iterator = new recursives\directory($path = uniqid(), $depedencies = new atoum\depedencies()))
			->then
				->string($iterator->getPath())->isEqualTo($path)
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iteratorDepedencies = $iterator->getDepedencies())->isIdenticalTo($depedencies[$iterator])
				->boolean(isset($iteratorDepedencies['directory\iterator']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\dot']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\extension']))->isTrue()
			->mockGenerator->shunt('__construct')
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['directory\iterator'] = $directoryIteratorInjector =  function() {})
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\dot'] = $dotFilterInjector = function() {})
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\extension'] = $extensionFilterInjector = function() {})
			->if($iterator = new recursives\directory($path = uniqid(), $depedencies))
			->then
				->string($iterator->getPath())->isEqualTo($path)
				->boolean($iterator->dotsAreAccepted())->isFalse()
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php'))
				->object($iteratorDepedencies = $iterator->getDepedencies())->isIdenticalTo($depedencies[$iterator])
				->object($iteratorDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
				->object($iteratorDepedencies['filters\dot'])->isIdenticalTo($dotFilterInjector)
				->object($iteratorDepedencies['filters\extension'])->isIdenticalTo($extensionFilterInjector)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($iterator)
				->object($iteratorDepedencies = $iterator->getDepedencies())->isIdenticalTo($depedencies[$iterator])
				->boolean(isset($iteratorDepedencies['directory\iterator']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\dot']))->isTrue()
				->boolean(isset($iteratorDepedencies['filters\extension']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['directory\iterator'] = $directoryIteratorInjector =  function() {})
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\dot'] = $dotFilterInjector = function() {})
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\extension'] = $extensionFilterInjector = function() {})
			->then
				->object($iterator->setDepedencies($depedencies))->isIdenticalTo($iterator)
				->object($iteratorDepedencies = $iterator->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\iterators\recursives\directory'])
				->object($iteratorDepedencies['directory\iterator'])->isIdenticalTo($directoryIteratorInjector)
				->object($iteratorDepedencies['filters\dot'])->isIdenticalTo($dotFilterInjector)
				->object($iteratorDepedencies['filters\extension'])->isIdenticalTo($extensionFilterInjector)
		;
	}

	public function testSetPath()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->setPath($path = uniqid()))->isIdenticalTo($iterator)
				->string($iterator->getPath())->isEqualTo($path)
		;
	}

	public function testAcceptExtensions()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptExtensions($extensions = array(uniqid())))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo($extensions)
				->object($iterator->acceptExtensions($extensions = array('.' . ($extension = uniqid()))))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo(array($extension))
		;
	}

	public function testAcceptAllExtensions()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptAllExtensions())->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEmpty()
		;
	}

	public function testRefuseExtension()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->refuseExtension('php'))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEmpty()
			->if($iterator->acceptExtensions(array('php', 'txt', 'xml')))
			->then
				->object($iterator->refuseExtension('txt'))->isIdenticalTo($iterator)
				->array($iterator->getAcceptedExtensions())->isEqualTo(array('php', 'xml'))
		;
	}

	public function testAcceptDots()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->acceptDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isTrue()
		;
	}

	public function testRefuseDots()
	{
		$this
			->if($iterator = new recursives\directory(uniqid()))
			->then
				->object($iterator->refuseDots())->isIdenticalTo($iterator)
				->boolean($iterator->dotsAreAccepted())->isFalse()
		;
	}

	public function testGetIterator()
	{
		$this
			->if($iterator = new \mock\mageekguy\atoum\iterators\recursives\directory())
			->then
				->exception(function() use ($iterator) {
						$iterator->getIterator();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Path is undefined')
			->if($depedencies = new \mock\mageekguy\atoum\depedencies())
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['directory\iterator'] = function($path) use (& $recursiveDirectoryIterator) { return $recursiveDirectoryIterator = new \mock\recursiveDirectoryIterator($path); })
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\dot'] = function($iterator, $depedencies) use (& $dotFilter) { return $dotFilter = new atoum\iterators\filters\recursives\dot($iterator, $depedencies); })
			->and($depedencies['mageekguy\atoum\iterators\recursives\directory']['filters\extension'] = function($iterator, $extensions) use (& $extensionFilter) { return $extensionFilter = new atoum\iterators\filters\recursives\extension($iterator, $extensions); })
			->and($iterator = new recursives\directory($path = uniqid(), $depedencies))
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($extensionFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($dotFilter)
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($extensionFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator())->isIdenticalTo($dotFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($iterator->getIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator = new recursives\directory(null, $depedencies))
			->then
				->object($filterIterator = $iterator->getIterator(uniqid()))->isIdenticalTo($extensionFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($dotFilter)
				->object($filterIterator->getInnerIterator()->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->acceptDots())
			->then
				->object($filterIterator = $iterator->getIterator(uniqid()))->isIdenticalTo($extensionFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->refuseDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($filterIterator = $iterator->getIterator(uniqid()))->isIdenticalTo($dotFilter)
				->object($filterIterator->getInnerIterator())->isIdenticalTo($recursiveDirectoryIterator)
			->if($iterator->acceptDots())
			->and($iterator->acceptExtensions(array()))
			->then
				->object($iterator->getIterator())->isIdenticalTo($recursiveDirectoryIterator)
		;
	}
}

?>
