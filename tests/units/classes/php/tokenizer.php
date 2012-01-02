<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

require_once __DIR__ . '/../../runner.php';

class tokenizer extends atoum\test
{
	public function testTokenize()
	{
		$this->assert
			->if($tokenizer = new php\tokenizer())
			->then
				->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEmpty()
				->object($tokenizer->tokenize('<?php ?>'))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php ?>')
				->object($tokenizer->tokenize('<?php function foo() {} ?>'))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo('<?php function foo() {} ?>')
		;
	}
}

?>
