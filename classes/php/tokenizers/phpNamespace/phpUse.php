<?php

namespace mageekguy\atoum\php\tokenizers\phpNamespace;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer
;

class phpUse extends php\tokenizer
{
	protected $namespaces = array();

	private $as = false;
	private $namespace = '';
	private $alias = '';

	public function getNamespaces()
	{
		return $this->namespaces;
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_USE);
	}

	public function tokenize($string)
	{
		$tokens = new tokenizer\tokens($string);

		if ($tokens->currentTokenHasName(T_OPEN_TAG) === true)
		{
			$tokens->next();
		}

		return $this->setFromTokens($tokens);
	}

	public function getIteratorInstance()
	{
		return new phpUse\iterator();
	}

	protected function appendCurrentToken(tokenizer\tokens $tokens)
	{
		if ($tokens->currentTokenHasName(T_STRING) === true || $tokens->currentTokenHasName(T_NS_SEPARATOR) === true)
		{
			if ($this->as === false)
			{
				$this->namespace .= $tokens->current()->getValue();
			}
			else
			{
				$this->alias .= $tokens->current()->getValue();
			}
		}

		parent::appendCurrentToken($tokens);

		switch (true)
		{
			case $tokens->currentTokenHasValue(';'):
				$this->namespaces[$this->namespace] = $this->alias ?: null;
				$this->namespace = '';
				$this->alias = '';
				$this->as = false;
				$this->stop();
				break;

			case $tokens->currentTokenHasName(T_AS):
				$this->as = true;
				break;

			case $tokens->currentTokenHasValue(','):
				$this->namespaces[$this->namespace] = $this->alias;
				$this->namespace = '';
				$this->alias = '';
				$this->as = false;
				break;
		}

		return $this;
	}

	protected function start(tokenizer\tokens $tokens)
	{
		$this->namespaces = array();

		return parent::start($tokens);
	}
}

?>
