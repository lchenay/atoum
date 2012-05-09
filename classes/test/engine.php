<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum
;

abstract class engine
{
	protected $depedencies = null;

	public function __construct(atoum\depedencies $depedencies = null)
	{
		$this->setDepedencies($depedencies ?: new atoum\depedencies());
	}

	public function setDepedencies(atoum\depedencies $depedencies)
	{
		$this->depedencies = $depedencies;

		if (isset($this->depedencies[$this]) === false)
		{
			$this->depedencies[$this] = new atoum\depedencies();
		}

		$this->depedencies[$this]->lock();
		$this->depedencies[$this]['score'] = function($depedencies) { return new atoum\score($depedencies); };
		$this->depedencies[$this]->unlock();

		return $this;
	}

	public function getDepedencies()
	{
		return $this->depedencies;
	}

	public abstract function isAsynchronous();
	public abstract function run(atoum\test $test);
	public abstract function getScore();
}

?>
