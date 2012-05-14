<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class builder extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		if (extension_loaded('svn') === false)
		{
			define('SVN_REVISION_HEAD', -1);
			define('PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS', 1);
			define('SVN_AUTH_PARAM_DEFAULT_USERNAME', 2);
			define('SVN_AUTH_PARAM_DEFAULT_PASSWORD', 3);
		}
	}

	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\script')
			->string(scripts\builder::defaultUnitTestRunnerScript)->isEqualTo('scripts/runner.php')
			->string(scripts\builder::defaultPharGeneratorScript)->isEqualTo('scripts/phar/generator.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($builder = new scripts\builder($name = uniqid()))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($builder->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($builder->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($builder->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
				->object($builder->getErrorWriter())->isInstanceOf('mageekguy\atoum\writers\std\err')
				->object($builder->getSuperglobals())->isInstanceOf('mageekguy\atoum\superglobals')
				->array($builder->getRunnerConfigurationFiles())->isEmpty()
				->variable($builder->getVersion())->isNull()
				->variable($builder->getWorkingDirectory())->isNull()
				->variable($builder->getDestinationDirectory())->isNull()
				->variable($builder->getErrorsDirectory())->isNull()
				->variable($builder->getScoreDirectory())->isNull()
				->variable($builder->getRevisionFile())->isNull()
				->string($builder->getUnitTestRunnerScript())->isEqualTo(scripts\builder::defaultUnitTestRunnerScript)
				->string($builder->getPharGeneratorScript())->isEqualTo(scripts\builder::defaultPharGeneratorScript)
				->variable($builder->getReportTitle())->isNull()
				->object($builder->getVcs())->isInstanceOf('mageekguy\atoum\scripts\builder\vcs\svn')
				->variable($builder->getTaggerEngine())->isNull()
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\scripts\builder']['locale'] = $locale = new atoum\locale())
			->and($depedencies['mageekguy\atoum\scripts\builder']['adapter'] = $adapter = new atoum\adapter())
			->and($depedencies['mageekguy\atoum\scripts\builder']['arguments\parser'] = $argumentsParser = new atoum\script\arguments\parser())
			->and($depedencies['mageekguy\atoum\scripts\builder']['writers\output'] = $stdOut = new atoum\writers\std\out())
			->and($depedencies['mageekguy\atoum\scripts\builder']['writers\error'] = $stdErr = new atoum\writers\std\err())
			->and($depedencies['mageekguy\atoum\scripts\builder']['superglobals'] = $superglobals = new atoum\superglobals())
			->and($depedencies['mageekguy\atoum\scripts\builder']['vcs\svn'] = $vcs = new atoum\scripts\builder\vcs\svn())
			->and($builder = new scripts\builder($name = uniqid(), $depedencies))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getLocale())->isIdenticalTo($locale)
				->object($builder->getAdapter())->isIdenticalTo($adapter)
				->object($builder->getArgumentsParser())->isIdenticalTo($argumentsParser)
				->object($builder->getOutputWriter())->isIdenticalTo($stdOut)
				->object($builder->getErrorWriter())->isIdenticalTo($stdErr)
				->object($builder->getSuperglobals())->isIdenticalTo($superglobals)
				->array($builder->getRunnerConfigurationFiles())->isEmpty()
				->variable($builder->getVersion())->isNull()
				->variable($builder->getWorkingDirectory())->isNull()
				->variable($builder->getDestinationDirectory())->isNull()
				->variable($builder->getErrorsDirectory())->isNull()
				->variable($builder->getScoreDirectory())->isNull()
				->variable($builder->getRevisionFile())->isNull()
				->string($builder->getUnitTestRunnerScript())->isEqualTo(scripts\builder::defaultUnitTestRunnerScript)
				->variable($builder->getReportTitle())->isNull()
				->string($builder->getPharGeneratorScript())->isEqualTo(scripts\builder::defaultPharGeneratorScript)
				->object($builder->getVcs())->isIdenticalTo($vcs)
				->variable($builder->getTaggerEngine())->isNull()
		;
	}

	public function testSetDepedencies()
	{
		$this
			->if($builder = new scripts\builder($name = uniqid()))
			->then
				->object($builder->setDepedencies($depedencies = new atoum\depedencies()))->isIdenticalTo($builder)
				->object($builderDepedencies = $builder->getDepedencies())->isIdenticalTo($depedencies['mageekguy\atoum\scripts\builder'])
				->boolean(isset($builderDepedencies['locale']))->isTrue()
				->boolean(isset($builderDepedencies['adapter']))->isTrue()
				->boolean(isset($builderDepedencies['arguments\parser']))->isTrue()
				->boolean(isset($builderDepedencies['writers\output']))->isTrue()
				->boolean(isset($builderDepedencies['writers\error']))->isTrue()
				->boolean(isset($builderDepedencies['superglobals']))->isTrue()
				->boolean(isset($builderDepedencies['vcs\svn']))->isTrue()
		;
	}

	public function testSetVersion()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setVersion($tag = uniqid()))->isIdenticalTo($builder)
				->string($builder->getVersion())->isIdenticalTo($tag)
				->object($builder->setVersion($tag = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getVersion())->isIdenticalTo((string) $tag)
		;
	}

	public function testSetSuperglobals()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($builder)
				->object($builder->getSuperglobals())->isIdenticalTo($superglobals);
		;
	}

	public function testGetPhp()
	{
		$superglobals = new atoum\superglobals();
		$superglobals->_SERVER['_'] = $php = uniqid();

		$builder = new scripts\builder(uniqid());
		$builder->setSuperglobals($superglobals);

		$this->string($builder->getPhpPath())->isEqualTo($php);

		unset($superglobals->_SERVER['_']);

		$builder = new scripts\builder(uniqid());
		$builder->setSuperglobals($superglobals);

		$this
			->exception(function() use ($builder) {
					$builder->getPhpPath();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
		;

		$builder->setPhpPath($php = uniqid());

		$this->string($builder->getPhpPath())->isEqualTo($php);
	}

	public function testSetPhp()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setPhpPath($php = uniqid()))->isIdenticalTo($builder)
				->string($builder->getPhpPath())->isIdenticalTo($php)
				->object($builder->setPhpPath($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getPhpPath())->isIdenticalTo((string) $php)
		;
	}

	public function testSetReportTitle()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setReportTitle($reportTitle = uniqid()))->isIdenticalTo($builder)
				->string($builder->getReportTitle())->isEqualTo($reportTitle)
				->object($builder->setReportTitle($reportTitle = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getReportTitle())->isEqualTo((string) $reportTitle)
		;
	}

	public function testSetVcs()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->and($vcsController = new mock\controller())
			->and($vcsController->__construct = function() {})
			->then
				->object($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController)))->isIdenticalTo($builder)
				->object($builder->getVcs())->isIdenticalTo($vcs)
		;
	}

	public function testSetTaggerEngine()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setTaggerEngine($taggerEngine = new atoum\scripts\tagger\engine()))->isIdenticalTo($builder)
				->object($builder->getTaggerEngine())->isIdenticalTo($taggerEngine)
		;
	}

	public function testSetUnitTestRunnerScript()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setUnitTestRunnerScript($php = uniqid()))->isIdenticalTo($builder)
				->string($builder->getUnitTestRunnerScript())->isIdenticalTo((string) $php)
				->object($builder->setUnitTestRunnerScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getUnitTestRunnerScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetPharGeneratorScript()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setPharGeneratorScript($php = uniqid()))->isIdenticalTo($builder)
				->string($builder->getPharGeneratorScript())->isIdenticalTo($php)
				->object($builder->setPharGeneratorScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getPharGeneratorScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetScoreDirectory()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setScoreDirectory($scoreDirectory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($scoreDirectory)
				->object($builder->setScoreDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($directory)
				->object($builder->setScoreDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($directory)
		;
	}

	public function testSetErrorsDirectory()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setErrorsDirectory($errorsDirectory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($errorsDirectory)
				->object($builder->setErrorsDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($directory)
				->object($builder->setErrorsDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
				->object($builder->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
				->object($builder->setDestinationDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetWorkingDirectory()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setWorkingDirectory($directory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo($directory)
				->object($builder->setWorkingDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo((string) $directory)
				->object($builder->setWorkingDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo($directory)
		;
	}

	public function testSetRevisionFile()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setRevisionFile($file = uniqid()))->isIdenticalTo($builder)
				->string($builder->getRevisionFile())->isEqualTo($file)
		;
	}

	public function testAddRunnerConfigurationFile()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->addRunnerConfigurationFile($file = uniqid()))->isIdenticalTo($builder)
				->array($builder->getRunnerConfigurationFiles())->isEqualTo(array($file))
		;
	}

	public function testSetRunFile()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->object($builder->setRunFile($runFile = uniqid()))->isIdenticalTo($builder)
				->string($builder->getRunFile())->isEqualTo($runFile)
		;
	}

	public function testDisableUnitTestChecking()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
				->object($builder->disableUnitTestChecking())->isIdenticalTo($builder)
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
		;
	}

	public function testEnableUnitTestChecking()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->and($builder->disableUnitTestChecking())
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
				->object($builder->enableUnitTestChecking())->isIdenticalTo($builder)
				->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
		;
	}

	public function testCheckUnitTests()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mock\mageekguy\atoum\scripts\builder']['adapter'] = $adapter = new atoum\test\adapter())
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $depedencies))
			->and($builder->disableUnitTestChecking())
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
				->boolean($builder->checkUnitTests())->isTrue()
			->if($builder->enableUnitTestChecking())
			->then
				->exception(function() use ($builder) {
						$builder->checkUnitTests();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to check unit tests, working directory is undefined')
			->if($builder->setWorkingDirectory($workingDirectory = uniqid()))
			->and($vcsController = new mock\controller())
			->and($vcsController->__construct = function() {})
			->and($vcsController->exportRepository = function() {})
			->and($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController)))
			->and($builder
				->setUnitTestRunnerScript($unitTestRunnerScript = uniqid())
				->setPhpPath($php = uniqid())
				->setReportTitle($reportTitle = uniqid())
				->addRunnerConfigurationFile($runnerConfigurationFile = uniqid())
			)
			->and($score = new \mock\mageekguy\atoum\score())
			->and($scoreController = $score->getMockController())
			->and($scoreController->getFailNumber = 0)
			->and($scoreController->getExceptionNumber = 0)
			->and($scoreController->getErrorNumber = 0)
			->and($adapter->sys_get_temp_dir = $tempDirectory = uniqid())
			->and($adapter->tempnam = $scoreFile = uniqid())
			->and($adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); })
			->and($adapter->proc_get_status = array('exit_code' => 0, 'running' => true))
			->and($adapter->stream_get_contents = function() { return ''; })
			->and($adapter->fclose = function() {})
			->and($adapter->proc_close = function() {})
			->and($adapter->file_get_contents = $scoreFileContents = uniqid())
			->and($adapter->unserialize = $score)
			->and($adapter->unlink = true)
			->and($command = escapeshellarg($php) . ' ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $unitTestRunnerScript) . ' -drt ' . escapeshellarg($reportTitle) . ' -ncc -sf ' . escapeshellarg($scoreFile) . ' -d ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes') . ' -p ' . escapeshellarg($php) . ' -c ' . escapeshellarg($runnerConfigurationFile))
			->and($builderController = $builder->getMockController())
			->and($builderController->writeErrorInErrorsDirectory = function() {})
			->then
				->boolean($builder->checkUnitTests())->isTrue()
				->mock($vcs)
					->call('setWorkingDirectory')
						->withArguments($workingDirectory)
						->once()
				->mock($vcs)
					->call('exportRepository')->once()
				->adapter($adapter)
					->call('sys_get_temp_dir')->once()
					->call('tempnam')->withArguments($tempDirectory, '')->once()
					->call('proc_open')->withArguments($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes)->once()
					->call('proc_get_status')->withArguments($resource)->once()
					->call('stream_get_contents')->withArguments($stdOut)->once()
					->call('fclose')->withArguments($stdOut)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('file_get_contents')->withArguments($scoreFile)->once()
					->call('unserialize')->withArguments($scoreFileContents)->once()
					->call('unlink')->withArguments($scoreFile)->once()
				->mock($score)
					->call('getFailNumber')->once()
					->call('getExceptionNumber')->once()
					->call('getErrorNumber')->once()
			->if($adapter->proc_open = false)
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Unable to execute \'' . $command . '\'')
					->once()
			->if($adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); })
			->and($adapter->proc_get_status = array('exitcode' => 126, 'running' => false))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Unable to find \'' . $php . '\' or it is not executable')
						->once()
			->if($adapter->proc_get_status = array('exitcode' => 127, 'running' => false))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Unable to find \'' . $php . '\' or it is not executable')
						->exactly(2)
			->if($adapter->proc_get_status = array('exitcode' => $exitCode = rand(1, 125), 'running' => false))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
						->once()
			->if($adapter->proc_get_status = array('exitcode' => $exitCode = rand(128, PHP_INT_MAX), 'running' => false))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
						->once()
			->if($adapter->proc_get_status = array('exit_code' => 0, 'running' => true))
			->and($adapter->stream_get_contents = function($stream) use (& $stdOut, & $stdOutContents) { return $stream != $stdOut ? '' : $stdOutContents = uniqid(); })
			->then
				->boolean($builder->checkUnitTests())->isTrue()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments($stdOutContents)
						->never()
			->if($adapter->stream_get_contents = function($stream) use (& $stdErr, & $stdErrContents) { return $stream != $stdErr ? '' : $stdErrContents = uniqid(); })
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments($stdErrContents)
						->once()
			->if($adapter->stream_get_contents = '')
			->and($adapter->file_get_contents = false)
			->and($builder->getMockController()->resetCalls())
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Unable to read score from file \'' . $scoreFile . '\'')
						->once()
			->if($adapter->file_get_contents = $scoreFileContents)
			->and($adapter->unserialize = false)
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Unable to unserialize score from file \'' . $scoreFile . '\'')
						->once()
			->if($adapter->unserialize = uniqid())
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Contents of file \'' . $scoreFile . '\' is not a score')
						->once()
			->if($adapter->unserialize = $score)
			->and($adapter->unlink = false)
			->then
				->exception(function() use ($builder) {
						$builder->checkUnitTests();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to delete score file \'' . $scoreFile . '\'')
			->if($adapter->unlink = true)
			->and($scoreController->getFailNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
			->if($scoreController->getFailNumber = 0)
			->and($scoreController->getExceptionNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
			->if($scoreController->getExceptionNumber = 0)
			->and($scoreController->getErrorNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
		;
	}

	public function testDisablePharCreation()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->then
				->boolean($builder->pharCreationIsEnabled())->isTrue()
				->object($builder->disablePharCreation())->isIdenticalTo($builder)
				->boolean($builder->pharCreationIsEnabled())->isFalse()
		;
	}

	public function testEnablePharCreation()
	{
		$this
			->if($builder = new scripts\builder(uniqid()))
			->and($builder->disablePharCreation())
			->then
				->boolean($builder->pharCreationIsEnabled())->isFalse()
				->object($builder->enablePharCreation())->isIdenticalTo($builder)
				->boolean($builder->pharCreationIsEnabled())->isTrue()
		;
	}

	public function testCreatePhar()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mock\mageekguy\atoum\scripts\builder']['adapter'] = $adapter = new atoum\test\adapter())
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $depedencies))
			->and($builder
				->setTaggerEngine($taggerEngine = new \mock\mageekguy\atoum\scripts\tagger\engine())
				->disablePharCreation()
			)
			->and($taggerEngine->getMockController()->tagVersion = function() {})
			->then
				->boolean($builder->createPhar())->isTrue()
			->if($builder->enablePharCreation())
			->and($vcsController = new mock\controller())
			->and($vcsController->__construct = function() {})
			->and($vcsController->getNextRevisions = array())
			->and($vcsController->exportRepository = function() {})
			->and($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController)))
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to create phar, destination directory is undefined')
			->if($builder->setDestinationDirectory($destinationDirectory = uniqid()))
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to create phar, working directory is undefined')
			->if($builder
				->setWorkingDirectory($workingDirectory = uniqid())
				->setPhpPath($php = uniqid())
				->setPharGeneratorScript($pharGeneratorScript = uniqid())
			)
			->and($builderController = $builder->getMockController())
			->and($builderController->writeErrorInErrorsDirectory = function() {})
			->and($adapter->file_get_contents = false)
			->then
				->boolean($builder->createPhar())->isTrue()
			->if($vcsController->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($builder->disableUnitTestChecking())
			->and($adapter->proc_open = false)
			->then
				->boolean($builder->createPhar())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments('Unable to execute \'' . escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory) . '\'')
						->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdErr, & $pipes, & $resource) { $pipes = array(2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); })
			->and($adapter->stream_get_contents = function() { return ''; })
			->and($adapter->fclose = function() {})
			->and($adapter->proc_close = function() {})
			->and($adapter->date = $date = uniqid())
			->and($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar())->isTrue()
				->mock($taggerEngine)
					->call('setVersion')
						->withArguments('nightly-' . $revision . '-' . $date)
						->once()
					->call('tagVersion')->atLeastOnce()
				->adapter($adapter)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('date')->withArguments('YmdHi')->atLeastOnce()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($adapter->resetCalls())
			->and($builder->getMockController()->resetCalls())
			->and($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar($tag = uniqid()))->isTrue()
				->mock($taggerEngine)
					->call('setVersion')->withArguments($tag)->once()
					->call('tagVersion')->exactly(3)
				->adapter($adapter)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('date')->never()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($adapter->resetCalls())
			->and($builder->getMockController()->resetCalls())
			->and($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($adapter->stream_get_contents = function() use (& $stdErrContents) { return $stdErrContents = uniqid(); })
			->then
				->boolean($builder->createPhar())->isFalse()
				->adapter($adapter)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')
						->withArguments($stdErrContents)
						->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($builder->setRevisionFile($revisionFile = uniqid()))
			->and($adapter->stream_get_contents = function() { return ''; })
			->and($adapter->file_get_contents = false)
			->and($adapter->file_put_contents = function() {})
			->and($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->once()
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($adapter->file_get_contents = false)
			->and($adapter->file_put_contents = function() {})
			->and($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->exactly(2)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('file_put_contents')->withArguments($revisionFile, $revision, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($adapter->file_put_contents = false)
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to save last revision in file \'' . $revisionFile . '\'')
			->if($vcsController->resetCalls())
			->and($vcsController->getNextRevisions[1] = array(1, 2, 3))
			->and($vcsController->getNextRevisions[2] = array(2, 3))
			->and($vcsController->getNextRevisions[3] = array(3))
			->and($vcsController->getNextRevisions[4] = array())
			->and($adapter->file_put_contents = function() {})
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->exactly(4)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->exactly(3)
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('file_put_contents')->withArguments($revisionFile, 3, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments(1)->once()
					->call('setRevision')->withArguments(2)->once()
					->call('setRevision')->withArguments(3)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->atLeastOnce()
					->call('exportRepository')->atLeastOnce()
			->if($vcsController->resetCalls())
			->and($vcsController->getNextRevisions[1] = array(4))
			->and($vcsController->getNextRevisions[2] = array())
			->and($adapter->file_get_contents = 1)
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->exactly(5)
					->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
					->call('stream_get_contents')->withArguments($stdErr)->once()
					->call('fclose')->withArguments($stdErr)->once()
					->call('proc_close')->withArguments($resource)->once()
					->call('file_put_contents')->withArguments($revisionFile, 4, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments(4)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
		;
	}

	public function testRun()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->file_get_contents = false)
			->and($adapter->fopen = $runFileResource = uniqid())
			->and($adapter->flock = true)
			->and($adapter->getmypid = $pid = uniqid())
			->and($adapter->fwrite = function() {})
			->and($adapter->fclose = function() {})
			->and($adapter->unlink = function() {})
			->and($depedencies = new atoum\depedencies())
			->and($depedencies['mock\mageekguy\atoum\scripts\builder']['adapter'] = $adapter)
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $depedencies))
			->and($builderController = $builder->getMockController())
			->and($builderController->createPhar = function() {})
			->and($builder->setRunFile($runFile = uniqid()))
			->then
				->object($builder->run())->isIdenticalTo($builder)
				->mock($builder)->call('createPhar')->once()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($runFile)->once()
					->call('fopen')->withArguments($runFile, 'w+')->once()
					->call('flock')->withArguments($runFileResource, \LOCK_EX | \LOCK_NB)->once()
					->call('fwrite')->withArguments($runFileResource, $pid)->once()
					->call('fclose')->withArguments($runFileResource)->once()
					->call('unlink')->withArguments($runFile)->once()
		;
	}

	public function testWriteInErrorDirectory()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->and($depedencies['mageekguy\atoum\scripts\builder']['adapter'] = $adapter = new atoum\test\adapter())
			->and($adapter->file_put_contents = function() {})
			->and($builder = new scripts\builder(uniqid(), $depedencies))
			->then
				->variable($builder->getErrorsDirectory())->isNull()
				->object($builder->writeErrorInErrorsDirectory(uniqid()))->isIdenticalTo($builder)
				->adapter($adapter)->call('file_put_contents')->never()
			->if($builder->setErrorsDirectory($errorDirectory = uniqid()))
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->exception(function() use ($builder) {
							$builder->writeErrorInErrorsDirectory(uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Revision is undefined')
				->adapter($adapter)->call('file_put_contents')->never()
			->if($vcsController = new mock\controller())
			->and($vcsController->__construct = function() {})
			->and($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs()))
			->and($vcs->setRevision($revision = rand(1, PHP_INT_MAX)))
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->object($builder->writeErrorInErrorsDirectory($message = uniqid()))->isIdenticalTo($builder)
				->adapter($adapter)->call('file_put_contents')->withArguments($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND)->once()
			->if($adapter->resetCalls()->file_put_contents = false)
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->exception(function() use ($builder, & $message) {
							$builder->writeErrorInErrorsDirectory($message = uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Unable to save error in file \'' . $errorDirectory . \DIRECTORY_SEPARATOR . $revision . '\'')
				->adapter($adapter)->call('file_put_contents')->withArguments($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND)->once()
		;
	}
}

?>
