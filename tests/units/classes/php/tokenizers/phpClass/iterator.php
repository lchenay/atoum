<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpClass;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass
;

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer\iterator');
	}

	public function test__construct()
	{
		$this->assert
			->if($iterator = new phpClass\iterator())
			->then
				->variable($iterator->getParent())->isNull()
		;
	}

	public function testGetType()
	{
		$this->assert
			->if($iterator = new phpClass\iterator())
			->then
				->string($iterator->getType())->isEqualTo('class')
		;
	}

	public function testGetName()
	{
		$this
		->assert
			->if($iterator = new phpClass\iterator())
			->then
				->variable($iterator->getName())->isNull()
		->assert
			->if($iterator->append(new tokenizer\token('class', T_CLASS)))
			->then
				->variable($iterator->getName())->isNull()
		->assert
			->if($iterator->append(new tokenizer\token('foo', T_STRING)))
			->then
				->string($iterator->getName())->isEqualTo('foo')
		;
	}

	/*
	public function testGetParent()
	{
		$this
		->assert
			->if($iterator = new phpClass\iterator())
			->then
				->variable($iterator->getParent())->isNull()
		->assert
			->if($iterator = new phpClass\iterator('<?php class foo { public function bar() {} } ?>'))
			->then
				->variable($iterator->getParent())->isNull()
		->assert
			->if($iterator = new phpClass\iterator('<?php class foo extends bar { public function bar() {} } ?>'))
			->then
				->string($iterator->getParent())->isEqualTo('bar')
		;
	}

	public function testGetInterfaces()
	{
		$this
		->assert
			->if($iterator = new phpClass\iterator())
			->then
				->array($iterator->getInterfaces())->isEmpty()
		->assert
			->if($iterator = new phpClass\iterator('<?php class foo {} ?>'))
			->then
				->array($iterator->getInterfaces())->isEmpty()
		->assert
			->if($iterator = new phpClass\iterator('<?php class foo implements iBar {} ?>'))
			->then
				->array($iterator->getInterfaces())->isEqualTo(array('iBar'))
		->assert
			->if($iterator = new phpClass\iterator('<?php class foo implements iBar, iFoo {} ?>'))
			->then
				->array($iterator->getInterfaces())->isEqualTo(array('iBar', 'iFoo'))
		;
	}
	*/
}

?>
