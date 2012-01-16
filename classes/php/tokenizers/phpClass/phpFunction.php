<?php

namespace mageekguy\atoum\php\tokenizers\phpClass;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass\phpFunction
;

class phpFunction extends php\tokenizers\phpFunction
{
	public function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}

	protected function start(php\tokenizer\tokens $tokens)
	{
		$goToPreviousToken = true;
		$currentTokenIsWhitespace = false;

		while ($tokens->valid() === true && $goToPreviousToken === true)
		{
			$tokens->prev();

			switch (true)
			{
				case $tokens->currentTokenHasName(T_WHITESPACE):
					$currentTokenIsWhitespace = true;
					break;

				case $tokens->currentTokenHasName(T_FINAL):
				case $tokens->currentTokenHasName(T_ABSTRACT):
				case $tokens->currentTokenHasName(T_STATIC):
				case $tokens->currentTokenHasName(T_PUBLIC):
				case $tokens->currentTokenHasName(T_PROTECTED):
				case $tokens->currentTokenHasName(T_PRIVATE):
					$currentTokenIsWhitespace = false;
					break;

				default:
					$goToPreviousToken = false;
			}
		}

		if ($currentTokenIsWhitespace === true)
		{
			$tokens->next();
		}

		$tokens->next();

		return parent::start($tokens);
	}
}

?>
