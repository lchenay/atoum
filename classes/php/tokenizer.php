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
	protected $started = false;

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
		return $this->setFromTokens(new tokenizer\tokens($string));
	}

	public function setFromTokens(tokenizer\tokens $tokens)
	{
		$this->iterator = $this->getIteratorInstance();

		$this->started = $this->canTokenize($tokens);

		while ($this->started === true && $tokens->valid() === true)
		{
			if (($tokenizer = $this->getTokenizer($tokens)) === null)
			{
				$this->appendToken($tokens->current());

				$tokens->next();
			}
			else
			{
				$this->iterator->append($iterator = $tokenizer->setFromTokens($tokens)->getIterator());

				$this->iterators[$iterator->getType()][] = $iterator;
			}
		}

		return $this;
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_OPEN_TAG);
	}

	public function getIterator()
	{
		return $this->iterator;
	}

	public function getFunctions()
	{
		return (isset($this->iterators[tokenizers\phpFunction\iterator::type]) === false ? array() : $this->iterators[tokenizers\phpFunction\iterator::type]);
	}

	public function getIteratorInstance()
	{
		return new tokenizer\iterator();
	}

	protected function appendToken(tokenizer\token $token)
	{
		$this->iterator->append($token);

		return $this;
	}

	protected function getTokenizer(tokenizer\tokens $tokens)
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
}

?>
