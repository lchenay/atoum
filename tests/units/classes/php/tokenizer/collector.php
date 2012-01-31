<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer
;

class collector extends atoum\test
{
	public function test__construct()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->boolean($collector->canCollect())->isFalse()
		;
	}

	public function testValueOf()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->valueOfToken(T_STRING))->isIdenticalTo($collector)
				->variable($collector->getValueOfTokens())->isIdenticalTo(array(T_STRING))
				->object($collector->valueOfToken(T_NS_SEPARATOR))->isIdenticalTo($collector)
				->variable($collector->getValueOfTokens())->isIdenticalTo(array(T_STRING, T_NS_SEPARATOR))
		;
	}

	public function testAfterToken()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->afterToken(T_STRING))->isIdenticalTo($collector)
				->array($collector->getAfterTokens())->isEqualTo(array(T_STRING))
		;
	}

	public function testBeforeToken()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->beforeToken(T_STRING))->isIdenticalTo($collector)
				->array($collector->getBeforeTokens())->isEqualTo(array(T_STRING))
		;
	}

	public function testBeforeValue()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->beforeValue('{'))->isIdenticalTo($collector)
				->array($collector->getBeforeValues())->isEqualTo(array('{'))
		;
	}

	public function testFrom()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->from($tokens = new tokenizer\tokens()))->isIdenticalTo($collector)
				->object($collector->getFrom())->isIdenticalTo($tokens)
		;
	}

	public function testInString()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->inString($string))->isIdenticalTo($collector)
				->variable->setByReferenceWith($collector->getInString())->isReferenceTo($string)
		;
	}

	public function testInArray()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->inString($array))->isIdenticalTo($collector)
				->array->setByReferenceWith($collector->getInArray())->isReferenceTo($array)
		;
	}

	public function testSkipName()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->skipToken(T_STRING))->isIdenticalTo($collector)
				->array($collector->getSkippedNames())->isEqualTo(array(T_STRING))
				->object($collector->skipToken(T_STRING))->isIdenticalTo($collector)
				->array($collector->getSkippedNames())->isEqualTo(array(T_STRING))
				->object($collector->skipToken(T_WHITESPACE))->isIdenticalTo($collector)
				->array($collector->getSkippedNames())->isEqualTo(array(T_STRING, T_WHITESPACE))
		;
	}

	public function testSkipValue()
	{
		$this
		->assert
			->if($collector = new tokenizer\collector())
			->then
				->object($collector->skipValue(T_STRING))->isIdenticalTo($collector)
				->array($collector->getSkippedValues())->isEqualTo(array(T_STRING))
				->object($collector->skipValue(T_STRING))->isIdenticalTo($collector)
				->array($collector->getSkippedValues())->isEqualTo(array(T_STRING))
				->object($collector->skipValue(T_WHITESPACE))->isIdenticalTo($collector)
				->array($collector->getSkippedValues())->isEqualTo(array(T_STRING, T_WHITESPACE))
		;
	}

	public function testExecute()
	{
		$tokens = new tokenizer\tokens('<?php function foo($bar) { echo $bar; } ?>');

		$this
		->assert("It's possible to execute an empty collector")
			->if($collector = new tokenizer\collector())
			->then
				->boolean($collector->canCollect())->isFalse()
				->object($collector->execute())->isIdenticalTo($collector)
		->assert("It's possible to execute a collector on an empty tokens iterator")
			->if($collector->from(new tokenizer\tokens()))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
		->assert("It's possible to execute a collector on a not empty tokens iterator and no string or array defined")
			->if($collector->from($tokens = new tokenizer\tokens('<?php function foo($bar) { echo $bar; } ?>')))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
		->assert("It's possible to collect each token in tokens iterator")
			->if($collector->inString($string))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('<?php ')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('<?php function')
		->assert("It's possible to collect a token after a specific token")
			->if($collector->afterToken(T_OPEN_TAG))
			->and($string = null)
			->and($tokens->rewind())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->variable($string)->isNull()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('function')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('function ')
		->assert("It's possible to collect a token of a specific name after a specific token")
			->if($string = null)
			->and($tokens->rewind())
			->and($collector->from($tokens))
			->and($collector->valueOfToken(T_STRING))
			->and($collector->afterToken(T_FUNCTION))
			->and($collector->skipToken(T_WHITESPACE))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->variable($string)->isNull()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->variable($string)->isNull()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->variable($string)->isNull()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
		->assert("It's possible to collect an array of tokens of a specific name after a specific token")
			->if($collector = new tokenizer\collector())
			->and($collector->from($tokens = new tokenizer\tokens('<?php class foo implements iFoo, iBar { toto } ?>')))
			->and($collector->inArray($array))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('<?php '))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('<?php ', 'class'))
			->if($tokens->rewind())
			->and($collector->afterToken(T_IMPLEMENTS))
			->and($collector->valueOfToken(T_STRING))
			->and($collector->inArray($array))
			->and($collector->skipValue(','))
			->and($collector->skipToken(T_WHITESPACE))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo', 'iBar'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo', 'iBar'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo', 'iBar'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo', 'iBar'))
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->array($array)->isEqualTo(array('iFoo', 'iBar', 'toto'))
		->assert("It's possible to collect several specific tokens")
			->if($collector = new tokenizer\collector())
			->and($collector->from($tokens = new tokenizer\tokens('<?php namespace foo\bar; ?>')))
			->and($collector->inString($string))
			->and($collector->afterToken(T_NAMESPACE))
			->and($collector->valueOfToken(T_STRING))
			->and($collector->valueOfToken(T_NS_SEPARATOR))
			->and($collector->skipToken(T_WHITESPACE))
			->and($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo\\')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo\\bar')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo\\bar')
		->assert("It's possible to collect a specific token before a token")
			->if($collector = new tokenizer\collector())
			->and($collector->valueOfToken(T_STRING))
			->and($collector->from($tokens = new tokenizer\tokens('<?php namespace foo; function bar() {} ?>')))
			->and($collector->inString($string))
			->and($collector->beforeToken(T_FUNCTION))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
		->assert("It's possible to collect a specific token before a value")
			->if($collector = new tokenizer\collector())
			->and($collector->valueOfToken(T_STRING))
			->and($collector->from($tokens = new tokenizer\tokens('<?php namespace foo; function bar() {} ?>')))
			->and($collector->inString($string))
			->and($collector->beforeValue(';'))
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEmpty()
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
			->if($tokens->next())
			->then
				->object($collector->execute())->isIdenticalTo($collector)
				->string($string)->isEqualTo('foo')
		;
	}
}
