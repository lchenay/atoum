<?php

namespace mageekguy\atoum\php\tokenizer;

class iterator extends tokens
{
	const type = 'script';

	public function __toString()
	{
		$string = '';

		foreach ($this as $token)
		{
			$string .= $token;
		}

		return $string;
	}

	public function getType()
	{
		return static::type;
	}
}

?>
