<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpClass;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
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
}

?>
