<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class dot extends \recursiveFilterIterator
{
	protected $depedencies = null;

	public function __construct($mixed, atoum\depedencies $depedencies = null)
	{
		$this->setDepedencies($depedencies ?: new atoum\depedencies());

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

	public function accept()
	{
		return (substr(basename((string) $this->getInnerIterator()->current()), 0, 1) != '.');
	}
}

?>
