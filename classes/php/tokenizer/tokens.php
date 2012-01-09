<?php

namespace mageekguy\atoum\php\tokenizer;

class tokens extends \arrayIterator
{
	protected $skippedTokenNames = array();

	public function __construct($value = '')
	{
		parent::__construct(array());

		foreach (token_get_all((string) $value) as $token)
		{
			$this->append(is_string($token) ? new token($token) : new token($token[1], $token[0]));
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

	public function nextTokenHasName($name)
	{
		$hasName = false;

		if ($this->valid() === true)
		{
			$key = $this->key();

			$this->next();

			while ($this->valid() === true && $hasName === false)
			{
				if ($this->currentTokenHasName($name) === true)
				{
					$hasName = true;
				}
				else
				{
					$this->next();
				}
			}

			$this->seek($key);

			if ($this->key() !== $key)
			{
				throw new \exception($this->key() . '/' . $key . '/' . $this->current() . '/' . $this);
			}
		}

		return $hasName;
	}
}

?>
