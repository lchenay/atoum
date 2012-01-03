<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum\php\tokenizer
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;
	protected $iterators = array();
	protected $tokenizers = array();
	protected $canTokenize = true;

	public function __construct($string = null)
	{
		if ($string !== null)
		{
			$this->tokenize($string);
		}
	}

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

	public function tokenize($string)
	{
		return $this->setFromTokens(token_get_all($string));
	}

	public function setFromTokens(array $tokens)
	{
		$this->iterator = $this->getIteratorInstance();

		$this->canTokenize = $this->canTokenize($tokens);

		while ($this->canTokenize === true && $tokens)
		{
			$tokenizer = $this->getTokenizer($tokens);

			if ($tokenizer !== null)
			{
				$tokens = $tokenizer->setFromTokens($tokens);

				$this->iterator->append($iterator = $tokenizer->getIterator());

				$this->iterators[$iterator->getType()][] = $iterator;
			}
			else
			{
				$this->iterator->append($this->handleToken(current($tokens)));

				array_shift($tokens);
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

	public function getFunctions()
	{
		return (isset($this->iterators[tokenizers\phpFunction\iterator::type]) === false ? array() : $this->iterators[tokenizers\phpFunction\iterator::type]);
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

	protected function getIteratorInstance()
	{
		return new tokenizer\iterator();
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
