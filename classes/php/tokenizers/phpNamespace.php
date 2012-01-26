<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpNamespace
;

class phpNamespace extends php\tokenizer
{
	protected $stack = null;
	protected $hasCurlyBrace = false;

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = $tokens->currentTokenHasName(T_NAMESPACE);

		$this->hasCurlyBrace = $tokens->nextTokenHasValue('{', array(T_STRING, T_WHITESPACE));

		return $canTokenize;
	}

	public function getIteratorInstance()
	{
		return new phpNamespace\iterator();
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
}

?>
