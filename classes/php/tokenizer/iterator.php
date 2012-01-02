<?php

namespace mageekguy\atoum\php\tokenizer;

abstract class iterator extends \arrayIterator
{
	protected $handlers = array();

	public function __toString()
	{
		$string = '';

		foreach ($this as $token)
		{
			$string .= $token;
		}

		return $string;
	}

	public function setHandler($tokenName, \closure $handler)
	{
		$this->handlers[(string) $tokenName] = $handler;

		return $this;
	}

	public function tokenize(array & $tokens)
	{
		reset($tokens);

		while ($tokens)
		{
			$token = current($tokens);

			if (isset($token[0]) === true && isset($this->handlers[$token[0]]) === true)
			{
				$value = $this->handlers[$token[0]]();
				$tokens = $value->tokenize($tokens);
			}
			else
			{
				$value = (isset($token[1]) === false ? $token : $token[1]);

				array_shift($tokens);
			}

			$this[] = $value;
		}

		return $tokens;
	}
}

?>
