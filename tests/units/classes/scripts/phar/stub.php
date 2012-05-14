<?php

namespace mageekguy\atoum\tests\units\scripts\phar;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts\phar,
	mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../../../runner.php';

class stub extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\scripts\runner');
	}

	public function testSetDepedencies()
	{
		$this
			->if($stub = new phar\stub(uniqid()))
			->then
				->object($stub->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($stub)
				->object($stubDepedencies = $stub->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\scripts\phar\stub'])
				->boolean(isset($stubDepedencies['locale']))->isTrue()
				->boolean(isset($stubDepedencies['adapter']))->isTrue()
				->boolean(isset($stubDepedencies['arguments\parser']))->isTrue()
				->boolean(isset($stubDepedencies['writers\output']))->isTrue()
				->boolean(isset($stubDepedencies['writers\error']))->isTrue()
				->boolean(isset($stubDepedencies['phar']))->isTrue()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['locale'] = $localeInjector = function() {})
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['adapter'] = $adapterInjector = function() {})
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['arguments\parser'] = $argumentsParserInjector = function() {})
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['writers\output'] = $outputWriterInjector = function() {})
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['writers\error'] = $errorWriterInjector = function() {})
			->and($depedencies['mageekguy\atoum\scripts\phar\stub']['phar'] = $pharInjector = function() {})
			->then
				->object($stub->setDepedencies($depedencies))->isIdenticalTo($stub)
				->object($stubDepedencies = $stub->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\scripts\phar\stub'])
				->object($stubDepedencies['locale'])->isIdenticalTo($localeInjector)
				->object($stubDepedencies['adapter'])->isIdenticalTo($adapterInjector)
				->object($stubDepedencies['arguments\parser'])->isIdenticalTo($argumentsParserInjector)
				->object($stubDepedencies['writers\output'])->isIdenticalTo($outputWriterInjector)
				->object($stubDepedencies['writers\error'])->isIdenticalTo($errorWriterInjector)
				->object($stubDepedencies['phar'])->isIdenticalTo($pharInjector)
		;
	}

	public function testClassConstants()
	{
		$this
			->string(phar\stub::scriptsDirectory)->isEqualTo('scripts')
			->string(phar\stub::scriptsExtension)->isEqualTo('.php')
		;
	}

	public function testUpdate()
	{
		$this
			->if($stub = new phar\stub(uniqid()))
			->and($stub->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 1 : ini_get($name); })
			->and($stub->setLocale($locale = new mock\locale()))
			->and($stub->setOutputWriter($stdOut = new mock\writers\std\out()))
			->and($stdOut->getMockController()->write = function() {})
			->then
				->exception(function() use ($stub) { $stub->update(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to update the PHAR, phar.readonly is set, use \'-d phar.readonly=0\'')
			->if($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 0 : $name = 'allow_url_fopen' ? 0 : ini_get($name); })
			->then
				->exception(function() use ($stub) { $stub->update(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to update the PHAR, allow_url_fopen is not set, use \'-d allow_url_fopen=1\'')
			->if($adapter->ini_get = function($name) { return $name === 'phar.readonly' ? 0 : $name = 'allow_url_fopen' ? 1 : ini_get($name); })
			->and($depedencies = $stub->getDepedencies())
			->and($depedencies['phar'] = function($path) use (& $phar) {
					$pharController = new atoum\mock\controller();
					$pharController->__construct = function() {};
					$pharController->offsetExists = true;
					$pharController->offsetGet = function($path) { return $path; };
					$pharController->offsetSet = function() {};
					$phar = new \mock\phar($path);

					return $phar;
				}
			)
			->and($adapter->file_get_contents = function($path) use (& $currentVersion) {
					switch ($path)
					{
						case 'versions':
							return serialize(array('1' => $currentVersion = uniqid(), 'current' => '1'));

						case phar\stub::updateUrl:
							return json_encode(array());

						default:
							return false;
					}
				}
			)
			->then
				->object($stub->update())->isIdenticalTo($stub)
				->adapter($adapter)
					->call('file_get_contents')->withArguments(sprintf(phar\stub::updateUrl, json_encode(array($currentVersion))))->once()
				->mock($phar)
					->call('offsetGet')->withArguments('versions')->once()
				->mock($locale)
					->call('_')
						->withArguments('Checking if a new version is available...')->once()
						->withArguments('Checking if a new version is available... Done !')->once()
						->withArguments('There is no new version available !')->once()
				->mock($stdOut)
					->call('clear')->once()
					->call('write')->withArguments('Checking if a new version is available...')->once()
					->call('write')->withArguments('Checking if a new version is available... Done !' . PHP_EOL)->once()
		;
	}
}

?>
