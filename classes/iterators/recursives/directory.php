<?php

namespace mageekguy\atoum\iterators\recursives;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class directory implements \iteratorAggregate
{
	protected $depedencies = null;
	protected $path = null;
	protected $acceptDots = false;
	protected $acceptedExtensions = array('php');

	public function __construct($path = null, atoum\depedencies $depedencies = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}

		$this->setDepedencies($depedencies ?: new atoum\depedencies());
	}

	public function setDepedencies(atoum\depedencies $depedencies)
	{
		$this->depedencies = $depedencies[$this];

		$this->depedencies->lock();
		$this->depedencies['directory\iterator'] = function($path) { return new \recursiveDirectoryIterator($path); };
		$this->depedencies['filters\dot'] = function($iterator, $depedencies) { return new atoum\iterators\filters\recursives\dot($iterator, $depedencies); };
		$this->depedencies['filters\extension'] = function($iterator, $extensions) { return new atoum\iterators\filters\recursives\extension($iterator, $extensions); };
		$this->depedencies->unlock();

		return $this;
	}

	public function setPath($path)
	{
		$this->path = (string) $path;

		return $this;
	}

	public function getDepedencies()
	{
		return $this->depedencies;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getIterator($path = null)
	{
		if ($path !== null)
		{
			$this->setPath($path);
		}
		else if ($this->path === null)
		{
			throw new exceptions\runtime('Path is undefined');
		}

		$iterator = $this->depedencies['directory\iterator']($this->path);

		if ($this->acceptDots === false)
		{
			$iterator = $this->depedencies['filters\dot']($iterator, $this->depedencies);
		}

		if (sizeof($this->acceptedExtensions) > 0)
		{
			$iterator = $this->depedencies['filters\extension']($iterator, $this->acceptedExtensions);
		}

		return $iterator;
	}

	public function dotsAreAccepted()
	{
		return $this->acceptDots;
	}

	public function acceptDots()
	{
		$this->acceptDots = true;

		return $this;
	}

	public function refuseDots()
	{
		$this->acceptDots = false;

		return $this;
	}

	public function getAcceptedExtensions()
	{
		return $this->acceptedExtensions;
	}

	public function acceptExtensions(array $extensions)
	{
		$this->acceptedExtensions = array();

		foreach ($extensions as $extension)
		{
			$this->acceptedExtensions[] = self::cleanExtension($extension);
		}

		return $this;
	}

	public function acceptAllExtensions()
	{
		return $this->acceptExtensions(array());
	}

	public function refuseExtension($extension)
	{
		$key = array_search(self::cleanExtension($extension), $this->acceptedExtensions);

		if ($key !== false)
		{
			unset($this->acceptedExtensions[$key]);

			$this->acceptedExtensions = array_values($this->acceptedExtensions);
		}

		return $this;
	}

	protected static function cleanExtension($extension)
	{
		return trim($extension, '.');
	}
}

?>
