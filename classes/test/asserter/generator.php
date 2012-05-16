<?php

namespace mageekguy\atoum\test\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

class generator extends asserter\generator
{
	protected $test = null;

	public function __construct(atoum\test $test, atoum\depedencies $depedencies = null)
	{
		parent::__construct($depedencies);

		$this->setTest($test);
	}

	public function __get($property)
	{
		return $this->test->getAssertionManager()->invoke($property);
	}

	public function __call($method, $arguments)
	{
		return $this->test->getAssertionManager()->invoke($method, $arguments);
	}

	public function setTest(atoum\test $test)
	{
		if ($this->test !== $test)
		{
			$this->test = $test;
		}

		return $this;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getAsserterInstance($asserter, array $arguments = array())
	{
		return parent::getAsserterInstance($asserter, $arguments)->setWithTest($this->test);
	}
}

?>
