<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

class phpScript extends tokenizer\iterator
{
	public function __construct()
	{
		$this->setHandler(T_FUNCTION, function() {
				return new iterators\phpFunction();
			}
		);
	}
}

?>
