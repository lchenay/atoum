<?php

namespace mageekguy\atoum\php\tokenizer;

use
	mageekguy\atoum\php\tokenizer
;

class collector
{
	protected $valueOfTokens = array();
	protected $afterToken = null;
	protected $from = null;
	protected $variable = null;
	protected $variableIsSet = false;
	protected $skippedTokens = array();
	protected $skippedValues = array();

	private $useNextValue = true;

	public function canCollect()
	{
		return ($this->from !== null && $this->from->valid() === true && $this->variableIsSet !== false);
	}

	public function valueOfToken($tokenName)
	{
		$this->valueOfTokens[] = $tokenName;

		return $this;
	}

	public function getValueOfTokens()
	{
		return $this->valueOfTokens;
	}

	public function afterToken($tokenName)
	{
		$this->afterToken = $tokenName;
		$this->useNextValue = false;

		return $this;
	}

	public function getAfterName()
	{
		return $this->afterToken;
	}

	public function from(tokenizer\tokens $tokens)
	{
		$this->from = $tokens;

		return $this;
	}

	public function getFrom()
	{
		return $this->from;
	}

	public function inString(& $string)
	{
		return $this->setVariable($string = '');
	}

	public function & getInString()
	{
		return $this->variable;
	}

	public function inArray(& $array)
	{
		return $this->setVariable($array = array());
	}

	public function & getInArray()
	{
		return $this->variable;
	}

	public function skipToken($tokenName)
	{
		$this->skippedTokens[] = $tokenName;

		$this->skippedTokens = array_unique($this->skippedTokens);

		return $this;
	}

	public function getSkippedNames()
	{
		return $this->skippedTokens;
	}

	public function skipValue($tokenValue)
	{
		$this->skippedValues[] = $tokenValue;

		$this->skippedValues = array_unique($this->skippedValues);

		return $this;
	}

	public function getSkippedValues()
	{
		return $this->skippedValues;
	}

	public function execute()
	{
		if ($this->canCollect() === true)
		{
			if ($this->afterToken !== null && $this->useNextValue === false)
			{
				$this->useNextValue = $this->from->currentTokenHasName($this->afterToken);
			}
			else
			{
				$currentToken = $this->from->current();
				$currentTokenName = $currentToken->getName();
				$currentTokenValue = $currentToken->getValue();

				if (in_array($currentTokenName, $this->skippedTokens, true) === false && in_array($currentTokenValue, $this->skippedValues, true) === false)
				{
					if (sizeof($this->valueOfTokens) > 0 && in_array($currentTokenName, $this->valueOfTokens, true) === false)
					{
						$this->from = null;
					}
					else if (is_array($this->variable) === true)
					{
						$this->variable[] = $currentTokenValue;
					}
					else
					{
						$this->variable .= $currentTokenValue;
					}
				}
			}
		}

		return $this;
	}

	protected function setVariable(& $variable)
	{
		$this->variable = & $variable;
		$this->variableIsSet = true;

		return $this;
	}
}

?>
