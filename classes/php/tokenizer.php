<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum\php\tokenizer
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;
	protected $tokenizers = array();
	protected $canTokenize = true;

	public function __toString()
	{
		return (string) $this->iterator;
	}

	public function addTokenizer(tokenizer $tokenizer)
	{
		$this->tokenizers[] = $tokenizer;

		return $this;
	}

	public function getTokenizers()
	{
		return $this->tokenizers;
	}

	public function tokenizeString($string)
	{
		return $this->tokenizeArray(token_get_all($string));
	}

	public function tokenizeArray(array $tokens)
	{
		$this->iterator = new tokenizer\iterator();

		$this->canTokenize = $this->canTokenize($tokens);

		while ($this->canTokenize === true && $tokens)
		{
			$tokenizer = $this->getTokenizer($tokens);

			if ($tokenizer === null)
			{
				$token = current($tokens);

				$this->iterator[] = $this->handleToken($token);

				array_shift($tokens);
			}
			else
			{
				$tokens = $tokenizer->tokenizeArray($tokens);

				$this->iterator[] = $tokenizer;
			}
		}

		return $tokens;
	}

	public function canTokenize(array $tokens)
	{
		return self::currentTokenIs($tokens, T_OPEN_TAG);
	}

	public function getIterator()
	{
		return $this->iterator;
	}

	protected function getTokenizer(array $tokens)
	{
		foreach ($this->tokenizers as $tokenizer)
		{
			if ($tokenizer->canTokenize($tokens) === true)
			{
				return $tokenizer;
			}
		}

		return null;
	}

	protected function handleToken($token)
	{
		return (is_array($token) === false || isset($token[1]) === false ? $token : $token[1]);
	}

	protected static function currentTokenIs($tokens, $tokenName)
	{
		$token = current($tokens);

		return (isset($token[0]) === true && $token[0] === $tokenName);
	}
}

?>
