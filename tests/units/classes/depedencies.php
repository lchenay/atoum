<?php

namespace mageekguy\atoum\tests\units;

require_once __DIR__ . '/../runner.php';

use
	mageekguy\atoum
;

class depedencies extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->array($depedencies->getInjectors())->isEmpty()
		;
	}

	public function testSerialize()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->variable($depedencies->serialize())->isNull()
		;
	}

	public function testUnserialize()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->object($depedencies->unserialize(uniqid()))->isIdenticalTo($depedencies)
			->if($depedencies[$key = uniqid()] = uniqid())
			->then
				->object($depedencies->unserialize(uniqid()))->isIdenticalTo($depedencies)
				->boolean(isset($depedencies[$key]))->isTrue()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->object($depedencies->offsetSet($key = uniqid(), $injector = function() {}))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
				->object($depedencies->offsetSet($otherKey = uniqid(), $injectorValue = uniqid()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
				->object($depedencies->offsetSet($this, $otherInjectorValue = uniqid()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($depedencies[$this]))->isTrue()
					->object($depedencies[$this])->isInstanceOf('closure')
					->string($depedencies[$this]())->isEqualTo($otherInjectorValue)
				->object($depedencies->offsetSet($otherKey, $otherDepedencies = new atoum\depedencies()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isIdenticalTo($otherDepedencies)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->object($depedencies[$class = uniqid()])->isInstanceOf($depedencies)
				->boolean(isset($depedencies[$class]))->isTrue()
			->if($depedencies['mageekguy\atoum\test'] = $testDepedencies = new atoum\depedencies())
			->then
				->object($depedencies[$this])->isIdenticalTo($testDepedencies)
		;
	}

	public function testLock()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->and($depedencies[$key = uniqid()] = $injector = function() {})
			->then
				->object($depedencies->lock())->isIdenticalTo($depedencies)
			->if($depedencies[$key] = $otherInjector = function() {})
			->then
				->object($depedencies[$key])->isIdenticalTo($injector)
				->object($depedencies[$key])->isNotIdenticalTo($otherInjector)
		;
	}

	public function testUnlock()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->and($depedencies[$key = uniqid()] = $injector = function() {})
			->and($depedencies->lock())
			->then
				->object($depedencies->unlock())->isIdenticalTo($depedencies)
			->if($depedencies[$key] = $otherInjector = function() {})
			->then
				->object($depedencies[$key])->isNotIdenticalTo($injector)
				->object($depedencies[$key])->isIdenticalTo($otherInjector)
		;
	}
}

?>
