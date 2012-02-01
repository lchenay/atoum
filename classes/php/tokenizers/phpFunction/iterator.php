<?php

namespace mageekguy\atoum\php\tokenizers\phpFunction;

use
	mageekguy\atoum\php\tokenizer
;

class iterator extends tokenizer\iterator
{
	const type = 'function';

	public function getName()
	{
		$nameCollector = new tokenizer\collector();

		$nameCollector
			->putInString($name)
			->valueOfToken(T_STRING)
				->afterToken(T_FUNCTION)
				->beforeValue('(')
					->skipToken(T_WHITESPACE)
			->from($this)
			->execute()
		;

		return ($name ?: null);
	}
}

?>
