<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpNamespace
;

class phpNamespace extends php\tokenizer
{
	protected $name = null;

	private $stack = null;
	private $hasCurlyBrace = false;

	public function __construct($string = null)
	{
		$this->putInString($this->name)
			->valueOfToken(T_STRING)
			->valueOfToken(T_NS_SEPARATOR)
				->afterToken(T_NAMESPACE)
				->skipToken(T_WHITESPACE);

		parent::__construct($string);
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = $tokens->currentTokenHasName(T_NAMESPACE);

		$this->hasCurlyBrace = $tokens->nextTokenHasValue('{', array(T_STRING, T_WHITESPACE));

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

	public function getIteratorInstance()
	{
		return new phpNamespace\iterator();
	}

	public function getName()
	{
		return ($this->name ?: null);
	}

	protected function appendCurrentToken(tokenizer\tokens $tokens)
	{
		parent::appendCurrentToken($tokens);

		if ($this->hasCurlyBrace === false)
		{
			if ($tokens->nextTokenHasName(T_NAMESPACE) === true || $tokens->nextTokenHasName(T_CLOSE_TAG) === true)
			{
				$this->stop();
			}
		}
		else
		{
			switch ($tokens->current()->getValue())
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
		}

		return $this;
	}

	protected function start(tokenizer\tokens $tokens)
	{
		$this->name = null;

		return parent::start($tokens);
	}
}

?>
