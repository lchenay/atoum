<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer
;

class tokens extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('arrayIterator');
	}

	public function test__construct()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->sizeOf($tokens)->isZero()
			->if($tokens = new tokenizer\tokens($string = uniqid()))
			->then
				->sizeOf($tokens)->isEqualTo(1)
				->object($tokens->current())->isInstanceOf('mageekguy\atoum\php\tokenizer\token')
				->castToString($tokens->current())->isEqualTo($string)
				->array($tokens->getSkippedTokenNames())->isEmpty()
			->if($tokens = new tokenizer\tokens($string = '<?php ?>'))
			->then
				->sizeOf($tokens)->isEqualTo(2)
				->object($tokens->current())->isInstanceOf('mageekguy\atoum\php\tokenizer\token')
				->castToString($tokens[0])->isEqualTo('<?php ')
				->castToString($tokens[1])->isEqualTo('?>')
				->array($tokens->getSkippedTokenNames())->isEmpty()
		;
	}

	public function __toString()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->castToString($tokens)->isEmpty()
			->if($tokens = new tokenizer\tokens($string = uniqid()))
			->then
				->castToString($tokens)->isEqualTo($string)
			->if($tokens = new tokenizer\tokens($string = '<?php ?>'))
			->then
				->castToString($tokens)->isEqualTo($string)
		;
	}

	public function testSkipTokenNames()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->object($tokens->skipTokenNames($skippedTokens = array(uniqid(), uniqid())))->isIdenticalTo($tokens)
				->array($tokens->getSkippedTokenNames())->isEqualTo($skippedTokens)
		;
	}

	public function testResetSkippedTokenNames()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->object($tokens->resetSkippedTokenNames())->isIdenticalTo($tokens)
				->array($tokens->getSkippedTokenNames())->isEmpty()
			->if($tokens->skipTokenNames(array(uniqid(), uniqid())))
			->then
				->object($tokens->resetSkippedTokenNames())->isIdenticalTo($tokens)
				->array($tokens->getSkippedTokenNames())->isEmpty()
		;
	}

	public function testCurrentTokenHasName()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->boolean($tokens->currentTokenHasName(T_OPEN_TAG))->isFalse()
				->boolean($tokens->currentTokenHasName(T_CLOSE_TAG))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ?>'))
			->then
				->boolean($tokens->currentTokenHasName(T_OPEN_TAG))->isTrue()
				->boolean($tokens->currentTokenHasName(T_CLOSE_TAG))->isFalse()
			->if($tokens->next())
			->then
				->boolean($tokens->currentTokenHasName(T_OPEN_TAG))->isFalse()
				->boolean($tokens->currentTokenHasName(T_CLOSE_TAG))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokens->currentTokenHasName(T_OPEN_TAG))->isFalse()
				->boolean($tokens->currentTokenHasName(T_CLOSE_TAG))->isFalse()
		;
	}

	public function testNext()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->object($tokens->next())->isIdenticalTo($tokens)
			->if($tokens = new tokenizer\tokens('<?php function foo() { } ?>'))
			->then
				->object($tokens->next())->isIdenticalTo($tokens)
				->integer($tokens->key())->isEqualTo(1)
				->castToString($tokens->current())->isEqualTo('function')
				->object($tokens->next())->isIdenticalTo($tokens)
				->integer($tokens->key())->isEqualTo(2)
				->castToString($tokens->current())->isEqualTo(' ')
			->if($tokens->rewind())
			->and($tokens->skipTokenNames(array(T_WHITESPACE)))
			->then
				->object($tokens->next())->isIdenticalTo($tokens)
				->integer($tokens->key())->isEqualTo(1)
				->castToString($tokens->current())->isEqualTo('function')
				->object($tokens->next())->isIdenticalTo($tokens)
				->integer($tokens->key())->isEqualTo(3)
				->castToString($tokens->current())->isEqualTo('foo')
		;
	}

	public function testRewind()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->object($tokens->rewind())->isIdenticalTo($tokens)
				->variable($tokens->key())->isNull()
			->if($tokens = new tokenizer\tokens('<?php function foo() {} ?>'))
			->and($tokens->next()->next()->next()->next())
			->then
				->object($tokens->rewind())->isIdenticalTo($tokens)
				->integer($tokens->key())->isEqualTo(0)
		;
	}

	public function testNextTokenHasName()
	{
		$this->assert
			->if($tokens = new tokenizer\tokens(''))
			->then
				->boolean($tokens->nextTokenHasName(T_OPEN_TAG))->isFalse()
				->boolean($tokens->nextTokenHasName(T_CLOSE_TAG))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ?>'))
			->then
				->boolean($tokens->nextTokenHasName(T_OPEN_TAG))->isFalse()
				->integer($tokens->key())->isEqualTo(0)
				->boolean($tokens->nextTokenHasName(T_CLOSE_TAG))->isTrue()
				->integer($tokens->key())->isEqualTo(0)
			->if($tokens = new tokenizer\tokens('<?php function foo() {} ?>'))
			->and($tokens->next())
			->then
				->boolean($tokens->nextTokenHasName(T_OPEN_TAG))->isFalse()
				->integer($tokens->key())->isEqualTo(1)
				->boolean($tokens->nextTokenHasName(T_WHITESPACE))->isTrue()
				->integer($tokens->key())->isEqualTo(1)
		;
	}
}

?>
