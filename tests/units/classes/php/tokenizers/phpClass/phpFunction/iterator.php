<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpClass\phpFunction;

require __DIR__ . '/../../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass\phpFunction
;

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizers\phpFunction\iterator');
	}

	public function testClassConstants()
	{
		$this->assert->string(phpFunction\iterator::type)->isEqualTo('method');
	}

	public function test__construct()
	{
		$this->assert
			->if($iterator = new phpFunction\iterator())
			->then
				->variable($iterator->getParent())->isNull()
		;
	}

	public function testGetType()
	{
		$this->assert
			->if($iterator = new phpFunction\iterator())
			->then
				->string($iterator->getType())->isEqualTo('method')
		;
	}

	public function testGetName()
	{
		$this
		->assert
			->if($iterator = new phpFunction\iterator())
			->then
				->variable($iterator->getName())->isNull()
		->assert
			->if($iterator->append(new tokenizer\token('function', T_FUNCTION)))
			->then
				->variable($iterator->getName())->isNull()
		->assert
			->if($iterator->append(new tokenizer\token(' ', T_WHITESPACE)))
			->then
				->variable($iterator->getName())->isNull()
		->assert
			->if($iterator->append(new tokenizer\token('foo', T_STRING)))
			->then
				->string($iterator->getName())->isEqualTo('foo')
		->assert
			->if($iterator->append(new tokenizer\token('(')))
			->then
				->string($iterator->getName())->isEqualTo('foo')
		;
	}
}

?>
