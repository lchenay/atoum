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
				->array($tokenizer->tokenize(''))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEmpty()
				->array($tokenizer->tokenize('<?php ?>'))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php ?>')
			->if($tokens = token_get_all($phpCode = '<?php function foo() {} ?>'))
			->and($phpFunction = new tokenizers\phpFunction())
			->and($phpFunction->setFromTokens(array_slice($tokens, 1, -2)))
			->then
				->array($tokenizer->tokenize($phpCode))->isEmpty()
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo($phpCode)
				->array($tokenizer->getFunctions())->isEqualTo(array($phpFunction->getIterator()))
		;
	}
}

?>
