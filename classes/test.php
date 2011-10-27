<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

/**
 * @property    mageekguy\atoum\asserter\generator  assert
 * @property    mageekguy\atoum\asserter\generator  define
 * @property    mageekguy\atoum\mock\generator      mockGenerator
 *
 * @method      mageekguy\atoum\asserter\generator  assert()
 * @method      mageekguy\atoum\test                mock()
 */
abstract class test implements observable, adapter\aggregator, \countable
{
	const testMethodPrefix = 'test';
	const runStart = 'testRunStart';
	const beforeSetUp = 'beforeTestSetUp';
	const afterSetUp = 'afterTestSetUp';
	const beforeTestMethod = 'beforeTestMethod';
	const fail = 'testAssertionFail';
	const error = 'testError';
	const exception = 'testException';
	const success = 'testAssertionSuccess';
	const afterTestMethod = 'afterTestMethod';
	const beforeTearDown = 'beforeTestTearDown';
	const afterTearDown = 'afterTestTearDown';
	const runStop = 'testRunStop';
	const defaultNamespace = 'tests\units';

    /**
     * Path to php binary
     *
     * @var string
     */
	private $phpPath = null;

    /**
     * Current test file path
     *
     * @var string
     */
	private $path = '';

    /**
     * Current test class name
     *
     * @var string
     */
	private $class = '';

    /**
     * @var string
     */
	private $classNamespace = '';

    /**
     * @var mageekguy\atoum\adapter
     */
	private $adapter = null;

    /**
     * @var mageekguy\atoum\asserter\generator
     */
	private $asserterGenerator = null;

    /**
     * @var mageekguy\atoum\score
     */
	private $score = null;

    /**
     * @var array
     */
	private $observers = array();

    /**
     * @var array
     */
	private $tags = array();

    /**
     * @var boolean
     */
	private $ignore = false;

    /**
     * @var array
     */
	private $testMethods = array();

    /**
     * @var array
     */
	private $runTestMethods = array();

    /**
     * @var string
     */
	private $currentMethod = null;

    /**
     * @var string
     */
	private $testNamespace = null;

    /**
     * @var mageekguy\atoum\mock\generator
     */
	private $mockGenerator = null;

    /**
     * @var integer
     */
	private $testsToRun = 0;

    /**
     * @var integer
     */
	private $size = 0;

    /**
     * @var string
     */
	private $phpCode = '';

    /**
     * @var array
     */
	private $children = array();

    /**
     * @var integer
     */
	private $maxChildrenNumber = null;

    /**
     * Whether or not to generate code coverage
     *
     * @var boolean
     */
	private $codeCoverage = false;

    /**
     * @var boolean
     */
	private $assertHasCase = false;


    /**
     * @var mageekguy\atoum\superglobals
     */
    public $superglobals = null;

    /**
     * @var mageekguy\atoum\locale
     */
    public $locale = null;

    /**
     * Current test namespace
     *
     * @var string
     */
	private static $namespace = null;

