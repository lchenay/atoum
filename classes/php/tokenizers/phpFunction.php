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

	public function tokenize($string)
	{
		$tokens = new tokenizer\tokens($string);

		if ($tokens->valid() === true && $tokens->current()->getName() === T_OPEN_TAG)
		{
			$tokens->next();
		}

		return $this->setFromTokens($tokens);
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

		if ($this->stack === 0)
		{
			$this->started = false;
			$this->stack = null;
		}

		return $this;
	}

	public function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}
}

?>
