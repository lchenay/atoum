<?php

namespace mageekguy\atoum\tests\units\php\tokenizers\phpNamespace;

require_once __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers
;

class phpUse extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer');
	}

	public function testGetNamespaces()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace\phpUse())
			->then
				->array($tokenizer->getNamespaces())->isEmpty()
			->if($tokenizer->tokenize('<?php use foo; ?>'))
			->then
				->array($tokenizer->getNamespaces())->isEqualTo(array('foo' => null))
			->if($tokenizer->tokenize('<?php use foo\bar; ?>'))
			->then
				->array($tokenizer->getNamespaces())->isEqualTo(array('foo\bar' => null))
			->if($tokenizer->tokenize('<?php use foo\bar as foo; ?>'))
			->then
				->array($tokenizer->getNamespaces())->isEqualTo(array('foo\bar' => 'foo'))
			->if($tokenizer->tokenize('<?php use foo\bar as foo, toto; ?>'))
			->then
				->array($tokenizer->getNamespaces())->isEqualTo(array('foo\bar' => 'foo', 'toto' => null))
		;
	}

	public function testCanTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpNamespace\phpUse())
			->and($tokens = new tokenizer\tokens())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php use foo; ?>'))
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
			->if($tokenizer = new tokenizers\phpNamespace\phpUse())
			->then
				->object($tokenizer->tokenize(''))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize(uniqid()))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize('<?php ' . uniqid() . ' ?>'))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
				->object($tokenizer->tokenize('<?php use foo; ?>'))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('use foo;')
		;
	}
}

?>
