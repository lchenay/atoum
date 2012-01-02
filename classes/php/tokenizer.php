<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum\php\tokenizer
;

class tokenizer
{
	protected $iterator = null;
	protected $factory = null;

	public function tokenize($string)
	{
		$this->iterator = new tokenizer\iterators\phpScript();

		$tokens = token_get_all($string);

		$this->iterator->tokenize($tokens);

		return $this;
	}

	public function getIterator()
	{
		return $this->iterator;
	}
}

?>