    /**
     * Constructor
     *
     * @param mageekguy\atoum\score         $score
     * @param mageekguy\atoum\locale        $locale
     * @param mageekguy\atoum\adapter       $adapter
     * @param mageekguy\atoum\superglobals  $superglobals
     *
     * @throws mageekguy\atoum\exceptions\runtime
     */
	public function __construct(score $score = null, locale $locale = null, adapter $adapter = null, superglobals $superglobals = null)
	{
		$this
			->setScore($score ?: new score())
			->setLocale($locale ?: new locale())
			->setAdapter($adapter ?: new adapter())
			->setSuperglobals($superglobals ?: new superglobals())
			->enableCodeCoverage()
		;

		$class = new \reflectionClass($this);

		$this->path = $class->getFilename();
		$this->class = $class->getName();
		$this->classNamespace = $class->getNamespaceName();

		$testedClassName = $this->getTestedClassName();

		if ($testedClassName === null)
		{
			throw new exceptions\runtime('Test class \'' . $this->getClass() . '\' is not in a namespace which contains \'' . $this->getTestNamespace() . '\'');
		}

		if ($this->adapter->class_exists($testedClassName) === false)
		{
			throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
		}

		$this->getAsserterGenerator()
			->setAlias('array', 'phpArray')
			->setAlias('class', 'phpClass')
		;

		foreach (new annotations\extractor($class->getDocComment()) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$this->ignore = $value == 'on';
					break;

				case 'tags':
					$this->tags = array_values(array_unique(preg_split('/\s+/', $value)));
					break;
			}
		}

		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod)
		{
			$methodName = $publicMethod->getName();

			if (strpos($methodName, self::testMethodPrefix) === 0)
			{
				$annotations = array();

				foreach (new annotations\extractor($publicMethod->getDocComment()) as $annotation => $value)
				{
					switch ($annotation)
					{
						case 'ignore':
							$annotations['ignore'] = $value == 'on';
							break;

						case 'tags':
							$annotations['tags'] = array_values(array_unique(preg_split('/\s+/', $value)));
							break;
					}
				}

				$this->testMethods[$methodName] = $annotations;
			}
		}

		$this->runTestMethods($this->getTestMethods());
	}


    /**
     * @return string
     */
	public function __toString()
	{
		return $this->getClass();
	}


    /**
     * Magic getter
     *
     * @param string $property
     *
     * @return mageekguy\atoum\asserter\generator|mageekguy\atoum\mock\generator
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __get($property)
	{
		switch ($property)
		{
			case 'define':
				return $this->getAsserterGenerator();

			case 'assert':
				return $this->unsetCaseOnAssert()->getAsserterGenerator();

			case 'mockGenerator':
				return $this->getMockGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Property \'' . $property . '\' is undefined in class \'' . get_class($this) . '\'');
		}
	}


    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mageekguy\atoum\test|mageekguy\atoum\asserter\generator
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'mock':
				$this->getMockGenerator()->generate(isset($arguments[0]) === false ? null : $arguments[0], isset($arguments[1]) === false ? null : $arguments[1], isset($arguments[2]) === false ? null : $arguments[2]);
				return $this;

			case 'assert':
				$this->unsetCaseOnAssert();

				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->setCaseOnAssert($case);
				}

				return $this->getAsserterGenerator();

			default:
				throw new exceptions\logic\invalidArgument('Method ' . get_class($this) . '::' . $method . '() is undefined');
		}
	}


    /**
     * @return boolean
     */
	public function codeCoverageIsEnabled()
	{
		return $this->codeCoverage;
	}


    /**
     * @return mageekguy\atoum\test
     */
	public function enableCodeCoverage()
	{
		$this->codeCoverage = $this->adapter->extension_loaded('xdebug');

		return $this;
	}


    /**
     * @return mageekguy\atoum\test
     */
	public function disableCodeCoverage()
	{
		$this->codeCoverage = false;

		return $this;
	}


    /**
     * @param integer $number
     *
     * @return mageekguy\atoum\test
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function setMaxChildrenNumber($number)
	{
		if ($number < 1)
		{
			throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
		}

		$this->maxChildrenNumber = $number;

		return $this;
	}


    /**
     * @param mageekguy\atoum\superglobals $superglobals
     *
     * @return mageekguy\atoum\test
     */
	public function setSuperglobals(atoum\superglobals $superglobals)
	{
		$this->superglobals = $superglobals;

		return $this;
	}


    /**
     * @return mageekguy\atoum\superglobals
     */
	public function getSuperglobals()
	{
		return $this->superglobals;
	}


    /**
     * @param mageekguy\atoum\mock\generator $generator
     *
     * @return mageekguy\atoum\test
     */
	public function setMockGenerator(mock\generator $generator)
	{
		$this->mockGenerator = $generator;

		return $this;
	}


    /**
     * @return mageekguy\atoum\mock\generator
     */
	public function getMockGenerator()
	{
		return $this->mockGenerator ?: $this->setMockGenerator(new mock\generator())->mockGenerator;
	}


    /**
     * @param mageekguy\atoum\asserter\generator $generator
     *
     * @return mageekguy\atoum\test
     */
	public function setAsserterGenerator(asserter\generator $generator)
	{
		$this->asserterGenerator = $generator->setTest($this);

		return $this;
	}


    /**
     * @return mageekguy\atoum\asserter\generator
     */
	public function getAsserterGenerator()
	{
		atoum\test\adapter::resetCallsForAllInstances();

		return $this->asserterGenerator ?: $this->setAsserterGenerator(new asserter\generator($this, $this->locale))->asserterGenerator;
	}


    /**
     * @param string $testNamespace
     *
     * @return mageekguy\atoum\test
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function setTestNamespace($testNamespace)
	{
		$this->testNamespace = self::cleanNamespace($testNamespace);

		if ($this->testNamespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Test namespace must not be empty');
		}

		return $this;
	}


    /**
     * @return string
     */
	public function getTestNamespace()
	{
		return $this->testNamespace ?: self::getNamespace();
	}


    /**
     * @param string$path
     *
     * @return mageekguy\atoum\test
     */
	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}


    /**
     * @return string
     *
     * @throws mageekguy\atoum\exceptions\runtime
     */
	public function getPhpPath()
	{
		if ($this->phpPath === null)
		{
			if (isset($this->superglobals->_SERVER['_']) === false)
			{
				throw new exceptions\runtime('Unable to find PHP executable');
			}

			$this->setPhpPath($this->superglobals->_SERVER['_']);
		}

		return $this->phpPath;
	}


    /**
     * @return array
     */
	public function getTags()
	{
		$tags = $this->getClassTags();

		foreach ($this->getMethodTags() as $methodTags)
		{
			$tags = array_merge($tags, $methodTags);
		}

		return array_values(array_unique($tags));
	}


    /**
     * @return array
     */
	public function getClassTags()
	{
		return $this->tags;
	}


    /**
     * @param string $testMethodName
     *
     * @return array
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidargument
     */
	public function getMethodTags($testMethodName = null)
	{
		$tags = array();

		$classTags = $this->getClassTags();

		if ($testMethodName === null)
		{
			foreach ($this->testMethods as $testMethodName => $annotations)
			{
				$tags[$testMethodName] = isset($annotations['tags']) === false ? $classTags : $annotations['tags'];
			}
		}
		else
		{
			if (isset($this->testMethods[$testMethodName]) === false)
			{
				throw new exceptions\logic\invalidargument('test method ' . $this->class . '::' . $testMethodName . '() is unknown');
			}

			$tags = isset($this->testMethods[$testMethodName]['tags']) === false ? $classTags : $this->testMethods[$testMethodName]['tags'];
		}

		return $tags;
	}


    /**
     * @param mageekguy\atoum\adapter $adapter
     *
     * @return mageekguy\atoum\test
     */
	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}


    /**
     * @return mageekguy\atoum\adapter
     */
	public function getAdapter()
	{
		return $this->adapter;
	}


    /**
     * @param mageekguy\atoum\score $score
     *
     * @return mageekguy\atoum\test
     */
	public function setScore(score $score)
	{
		$this->score = $score;

		return $this;
	}


    /**
     * @return mageekguy\atoum\score
     */
	public function getScore()
	{
		return $this->score;
	}


    /**
     * @param mageekguy\atoum\locale $locale
     *
     * @return mageekguy\atoum\test
     */
	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}


    /**
     * @return mageekguy\atoum\locale
     */
	public function getLocale()
	{
		return $this->locale;
	}


    /**
     * @return string
     */
	public function getTestedClassName()
	{
		$class = null;

		$testClass = $this->getClass();
		$testNamespace = $this->getTestNamespace();

		$position = strpos($testClass, $testNamespace);

		if ($position !== false)
		{
			$class = trim(substr($testClass, 0, $position) . substr($testClass, $position + strlen($testNamespace) + 1), '\\');
		}

		return $class;
	}


    /**
     * @return string
     */
	public function getClass()
	{
		return $this->class;
	}

	public function getClassNamespace()
	{
		return $this->classNamespace;
	}

    /**
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}

	public function filterTestMethods(array $methods, array $tags = array())
	{
		return array_values(array_uintersect($methods, $this->getTestMethods($tags), 'strcasecmp'));
	}

    /**
     * @param array $tags
     *
     * @return array
     */
	public function getTestMethods(array $tags = array())
	{
		$testMethods = array();

		foreach (array_keys($this->testMethods) as $methodName)
		{
			if ($this->methodIsIgnored($methodName, $tags) === false)
			{
				$testMethods[] = $methodName;
			}
		}

		return $testMethods;
	}


    /**
     * @return string
     */
	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}


    /**
     * @return integer
     */
	public function count()
	{
		return $this->size;
	}


    /**
     * @param mageekguy\atoum\observers\test $observer
     *
     * @return mageekguy\atoum\test
     */
	public function addObserver(atoum\observers\test $observer)
	{
		$this->observers[] = $observer;

		return $this;
	}


    /**
     * @param string $method
     *
     * @return mageekguy\atoum\test
     */
	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}


    /**
     * @param boolean $boolean
     *
     * @return mageekguy\atoum\test
     */
	public function ignore($boolean)
	{
		$this->ignore = ($boolean == true);

		$this->runTestMethods($this->getTestMethods());

		return $this;
	}


    /**
     * @return boolean
     */
	public function isIgnored()
	{
		return ($this->ignore === true);
	}


    /**
     * @param string $methodName
     * @param array  $tags
     *
     * @return boolean
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function methodIsIgnored($methodName, array $tags = array())
	{
		if (isset($this->testMethods[$methodName]) === false)
		{
			throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() is unknown');
		}

		$isIgnored = (isset($this->testMethods[$methodName]['ignore']) === true ? $this->testMethods[$methodName]['ignore'] : $this->ignore);

		if ($isIgnored === false && sizeof($tags) > 0)
		{
			$isIgnored = sizeof($methodTags = $this->getMethodTags($methodName)) <= 0 || sizeof(array_intersect($tags, $methodTags)) <= 0;
		}

		return $isIgnored;
	}


    /**
     * @param string $testMethod
     * @param array  $tags
     *
     * @return mageekguy\atoum\test
     *
     * @throws mageekguy\atoum\exception
     */
	public function runTestMethod($testMethod, array $tags = array())
	{
		if ($this->methodIsIgnored($testMethod, $tags) === false)
		{
			set_error_handler(array($this, 'errorHandler'));

			ini_set('display_errors', 'stderr');
			ini_set('log_errors', 'Off');
			ini_set('log_errors_max_len', '0');

			$this->currentMethod = $testMethod;

			try
			{
				try
				{
					$this->beforeTestMethod($this->currentMethod);

					ob_start();

					if ($this->codeCoverageIsEnabled() === true)
					{
						xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
					}

					$time = microtime(true);
					$memory = memory_get_usage(true);

					$this->{$testMethod}();

					$memoryUsage = memory_get_usage(true) - $memory;
					$duration = microtime(true) - $time;

					if ($this->codeCoverageIsEnabled() === true)
					{
						$this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
						xdebug_stop_code_coverage();
					}

					$this->score
						->addMemoryUsage($this->class, $this->currentMethod, $memoryUsage)
						->addDuration($this->class, $this->currentMethod, $duration)
						->addOutput($this->class, $this->currentMethod, ob_get_clean())
					;

					$this->afterTestMethod($testMethod);

				}
				catch (\exception $exception)
				{
					$this->score->addOutput($this->class, $this->currentMethod, ob_get_clean());

					throw $exception;
				}
			}
			catch (asserter\exception $exception)
			{
				if ($this->score->failExists($exception) === false)
				{
					$this->addExceptionToScore($exception);
				}
			}
			catch (\exception $exception)
			{
				$this->addExceptionToScore($exception);
			}

			$this->currentMethod = null;

			restore_error_handler();

			ini_restore('display_errors');
			ini_restore('log_errors');
			ini_restore('log_errors_max_len');
		}

		return $this;
	}


    /**
     * @param array $runTestMethods
     * @param array $tags
     *
     * @return mageekguy\atoum\test
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     * @throws mageekguy\atoum\exception
     * @throws mageekguy\atoum\exceptions\runtime
     */
	public function run(array $runTestMethods = array(), array $tags = array())
	{
		if ($this->isIgnored() === false)
		{
			if (sizeof($runTestMethods) > 0)
			{
				$this->runTestMethods(array_intersect($runTestMethods, $this->getTestMethods($tags)));
			}

			$this->callObservers(self::runStart);

			if (sizeof($this) > 0)
			{
				$this->phpCode =
					'<?php ' .
					'define(\'' . __NAMESPACE__ . '\autorun\', false);' .
					'require \'' . $this->path . '\';' .
					'$test = new ' . $this->class . '();' .
					'$test->setLocale(new ' . get_class($this->locale) . '(' . $this->locale->get() . '));' .
					'$test->setPhpPath(\'' . $this->getPhpPath() . '\');' .
					($this->codeCoverageIsEnabled() === true ? '' : '$test->disableCodeCoverage();') .
					'$test->runTestMethod($method = \'%s\');' .
					'echo serialize($test->getScore());' .
					'?>'
				;

				$null = null;

				try
				{
					$this->callObservers(self::beforeSetUp);
					$this->setUp();
					$this->callObservers(self::afterSetUp);

					while (sizeof($this->runChild()->children) > 0)
					{
						$pipes = array();

						foreach ($this->children as $child)
						{
							if (isset($child[1][1]) === true)
							{
								$pipes[] = $child[1][1];
							}

							if (isset($child[1][2]) === true)
							{
								$pipes[] = $child[1][2];
							}
						}

						$pipesUpdated = stream_select($pipes, $null, $null, $this->canRunChild() === true ? 0 : null);

						if ($pipesUpdated !== false && $pipesUpdated > 0)
						{
							$children = $this->children;
							$this->children = array();

							foreach ($children as $this->currentMethod => $child)
							{
								if (isset($child[1][2]) && in_array($child[1][2], $pipes) === true)
								{
									$child[3] .= stream_get_contents($child[1][2]);

									if (feof($child[1][2]) === true)
									{
										fclose($child[1][2]);
										unset($child[1][2]);
									}
								}

								if (isset($child[1][1]) && in_array($child[1][1], $pipes) === true)
								{
									$child[2] .= stream_get_contents($child[1][1]);

									if (feof($child[1][1]) === true)
									{
										fclose($child[1][1]);
										unset($child[1][1]);
									}
								}

								if (isset($child[1][1]) === true || isset($child[1][2]) === true)
								{
									$this->children[$this->currentMethod] = $child;
								}
								else
								{
									$phpStatus = proc_get_status($child[0]);

									while ($phpStatus['running'] == true)
									{
										$phpStatus = proc_get_status($child[0]);
									}

									proc_close($child[0]);

									switch ($phpStatus['exitcode'])
									{
										case 126:
										case 127:
											throw new exceptions\runtime('Unable to execute test method ' . $this->class . '::' . $this->currentMethod . '()');
									}

									$score = @unserialize($child[2]);

									if ($score instanceof score === false)
									{
										$score = new score();
									}

									if ($child[3] !== '')
									{
										if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($child[3]), $errors, PREG_SET_ORDER) === 0)
										{
											$score->addError($this->path, null, $this->class, $this->currentMethod, 'UNKNOWN', $child[3]);
										}
										else foreach ($errors as $error)
										{
											$score->addError($this->path, null, $this->class, $this->currentMethod, $error[1], $error[2], $error[3], $error[4]);
										}
									}

									$this->callObservers(self::afterTestMethod);

									if ($score->getFailNumber() > 0)
									{
										$this->callObservers(self::fail);
									}

									if ($score->getErrorNumber() > 0)
									{
										$this->callObservers(self::error);
									}
									else if ($score->getExceptionNumber() > 0)
									{
										$this->callObservers(self::exception);
									}

									if ($score->getPassNumber() > 0)
									{
										$this->callObservers(self::success);
									}

									$this->score->merge($score);
								}
							}

							$this->currentMethod = null;
						}
					}

					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;
				}
				catch (\exception $exception)
				{
					$this
						->callObservers(self::beforeTearDown)
						->tearDown()
						->callObservers(self::afterTearDown)
					;

					throw $exception;
				}
			}

			$this->callObservers(self::runStop);
		}

		return $this;
	}


    /**
     * @param integer $errno
     * @param string  $errstr
     * @param string  $errfile
     * @param integer $errline
     * @param array   $context
     *
     * @return boolean
     */
	public function errorHandler($errno, $errstr, $errfile, $errline, $context)
	{
		if (error_reporting() !== 0)
		{
			list($file, $line) = $this->getBacktrace();

			$this->score->addError($file, $line, $this->class, $this->currentMethod, $errno, $errstr, $errfile, $errline);
		}

		return true;
	}


    /**
     * Generate a mock
     *
     * @param string $class
     * @param string $mockNamespace
     * @param string $mockClass
     *
     * @return mageekguy\atoum\test
     */
	public function mock($class, $mockNamespace = null, $mockClass = null)
	{
		$this->getMockGenerator()->generate($class, $mockNamespace, $mockClass);

		return $this;
	}


    /**
     * @deprecated
     *
     * @param string
     */
	public function setTestsSubNamespace($testsSubNamespace)
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::setTestNamespace() instead');
	}


    /**
     * @deprecated
     */
	public function getTestsSubNamespace()
	{
		#DEPRECATED
		die(__METHOD__ . ' is deprecated, please use ' . __CLASS__ . '::getTestNamespace() instead');
	}


    /**
     * @param string $namespace
     *
     * @throws atoum\exceptions\logic\invalidArgument
     */
	public static function setNamespace($namespace)
	{
		self::$namespace = self::cleanNamespace($namespace);

		if (self::$namespace === '')
		{
			throw new atoum\exceptions\logic\invalidArgument('Namespace must not be empty');
		}
	}


    /**
     * @return string
     */
	public static function getNamespace()
	{
		return self::$namespace ?: self::defaultNamespace;
	}


    /**
     * @return array
     */
	public static function getObserverEvents()
	{
		return array(
			self::runStart,
			self::beforeSetUp,
			self::afterSetUp,
			self::beforeTestMethod,
			self::fail,
			self::error,
			self::exception,
			self::success,
			self::afterTestMethod,
			self::beforeTearDown,
			self::afterTearDown,
			self::runStop
		);
	}


    /**
     * @return mageekguy\atoum\test
     */
	protected function setUp()
	{
		return $this;
	}


    /**
     * @param string $case
     *
     * @return mageekguy\atoum\test
     */
	protected function startCase($case)
	{
		$this->score->setCase($case);

		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return mageekguy\atoum\test
     */
	protected function beforeTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @param string $testMethod current test method
     *
     * @return mageekguy\atoum\test
     */
	protected function afterTestMethod($testMethod)
	{
		return $this;
	}


    /**
     * @return mageekguy\atoum\test
     */
	protected function tearDown()
	{
		return $this;
	}


    /**
     * @param \exception $exception
     *
     * @return mageekguy\atoum\test
     */
	protected function addExceptionToScore(\exception $exception)
	{
		list($file, $line) = $this->getBacktrace($exception->getTrace());

		$this->score->addException($file, $line, $this->class, $this->currentMethod, $exception);

		return $this;
	}


    /**
     * @param array $trace
     *
     * @return array|null
     */
	protected function getBacktrace(array $trace = null)
	{
		$debugBacktrace = $trace === null ? debug_backtrace(false) : $trace;

		foreach ($debugBacktrace as $key => $value)
		{
			if (isset($value['class']) === true && isset($value['function']) === true && $value['class'] === $this->class && $value['function'] === $this->currentMethod)
			{
				if (isset($debugBacktrace[$key - 1]) === true)
				{
					$key -= 1;
				}

				return array(
					$debugBacktrace[$key]['file'],
					$debugBacktrace[$key]['line']
				);
			}
		}

		return null;
	}


    /**
     * @param array $methods
     *
     * @return mageekguy\atoum\test
     */
	protected function runTestMethods(array $methods)
	{
		$this->runTestMethods = $methods;
		$this->size = sizeof($this->runTestMethods);

		return $this;
	}


    /**
     * @return mageekguy\atoum\test
     */
	private function runChild()
	{
		if ($this->canRunChild() === true)
		{
			$php = @proc_open(
				escapeshellarg($this->getPhpPath()),
				array(
					0 => array('pipe', 'r'),
					1 => array('pipe', 'w'),
					2 => array('pipe', 'w')
				),
				$pipes
			);

			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);

			$currentMethod = array_shift($this->runTestMethods);

			$this->callObservers(self::beforeTestMethod);

			fwrite($pipes[0], sprintf($this->phpCode, $currentMethod));
			fclose($pipes[0]);
			unset($pipes[0]);

			$this->children[$currentMethod] = array(
				$php,
				$pipes,
				'',
				''
			);
		}

		return $this;
	}


    /**
     * @return boolean
     */
	private function canRunChild()
	{
		return (sizeof($this->runTestMethods) > 0 && ($this->maxChildrenNumber === null || sizeof($this->children) < $this->maxChildrenNumber));
	}


    /**
     * @param string $case
     *
     * @return mageekguy\atoum\test
     */
	private function setCaseOnAssert($case)
	{
		$this->startCase($case)->assertHasCase = true;

		return $this;
	}


    /**
     * @return mageekguy\atoum\test
     */
	private function unsetCaseOnAssert()
	{
		if ($this->assertHasCase === true)
		{
			$this->score->unsetCase();
		}

		return $this;
	}


    /**
     * @param string $namespace
     *
     * @return string
     */
	private static function cleanNamespace($namespace)
	{
		return trim((string) $namespace, '\\');
	}
}

?>
