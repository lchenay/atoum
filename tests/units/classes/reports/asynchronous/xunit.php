<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	ageekguy\atoum\asserter\exception,
	mageekguy\atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class xunit extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\reports\asynchronous');
	}

	public function testClassConstants()
	{
		$this->string(atoum\reports\asynchronous\xunit::defaultTitle)->isEqualTo('atoum testsuite');
	}

	public function test__construct()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->array($report->getFields(atoum\runner::runStart))->isEmpty()
				->object($depedencies = $report->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['locale']))->isTrue()
				->boolean(isset($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['adapter']))->isTrue()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['locale'] = $localeInjector = function() use (& $locale) { return $locale = new atoum\locale(); })
			->and($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['adapter'] = $adapterInjector = function() use ($adapter) { return $adapter; })
			->and($report = new reports\xunit($depedencies))
			->then
				->object($report->getDepedencies())->isIdenticalTo($depedencies)
				->object($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['locale'])->isIdenticalTo($localeInjector)
				->object($depedencies['mageekguy\atoum\reports\asynchronous\xunit']['adapter'])->isIdenticalTo($adapterInjector)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->array($report->getFields())->isEmpty()
				->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($depedencies) {
								new reports\xunit($depedencies);
							}
						)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('libxml PHP extension is mandatory for xunit report')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
				->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($report = new reports\xunit())
			->then
				->variable($report->getTitle())->isNull()
				->castToString($report)->isEmpty()
				->string($report->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo(atoum\reports\asynchronous\xunit::defaultTitle)
				->castToString($report)->isNotEmpty()
			->if($report = new reports\xunit())
			->then
				->string($report->setTitle($title = uniqid())->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo($title)
			->if($report = new reports\xunit())
			->and($writer = new \mock\mageekguy\atoum\writers\file())
			->and($writer->getMockController()->write = $writer)
			->then
				->when(function() use ($report, $writer) { $report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner()); })
					->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
		;
	}
}

?>
