<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer
;

class phpClass extends php\tokenizer
{
	private $stack = null;

	public function canTokenize(tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_CLASS);
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

	protected function start(tokenizer\tokens $tokens)
	{
		$goToPreviousToken = true;

		while ($tokens->valid() === true && $goToPreviousToken === true)
		{
			$tokens->prev();

			switch (true)
			{
				case $tokens->currentTokenHasName(T_WHITESPACE):
				case $tokens->currentTokenHasName(T_FINAL):
				case $tokens->currentTokenHasName(T_ABSTRACT):
					$tokens->prev();
					break;

				default:
					$goToPreviousToken = false;
			}
		}

		$tokens->next();

		return parent::start($tokens);
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
			$this->stack = null;
			$this->stop();
		}

		return $this;
	}

	public function getIteratorInstance()
	{
		return new phpClass\iterator();
	}
}

?>
