<?php

namespace mageekguy\atoum\php\tokenizers\phpClass;

use
	mageekguy\atoum\php,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizers\phpClass\phpFunction
;

class phpFunction extends php\tokenizers\phpFunction
{
	protected $final = '';
	protected $static = '';
	protected $abstract = '';
	protected $encapsulation = '';

	public function __construct($string = null)
	{
		$this->putInString($this->encapsulation)
			->valueOfToken(T_PUBLIC)
			->valueOfToken(T_PROTECTED)
			->valueOfToken(T_PRIVATE)
			->beforeToken(T_FUNCTION)
		;

		$this->putInString($this->abstract)
			->valueOfToken(T_ABSTRACT)
			->beforeToken(T_FUNCTION)
		;

		$this->putInString($this->final)
			->valueOfToken(T_FINAL)
			->beforeToken(T_FUNCTION)
		;

		$this->putInString($this->static)
			->valueOfToken(T_STATIC)
			->beforeToken(T_FUNCTION)
		;

		parent::__construct($string);
	}

	public function isPublic()
	{
		return ($this->encapsulation === '' || $this->encapsulation === 'public');
	}

	public function isProtected()
	{
		return ($this->encapsulation === 'protected');
	}

	public function isPrivate()
	{
		return ($this->encapsulation === 'private');
	}

	public function isAbstract()
	{
		return ($this->abstract != '');
	}

	public function isFinal()
	{
		return ($this->final != '');
	}

	public function isStatic()
	{
		return ($this->static != '');
	}

	public function getIteratorInstance()
	{
		return new phpFunction\iterator();
	}

	public function canTokenize(tokenizer\tokens $tokens)
	{
		$canTokenize = parent::canTokenize($tokens);

		if (
				$canTokenize === false
				&&
				(
					$tokens->currentTokenHasName(T_FINAL)
					||
					$tokens->currentTokenHasName(T_ABSTRACT)
					||
					$tokens->currentTokenHasName(T_STATIC)
					||
					$tokens->currentTokenHasName(T_PUBLIC)
					||
					$tokens->currentTokenHasName(T_PROTECTED)
					||
					$tokens->currentTokenHasName(T_PRIVATE)
				)
				&&
				$tokens->valid() === true
			)
		{
			$key = $tokens->key();

			$goToNextToken = true;

			while ($tokens->valid() === true && $goToNextToken === true)
			{
				$tokens->next();

				switch (true)
				{
					case $tokens->currentTokenHasName(T_WHITESPACE):
					case $tokens->currentTokenHasName(T_FINAL):
					case $tokens->currentTokenHasName(T_ABSTRACT):
					case $tokens->currentTokenHasName(T_STATIC):
					case $tokens->currentTokenHasName(T_PUBLIC):
					case $tokens->currentTokenHasName(T_PROTECTED):
					case $tokens->currentTokenHasName(T_PRIVATE):
						break;

					case  $tokens->currentTokenHasName(T_FUNCTION):
						$canTokenize = parent::canTokenize($tokens);
						$goToNextToken = false;
						break;

					default:
						$goToNextToken = false;
				}
			}

			$tokens->seek($key);
		}

		return $canTokenize;
	}

	protected function start(tokenizer\tokens $tokens)
	{
		$this->final = '';
		$this->static = '';
		$this->abstract = '';
		$this->encapsulation = '';

		return parent::start($tokens);
	}
}

?>
