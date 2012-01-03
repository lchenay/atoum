<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizers
;

require_once __DIR__ . '/../../runner.php';

class tokenizer extends atoum\test
{
	public function testTokenize()
	{
		$this->assert
			->if($tokenizer = new php\tokenizer())
			->and($tokenizer->addTokenizer(new tokenizers\phpFunction()))
			->then
				->array($tokenizer->tokenizeString(''))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEmpty()
				->array($tokenizer->tokenizeString('<?php ?>'))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php ?>')
				->array($tokenizer->tokenizeString('<?php function foo() {} ?>'))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php function foo() {} ?>')
//			->if($iterator = $tokenizer->getIterator())
//			->and($iterator->next())
//			->and($functionIterator = $iterator->current())
//			->then
//				->castToString($functionIterator)->isEqualTo('function foo() {}')
		;
	}
}

?>
