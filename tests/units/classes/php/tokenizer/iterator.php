<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\tests\units\php\tokenizer\tokens
;

class iterator extends tokens
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer\tokens');
	}

	public function test__construct()
	{
		$this->assert
			->if($iterator = new tokenizer\iterator())
			->then
				->variable($iterator->getParent())->isNull()
		;
	}

	public function testSetParent()
	{
		$this->assert
			->if($iterator = new tokenizer\iterator())
			->then
				->object($iterator->setParent($parent = new tokenizer\iterator()))->isIdenticalTo($iterator)
				->object($iterator->getParent())->isIdenticalTo($parent)
		;
	}
}

?>
