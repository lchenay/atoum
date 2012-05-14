<?php

namespace mageekguy\atoum;

class depedencies implements \arrayAccess, \serializable
{
	protected $lock = false;
	protected $injectors = array();
	protected $defaultInjector = null;

	public function __construct()
	{
		$this->setDefaultInjector(function() { return new static(); });
	}

	public function serialize()
	{
		return null;
	}

	public function unserialize($string)
	{
		return $this;
	}

	public function getInjectors()
	{
		return $this->injectors;
	}

	public function setDefaultInjector($mixed)
	{
		$this->defaultInjector = self::buildInjector($mixed);

		return $this;
	}

	public function lock()
	{
		$this->lock = true;

		return $this;
	}

	public function unlock()
	{
		$this->lock = false;

		return $this;
	}

	public function offsetSet($mixed, $injector)
	{
		$key = self::getKey($mixed);

		if ($this->lock === false || isset($this->injectors[$key]) === false)
		{
			$this->injectors[$key] = self::buildInjector($injector);
		}

		return $this;
	}

	public function offsetGet($mixed)
	{
		$key = self::getKey($mixed);

		if (isset($this->injectors[$key]) === false)
		{
			$parent = get_parent_class($key);

			while ($parent !== false)
			{
				if (isset($this->injectors[$parent]) === true)
				{
					return $this->injectors[$parent];
				}
				else
				{
					$parent = get_parent_class($parent);
				}
			}

			$this->injectors[$key] = $this->defaultInjector->__invoke();
		}

		return $this->injectors[$key];
	}

	public function offsetUnset($mixed)
	{
		$key = self::getKey($mixed);

		if (isset($this->injectors[$key]) === true)
		{
			unset($this->injectors[$key]);
		}

		return $this;
	}

	public function offsetExists($mixed)
	{
		return isset($this->injectors[self::getKey($mixed)]);
	}

	public function isLocked($mixed)
	{
		return ($this->lock === true && isset($this->injectors[self::getKey($mixed)]) === true);
	}

	protected static function getKey($value)
	{
		return is_object($value) ? get_class($value) : (string) $value;
	}

	protected static function buildInjector($injector)
	{
		if ($injector instanceof \closure === false && $injector instanceof self === false)
		{
			$injector = function() use ($injector) { return $injector; };
		}

		return $injector;
	}
}

?>
