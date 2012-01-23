<?php

namespace mageekguy\atoum\php\tokenizers;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpNamespace
;

class phpNamespace extends php\tokenizer
{
	public function canTokenize(tokenizer\tokens $tokens)
	{
		return $tokens->currentTokenHasName(T_NAMESPACE);
	}

	public function getIteratorInstance()
	{
		return new phpNamespace\iterator();
	}
}

?>
