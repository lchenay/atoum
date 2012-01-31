<?php

namespace mageekguy\atoum\tests\units\php\tokenizers;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers
;

class phpNamespace extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer');
	}

	public function testCanTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace())
			->and($tokens = new tokenizer\tokens())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php namespace foo; ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
		;
	}

	public function testTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace())
			->then
				->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
				->castToString($tokenizer->getIterator())->isEmpty()
				->object($tokenizer->tokenize('namespace foo;'))->isIdenticalTo($tokenizer)
				->castToString($tokenizer->getIterator())->isEmpty()
				->object($tokenizer->tokenize('<?php namespace foo; ?>'))->isIdenticalTo($tokenizer)
				->castToString($tokenizer->getIterator())->isEqualTo('namespace foo; ')
		;
	}

	public function testSetFromTokens()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace())
			->and($tokens = new tokenizer\tokens())
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens = new tokenizer\tokens('<?php namespace foo; class bar {} ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_NAMESPACE))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('namespace foo; class bar {} ')
			->if($tokens = new tokenizer\tokens('<?php namespace foo { class bar {} } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_NAMESPACE))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('namespace foo { class bar {} }')
			->if($tokens = new tokenizer\tokens('<?php namespace foo; class bar {} namespace bar; class foo {} ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_NAMESPACE))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('namespace foo; class bar {} ')
		;
	}

	public function testGetIteratorInstance()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace())
			->then
				->object($tokenizer->getIteratorInstance())->isEqualTo(new tokenizers\phpNamespace\iterator())
		;
	}

	public function testGetName()
	{
		$this
		->assert
			->if($tokenizer = new tokenizers\phpNamespace())
			->then
				->variable($tokenizer->getName())->isNull()
		->assert
			->if($tokenizer = new tokenizers\phpNamespace('<?php namespace foo; ?>'))
			->then
				->string($tokenizer->getName())->isEqualTo('foo')
		->assert
			->if($tokenizer = new tokenizers\phpNamespace('<?php namespace foo\bar; ?>'))
			->then
				->string($tokenizer->getName())->isEqualTo('foo\bar')
		;
	}

}

?>
