<?php

namespace mageekguy\atoum\php\tokenizer;

use
	mageekguy\atoum\php\tokenizer
;

class collector
{
	protected $valueOfTokens = array();
	protected $beforeTokens = array();
	protected $beforeValues = array();
	protected $afterTokens = array();
	protected $from = null;
	protected $destination = null;
	protected $destinationIsSet = false;
	protected $skippedTokens = array();
	protected $skippedValues = array();

	private $isAfterToken = true;

	public function canCollect()
	{
		return ($this->from !== null && $this->from->valid() === true && $this->destinationIsSet !== false);
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

	public function beforeToken($tokenName)
	{
		$this->beforeTokens[] = $tokenName;

		return $this;
	}

	public function getBeforeTokens()
	{
		return $this->beforeTokens;
	}

	public function beforeValue($tokenName)
	{
		$this->beforeValues[] = $tokenName;

		return $this;
	}

	public function getBeforeValues()
	{
		return $this->beforeValues;
	}

	public function afterToken($tokenName)
	{
		$this->afterTokens[] = $tokenName;
		$this->isAfterToken = false;

		return $this;
	}

	public function getAfterTokens()
	{
		return $this->afterTokens;
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

	public function putInString(& $string)
	{
		return $this->setVariable($string = '');
	}

	public function putInArray(& $array)
	{
		return $this->setVariable($array = array());
	}

	public function & getDestination()
	{
		return $this->destination;
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
		if ($this->from !== null)
		{
			$this->from->rewind();
		}

		while ($this->canCollect() === true)
		{
			$currentToken = $this->from->current();
			$currentTokenName = $currentToken->getName();
			$currentTokenValue = $currentToken->getValue();

			if (sizeof($this->afterTokens) > 0 && $this->isAfterToken === false)
			{
				$this->isAfterToken = in_array($currentTokenName, $this->afterTokens);
			}
			else
			{
				if (
						(sizeof($this->beforeTokens) > 0 && in_array($currentTokenName, $this->beforeTokens) === true)
						||
						(sizeof($this->beforeValues) > 0 && in_array($currentTokenValue, $this->beforeValues) === true)
					)
				{
					$this->from = null;
				}
				else if (in_array($currentTokenName, $this->skippedTokens, true) === false && in_array($currentTokenValue, $this->skippedValues, true) === false)
				{
					if (sizeof($this->valueOfTokens) <= 0 || in_array($currentTokenName, $this->valueOfTokens, true) === true)
					{
						if (is_array($this->destination) === true)
						{
							$this->destination[] = $currentTokenValue;
						}
						else
						{
							$this->destination .= $currentTokenValue;
						}
					}
				}
			}

			if ($this->from !== null)
			{
				$this->from->next();
			}
		}

		return $this;
	}

	protected function setVariable(& $variable)
	{
		$this->destination = & $variable;
		$this->destinationIsSet = true;

		return $this;
	}
}

?>
