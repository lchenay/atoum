<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\reports\asynchronous
;

require_once __DIR__ . '/../../../runner.php';

class vim extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\reports\asynchronous');
	}

	public function test__construct()
	{
		$this
			->if($report = new asynchronous\vim())
			->then
				->object($depedencies = $report->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($depedencies['locale']))->isTrue()
				->boolean(isset($depedencies['adapter']))->isTrue()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\reports\asynchronous\vim']['locale'] = $localeInjector = function() use (& $locale) { return $locale = new atoum\locale(); })
			->and($depedencies['mageekguy\atoum\reports\asynchronous\vim']['adapter'] = $adapterInjector = function() use (& $adapter) { return $adapter = new atoum\adapter(); })
			->and($report = new asynchronous\vim($depedencies))
			->then
				->object($report->getDepedencies())->isIdenticalTo($depedencies[$report])
				->object($depedencies['mageekguy\atoum\reports\asynchronous\vim']['locale'])->isIdenticalTo($localeInjector)
				->object($depedencies['mageekguy\atoum\reports\asynchronous\vim']['adapter'])->isIdenticalTo($adapterInjector)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}
}

?>
