<?php

namespace mageekguy\atoum\php\tokenizer;

use
	mageekguy\atoum\exceptions
;

class tokens extends \arrayIterator
{
	protected $skippedTokenNames = array();

	public function __construct($value = '')
	{
		parent::__construct(array());

		foreach (token_get_all((string) $value) as $token)
		{
			$this->append(self::getTokenInstance($token));
		}
	}

	public function __toString()
	{
		$string = '';

		foreach ($this as $token)
		{
			$string .= $token;
		}

		return $string;
	}

	public function next()
	{
		parent::next();

		if (sizeof($this->skippedTokenNames) > 0)
		{
			while ($this->valid() === true && in_array($this->current()->getName(), $this->skippedTokenNames) === true)
			{
				$this->next();
			}
		}

		return $this;
	}

	public function prev()
	{
		if ($this->valid() === true)
		{
			$this->seek($this->key() - 1);

			if (sizeof($this->skippedTokenNames) > 0)
			{
				while ($this->valid() === true && in_array($this->current()->getName(), $this->skippedTokenNames) === true)
				{
					$this->prev();
				}
			}
		}

		return $this;
	}

	public function rewind()
	{
		parent::rewind();

		return $this;
	}

	public function skipTokenNames(array $tokenNames)
	{
		$this->skippedTokenNames = $tokenNames;

		return $this;
	}

	public function getSkippedTokenNames()
	{
		return $this->skippedTokenNames;
	}

	public function resetSkippedTokenNames()
	{
		$this->skippedTokenNames = array();

		return $this;
	}

	public function currentTokenHasName($name)
	{
		return ($this->valid() === true && $this->current()->getName() === $name);
	}

	public function currentTokenHasValue($value)
	{
		return ($this->valid() === true && $this->current()->getValue() === $value);
	}

	public function nextTokenHasName($name, array $skippedTokenNames = array())
	{
		$hasName = false;

		if ($this->valid() === true)
		{
			$key = $this->key();

			$this->next();

			while ($this->valid() === true && $this->currentTokenIsSkipped($skippedTokenNames) === true)
			{
				$this->next();
			}

			$hasName = $this->currentTokenHasName($name);

			$this->seek($key);
		}

		return $hasName;
	}

	public function nextTokenHasValue($value, array $skippedTokenNames = array())
	{
		$hasValue = false;

		if ($this->valid() === true)
		{
			$key = $this->key();

			$this->next();

			while ($this->valid() === true && $this->currentTokenIsSkipped($skippedTokenNames) === true)
			{
				$this->next();
			}

			$hasValue = $this->currentTokenHasValue($value);

			$this->seek($key);
		}

		return $hasValue;
	}

	public function goToNextTokenWithName($name)
	{
		$tokenFound = false;

		if ($this->valid() === true)
		{
			$key = $this->key();

			while ($this->valid() === true && $tokenFound === false)
			{
				$tokenFound = $this->next()->currentTokenHasName($name);
			}

			if ($tokenFound === false)
			{
				$this->seek($key);
			}
		}

		return $tokenFound;
	}

	protected function currentTokenIsSkipped(array $skippedTokenNames)
	{
		$skippedTokenNames = array_merge($this->skippedTokenNames, $skippedTokenNames);

		return (sizeof($skippedTokenNames) <= 0 ? false : in_array($this->current()->getName(), $skippedTokenNames) === true);
	}

	protected static function getTokenInstance($value)
	{
		return (is_string($value) ? new token($value) : new token($value[1], $value[0]));
	}
}

?>
