<?php

namespace mageekguy\atoum\php\tokenizer;

class iterator extends \arrayIterator
{
	public function __toString()
	{
		$string = '';

		foreach ($this as $token)
		{
			$string .= $token;
		}

		return $string;
	}
}

?>
