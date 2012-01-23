<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpNamespace;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizers\phpNamespace
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
			->if($iterator = new phpNamespace\iterator())
			->then
				->variable($iterator->getParent())->isNull()
		;
	}

	public function testGetType()
	{
		$this->assert
			->if($iterator = new phpNamespace\iterator())
			->then
				->string($iterator->getType())->isEqualTo('namespace')
		;
	}
}

?>
