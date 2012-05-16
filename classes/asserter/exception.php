<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum
;

class exception extends \runtimeException
{
	protected $asserter = null;

	public function __construct(atoum\asserter $asserter, $message, $code = 0, \exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->asserter = $asserter;
	}

	public function getAsserter()
	{
		return $this->asserter;
	}

	public function getFailLine($file)
	{
		return (($trace = $this->getTraceInFile($file)) === null || isset($trace['line']) === false ? null : $trace['line']);
	}

	public function getFailCall($file)
	{
		return (($trace = $this->getTraceInFile($file)) === null || isset($trace['function']) === false ? null : get_class($this->asserter) . '::' . $trace['function'] . '()');
	}

	protected function getTraceInFile($file)
	{
		foreach (array_reverse($this->getTrace()) as $trace)
		{
			if (isset($trace['file']) === true && $trace['file'] === $file)
			{
				return $trace;
			}
		}

		return null;
	}
}

?>
