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
				->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEmpty()
				->object($tokenizer->tokenize('<?php ?>'))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php ?>')
			->if($tokens = new php\tokenizer\tokens($phpCode = '<?php function foo() {} ?>'))
			->and($tokens->next())
			->and($phpFunction = new tokenizers\phpFunction())
			->and($phpFunction->setFromTokens($tokens))
			->then
				->object($tokenizer->tokenize($phpCode))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo($phpCode)
				->array($tokenizer->getFunctions())->isEqualTo(array($phpFunction->getIterator()))
		;
	}

	public function testCurrentTokenIs()
	{
	}
}

?>
