<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer
;

require_once __DIR__ . '/../../../runner.php';

class token extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($token = new tokenizer\token($value = uniqid()))
			->then
				->string($token->getValue())->isEqualTo($value)
				->variable($token->getName())->isNull()
			->if($token = new tokenizer\token($value = uniqid(), $name = uniqid()))
			->then
				->string($token->getValue())->isEqualTo($value)
				->string($token->getName())->isEqualTo($name)
		;
	}

	public function test__toString()
	{
		$this->assert
			->if($token = new tokenizer\token($value = uniqid()))
			->then
				->castToString($token)->isEqualTo($value)
			->if($token = new tokenizer\token($value = uniqid(), $name = uniqid()))
			->then
				->castToString($token)->isEqualTo($value)
		;
	}
}

?>
