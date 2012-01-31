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

	public function test__construct()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction())
			->then
				->variable($tokenizer->getName())->isNull()
		;
	}

	public function testGetName()
	{
		$this
		->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo {} ?>'))
			->then
				->string($tokenizer->getName())->isEqualTo('foo')
		;
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
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php public function foo(); ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->goToNextTokenWithName(T_PUBLIC))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens = new tokenizer\tokens('<?php function() {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
			->then
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

	public function testIsAbstract()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isFalse()
			->if($tokenizer->tokenize('<?php abstract function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isTrue()
			->if($tokenizer->tokenize('<?php public abstract function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isTrue()
			->if($tokenizer->tokenize('<?php abstract public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isTrue()
			->if($tokenizer->tokenize('<?php abstract static function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isTrue()
			->if($tokenizer->tokenize('<?php static abstract function foo() {} ?>'))
			->then
				->boolean($tokenizer->isAbstract())->isTrue()
		;
	}

	public function testIsFinal()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isFalse()
			->if($tokenizer->tokenize('<?php final function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isTrue()
			->if($tokenizer->tokenize('<?php public final function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isTrue()
			->if($tokenizer->tokenize('<?php final public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isTrue()
			->if($tokenizer->tokenize('<?php final static function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isTrue()
			->if($tokenizer->tokenize('<?php static final function foo() {} ?>'))
			->then
				->boolean($tokenizer->isFinal())->isTrue()
		;
	}

	public function testIsStatic()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isFalse()
			->if($tokenizer->tokenize('<?php static function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isTrue()
			->if($tokenizer->tokenize('<?php public static function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isTrue()
			->if($tokenizer->tokenize('<?php static public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isTrue()
			->if($tokenizer->tokenize('<?php static final function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isTrue()
			->if($tokenizer->tokenize('<?php final static function foo() {} ?>'))
			->then
				->boolean($tokenizer->isStatic())->isTrue()
		;
	}

	public function testIsPublic()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPublic())->isTrue()
			->if($tokenizer->tokenize('<?php public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPublic())->isTrue()
			->if($tokenizer->tokenize('<?php protected function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPublic())->isFalse()
		;
	}

	public function testIsProtected()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isProtected())->isFalse()
			->if($tokenizer->tokenize('<?php public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isProtected())->isFalse()
			->if($tokenizer->tokenize('<?php private function foo() {} ?>'))
			->then
				->boolean($tokenizer->isProtected())->isFalse()
			->if($tokenizer->tokenize('<?php protected function foo() {} ?>'))
			->then
				->boolean($tokenizer->isProtected())->isTrue()
		;
	}

	public function testIsPrivate()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass\phpFunction('<?php function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPrivate())->isFalse()
			->if($tokenizer->tokenize('<?php public function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPrivate())->isFalse()
			->if($tokenizer->tokenize('<?php protected function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPrivate())->isFalse()
			->if($tokenizer->tokenize('<?php private function foo() {} ?>'))
			->then
				->boolean($tokenizer->isPrivate())->isTrue()
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
			->if($tokens->goToNextTokenWithName(T_PUBLIC))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('public function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php protected function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_PROTECTED))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('protected function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php private function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_PRIVATE))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('private function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php abstract function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_ABSTRACT))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('abstract function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php final function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_FINAL))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('final function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php public static function foo() { echo __FUNCTION__; } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_PUBLIC))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('public static function foo() { echo __FUNCTION__; }')
			->if($tokens = new tokenizer\tokens('<?php class foo { public function bar() { echo __FUNCTION__; } } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_PUBLIC))
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
