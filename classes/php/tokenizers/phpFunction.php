<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php
;

class phpFunction extends php\tokenizer
{
	protected $stack = null;

	public function canTokenize(array $tokens)
	{
		$token = current($tokens);

		return (isset($token[0]) === true && $token[0] === T_FUNCTION);
	}

	public function handleToken($token)
	{
		$token = parent::handleToken($token);

		switch ($token)
		{
			case '{':
				++$this->stack;
				break;

			case '}':
				--$this->stack;
				break;
		}

		$this->canTokenize = ($this->stack !== 0);

		return $token;
	}
}

?>
