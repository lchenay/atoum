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
	public function test__construct()
	{
		$this->assert
			->if($tokenizer = new php\tokenizer())
			->then
				->variable($tokenizer->getIterator())->isNull()
				->array($tokenizer->getTokenizers())->isEmpty()
				->array($tokenizer->getIterators())->isEmpty()
			->if($tokenizer = new php\tokenizer('<?php ?>'))
			->then
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->array($tokenizer->getTokenizers())->isEmpty()
				->array($tokenizer->getIterators())->isEmpty()
		;
	}

	public function test__toString()
	{
		$this->assert
			->if($tokenizer = new php\tokenizer())
			->then
				->castToString($tokenizer)->isEmpty()
			->if($tokenizer = new php\tokenizer('<?php ?>'))
			->then
				->castToString($tokenizer)->isEqualTo('<?php ?>')
		;
	}

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
				->array($tokenizer->getIterators())->isEqualTo(array(tokenizers\phpFunction\iterator::type => array($phpFunction->getIterator())))
				->array($tokenizer->getIterators(tokenizers\phpFunction\iterator::type))->isEqualTo(array($phpFunction->getIterator()))
				->array($tokenizer->getFunctions())->isEqualTo(array($phpFunction->getIterator()))
			->if($tokens = new php\tokenizer\tokens($phpCode = '<?php class foo {} ?>'))
			->and($tokenizer->addTokenizer(new tokenizers\phpClass()))
			->and($tokens->next())
			->and($phpClass = new tokenizers\phpClass())
			->and($phpClass->setFromTokens($tokens))
			->then
				->object($tokenizer->tokenize($phpCode))->isIdenticalTo($tokenizer)
				->object($tokenizer->getIterator())->isInstanceOf('mageekguy\atoum\php\tokenizer\iterator')
				->castToString($tokenizer->getIterator())->isEqualTo($phpCode)
				->array($tokenizer->getIterators())->isEqualTo(array(tokenizers\phpClass\iterator::type => array($phpClass->getIterator())))
				->array($tokenizer->getIterators(tokenizers\phpClass\iterator::type))->isEqualTo(array($phpClass->getIterator()))
				->array($tokenizer->getClasses())->isEqualTo(array($phpClass->getIterator()))
		;
	}
}

?>
