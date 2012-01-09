<?php

namespace mageekguy\atoum\tests\units\php\tokenizers;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers
;

class phpFunction extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer');
	}

	public function testCanTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpFunction())
			->and($tokens = new tokenizer\tokens())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php function() {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
		;
	}
}

?>
