<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpClass;

require __DIR__ . '/../../../../runner.php';

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
			->if($tokenizer = new tokenizers\phpClass\phpFunction())
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
			->if($tokens = new tokenizer\tokens('<?php public function foo(); ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
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

	public function testTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction())
			->then
				->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize(uniqid()))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize('<?php ' . uniqid() . ' ?>'))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize('<?php function foo() { if (true) { echo __FUNCTION__; } } ?>'))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('function foo() { if (true) { echo __FUNCTION__; } }')
		;
	}

	public function testSetFromTokens()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction())
			->and($tokens = new tokenizer\tokens())
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens = new tokenizer\tokens('<?php function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->next())
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php public function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('public function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php protected function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('protected function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php private function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('private function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php abstract function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('abstract function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php final function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('final function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php public static function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('public static function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php class foo { public function bar() { echo __FUNCTION__; } } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FUNCTION))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('public function bar() { echo __FUNCTION__; }')
		;
	}

	public function testGetIteratorInstance()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction())
			->then
				->object($tokenizer->getIteratorInstance())->isEqualTo(new tokenizers\phpClass\phpFunction\iterator())
		;
	}
}

?>
