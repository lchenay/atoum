<?php

namespace mageekguy\atoum\php\tokenizer;

use
	mageekguy\atoum\php\tokenizer
;

class collector
{
	protected $valueOf = null;
	protected $afterName = null;
	protected $from = null;
	protected $variable = null;
	protected $variableIsSet = false;
	protected $skippedNames = array();
	protected $skippedValues = array();

	private $useNextValue = true;

	public function canCollect()
	{
		return ($this->from !== null && $this->from->valid() === true && $this->variableIsSet !== false);
	}

	public function valueOf($tokenName)
	{
		$this->valueOf = $tokenName;

		return $this;
	}

	public function getValueOf()
	{
		return $this->valueOf;
	}

	public function afterName($tokenName)
	{
		$this->afterName = $tokenName;
		$this->useNextValue = false;

		return $this;
	}

	public function getAfterName()
	{
		return $this->afterName;
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

	public function skipName($tokenName)
	{
		$this->skippedNames[] = $tokenName;

		$this->skippedNames = array_unique($this->skippedNames);

		return $this;
	}

	public function getSkippedNames()
	{
		return $this->skippedNames;
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
			if ($this->afterName !== null && $this->useNextValue === false)
			{
				$this->useNextValue = $this->from->currentTokenHasName($this->afterName);
			}
			else
			{
				$currentToken = $this->from->current();
				$currentTokenName = $currentToken->getName();
				$currentTokenValue = $currentToken->getValue();

				if (in_array($currentTokenName, $this->skippedNames) === false && in_array($currentTokenValue, $this->skippedValues) === false)
				{
					if ($this->valueOf !== null && $this->valueOf !== $currentTokenName)
					{
						$this->from = null;
					}
					else if (is_array($this->variable) === true)
					{
						$this->variable[] = $currentTokenValue;
					}
					else
					{
						$this->variable = $currentTokenValue;

						if ($this->afterName !== null)
						{
							$this->from = null;
						}
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
