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
		if ($this->isLocked($mixed) === false)
		{
			$this->injectors[self::getKey($mixed)] = self::buildInjector($injector);
		}

		return $this;
	}

	public function offsetGet($mixed)
	{
		$key = self::getKey($mixed);

		if ($this->offsetExists($key) === false)
		{
			$this->offsetSet($key, $this->defaultInjector->__invoke());
		}

		return $this->injectors[$key];
	}

	public function offsetUnset($mixed)
	{
		$key = self::getKey($mixed);

		if ($this->offsetExists($key) === true)
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
		return ($this->lock === true && $this->offsetExists(self::getKey($mixed)) === true);
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
