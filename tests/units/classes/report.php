<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class report extends atoum\test
{
	public function testTestedClass()
	{
		$this->testedClass
			->isSubclassOf('mageekguy\atoum\observer')
			->isSubclassOf('mageekguy\atoum\adapter\aggregator')
		;
	}

	public function test__construct()
	{
		$this
			->if($report = new atoum\report())
			->then
				->variable($report->getTitle())->isNull()
				->object($depedencies = $report->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
				->boolean(isset($depedencies['mageekguy\atoum\report']['locale']))->isTrue()
				->boolean(isset($depedencies['mageekguy\atoum\report']['adapter']))->isTrue()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\report'] = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\report']['locale'] = $localeInjector = function() use (& $locale) { return $locale = new atoum\locale(); })
			->and($depedencies['mageekguy\atoum\report']['adapter'] = $adapterInjector = function() use (& $adapter) { return $adapter = new atoum\adapter(); })
			->and($report = new atoum\report($depedencies))
			->then
				->variable($report->getTitle())->isNull()
				->object($report->getDepedencies())->isIdenticalTo($depedencies)
				->boolean(isset($depedencies['mageekguy\atoum\report']['locale']))->isTrue()
				->object($depedencies['mageekguy\atoum\report']['locale'])->isIdenticalTo($localeInjector)
				->boolean(isset($depedencies['mageekguy\atoum\report']['adapter']))->isTrue()
				->object($depedencies['mageekguy\atoum\report']['adapter'])->isIdenticalTo($adapterInjector)
				->object($report->getLocale())->isIdenticalTo($locale)
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->array($report->getFields())->isEmpty()
				->array($report->getWriters())->isEmpty()
		;
	}

	public function testSetTitle()
	{
		$this
			->if($report = new atoum\report())
			->then
				->object($report->setTitle($title = uniqid()))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo($title)
				->object($report->setTitle($title = rand(1, PHP_INT_MAX)))->isEqualTo($report)
				->string($report->getTitle())->isEqualTo((string) $title)
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($report = new atoum\report())
			->then
				->object($report->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($report)
				->boolean(isset($depedencies['mageekguy\atoum\report']['locale']))->isTrue()
				->boolean(isset($depedencies['mageekguy\atoum\report']['adapter']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\report'] = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\report']['locale'] = $localeInjector = function() { return new atoum\locale(); })
			->and($depedencies['mageekguy\atoum\report']['adapter'] = $adapterInjector = function() { return new atoum\adapter(); })
			->then
				->object($report->setDepedencies($depedencies))->isIdenticalTo($report)
				->boolean(isset($depedencies['mageekguy\atoum\report']['locale']))->isTrue()
				->object($depedencies['mageekguy\atoum\report']['locale'])->isIdenticalTo($localeInjector)
				->boolean(isset($depedencies['mageekguy\atoum\report']['adapter']))->isTrue()
				->object($depedencies['mageekguy\atoum\report']['adapter'])->isIdenticalTo($adapterInjector)
		;
	}

	public function testSetLocale()
	{

		$this
			->if($report = new atoum\report())
			->then
				->object($report->setLocale($locale = new atoum\locale()))->isIdenticalTo($report)
				->object($report->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testAddField()
	{
		$this
			->if($report = new atoum\report())
			->then
				->object($report->addField($field = new \mock\mageekguy\atoum\report\field))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field))
				->object($report->addField($otherField = new \mock\mageekguy\atoum\report\field()))->isIdenticalTo($report)
				->array($report->getFields())->isIdenticalTo(array($field, $otherField))
		;
	}
}

?>
