<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum
;

class exception extends \runtimeException
{
	protected $asserter = '';
	protected $failFile = '';
	protected $failLine = 0;
	protected $failClass = '';
	protected $failMethod = '';

	public function __construct(atoum\asserter $asserter, $message, $code = 0, \exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$traces = array_reverse($this->getTrace());

		foreach ($traces as $level => $trace)
		{
			if (isset($trace['class']) === true && $asserter instanceof $trace['class'])
			{
				$this->asserter = $trace['class'] . '::' . $trace['function'] . '()';
				$this->failFile = $trace['file'];
				$this->failLine = $trace['line'];
				$this->failClass = $traces[$level - 1]['class'];
				$this->failMethod = $traces[$level - 1]['function'];

				break;
			}
		}
	}

	public function getAsserter()
	{
		return $this->asserter;
	}

	public function getFailFile()
	{
		return $this->failFile;
	}

	public function getFailLine()
	{
		return $this->failLine;
	}

	public function getFailClass()
	{
		return $this->failClass;
	}

	public function getFailMethod()
	{
		return $this->failMethod;
	}
}

?>
