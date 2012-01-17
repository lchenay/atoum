<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass
;

class phpClass extends php\tokenizer
{
	private $stack = null;

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = $tokens->currentTokenHasName(T_CLASS);

		if (
				$canTokenize === false
				&&
				(
					$tokens->currentTokenHasName(T_FINAL)
					||
					$tokens->currentTokenHasName(T_ABSTRACT)
				)
				&&
				$tokens->valid() === true
			)
		{
			$key = $tokens->key();

			$goToNextToken = true;

			while ($tokens->valid() === true && $goToNextToken === true)
			{
				$tokens->next();

				switch (true)
				{
					case $tokens->currentTokenHasName(T_WHITESPACE):
					case $tokens->currentTokenHasName(T_FINAL):
					case $tokens->currentTokenHasName(T_ABSTRACT):
						break;

					case  $tokens->currentTokenHasName(T_CLASS):
						$canTokenize = true;
						$goToNextToken = false;
						break;

					default:
						$goToNextToken = false;
				}
			}

			$tokens->seek($key);
		}

		return $canTokenize;
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

	public function getFunctions()
	{
		return $this->getIterators(phpClass\phpFunction\iterator::type);
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
