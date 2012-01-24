<?php

namespace mageekguy\atoum\tests\units\php\tokenizers;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers
;

class phpClass extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\php\tokenizer');
	}

	public function testCanTokenize()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass())
			->and($tokens = new tokenizer\tokens())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php ' . uniqid() . ' ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php class foo {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->goToNextTokenWithName(T_CLASS))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php abstract class foo {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->goToNextTokenWithName(T_CLASS))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens = new tokenizer\tokens('<?php final class foo {} ?>'))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
			->if($tokens->goToNextTokenWithName(T_CLASS))
			->then
				->boolean($tokenizer->canTokenize($tokens))->isTrue()
			->if($tokens->next())
			->then
				->boolean($tokenizer->canTokenize($tokens))->isFalse()
		;
	}

	public function testSetFromTokens()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass())
			->and($tokens = new tokenizer\tokens())
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens = new tokenizer\tokens('<?php class foo { public function bar() {} } ?>'))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isZero()
			->if($tokens->goToNextTokenWithName(T_CLASS))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('class foo { public function bar() {} }')
			->if($tokens->rewind()->goToNextTokenWithName(T_CLASS))
			->and($tokenizer->addTokenizer(new tokenizers\phpClass\phpFunction()))
			->then
				->object($tokenizer->setFromTokens($tokens))->isIdenticalTo($tokenizer)
				->sizeOf($tokenizer->getIterator())->isGreaterThan(0)
				->castToString($tokenizer->getIterator())->isEqualTo('class foo { public function bar() {} }')
		;
	}

	public function testGetIteratorInstance()
	{
		$this->assert
			->if($tokenizer = new tokenizers\phpClass())
			->then
				->object($tokenizer->getIteratorInstance())->isEqualTo(new tokenizers\phpClass\iterator())
		;
	}
}

?>
