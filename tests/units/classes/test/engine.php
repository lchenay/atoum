<?php

namespace mageekguy\atoum\tests\units\test;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum
;

class engine extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function test__construct()
	{
		$this
			->if($engine = new \mock\mageekguy\atoum\test\engine())
			->then
				->object($engine->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
			->if($engine = new \mock\mageekguy\atoum\test\engine($depedencies = new atoum\depedencies()))
			->then
				->object($engine->getDepedencies())->isIdenticalTo($depedencies)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($engine = new \mock\mageekguy\atoum\test\engine())
			->then
				->object($engine->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($engine)
				->object($engine->getDepedencies())->isIdenticalTo($depedencies)
		;
	}
}

?>
