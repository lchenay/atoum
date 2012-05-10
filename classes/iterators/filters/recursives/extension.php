<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class extension extends \recursiveFilterIterator
{
	protected $depedencies = null;
	protected $acceptedExtensions = array();

	public function __construct($mixed, array $acceptedExtensions, atoum\depedencies $depedencies = null)
	{
		$this
			->setDepedencies($depedencies ?: new atoum\depedencies())
			->setAcceptedExtensions($acceptedExtensions)
		;

		if ($mixed instanceof \recursiveIterator === false)
		{
			$mixed = $this->depedencies['directory\iterator']((string) $mixed);
		}

		parent::__construct($mixed);
	}

	public function setDepedencies(atoum\depedencies $depedencies)
	{
		$this->depedencies = $depedencies[$this];

		$this->depedencies->lock();
		$this->depedencies['directory\iterator'] = function($path) { return new \recursiveDirectoryIterator($path); };
		$this->depedencies->unlock();

		return $this;
	}

	public function getDepedencies()
	{
		return $this->depedencies;
	}

	public function setAcceptedExtensions(array $extensions)
	{
		array_walk($extensions, function(& $extension) { $extension = trim($extension, '.'); });

		$this->acceptedExtensions = $extensions;

		return $this;
	}

	public function getAcceptedExtensions()
	{
		return $this->acceptedExtensions;
	}

	public function accept()
	{
		$path = basename((string) $this->getInnerIterator()->current());

		$extension = pathinfo($path, PATHINFO_EXTENSION);

		return ($extension == '' || in_array($extension, $this->acceptedExtensions) === true);
	}

	public function getChildren()
	{
		return new self($this->getInnerIterator()->getChildren(), $this->acceptedExtensions);
	}
}

?>
