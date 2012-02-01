<?php

namespace mageekguy\atoum\php\tokenizers\phpClass;

use
	mageekguy\atoum\php\tokenizer
;

class iterator extends tokenizer\iterator
{
	const type = 'class';

	public function getName()
	{
		$nameCollector = new tokenizer\collector();

		$nameCollector
			->putInString($name)
			->valueOfToken(T_STRING)
				->afterToken(T_CLASS)
				->beforeToken(T_IMPLEMENTS)
				->beforeToken(T_EXTENDS)
				->beforeValue('{')
					->skipToken(T_WHITESPACE)
			->from($this)
			->execute()
		;

		return ($name ?: null);
	}
}

?>
