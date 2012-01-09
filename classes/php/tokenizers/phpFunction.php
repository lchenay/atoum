<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpFunction
;

class phpFunction extends php\tokenizer
{
	protected $stack = null;

	public function canTokenize(php\tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_FUNCTION) && $tokens->nextTokenHasName(T_STRING);
	}

	protected function appendToken(tokenizer\token $token)
	{
		parent::appendToken($token);

		switch ($token->getValue())
		{
			case '{':
				++$this->stack;
				break;

			case '}':
				--$this->stack;
				break;
		}

		$this->canTokenize = ($this->stack !== 0);

		return $this;
	}

	protected static function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}

}

?>
