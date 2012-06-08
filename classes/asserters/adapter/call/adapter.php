<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum\test,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class adapter
{
    /**
     * @var \mageekguy\atoum\asserters\adapter|null
     */
	protected $adapterAsserter = null;

    /**
     * @var \mageekguy\atoum\test\adapter|null
     */
	protected $adapter = null;

    /**
     * @var string
     */
	protected $functionName = '';

    /**
     * @var array
     */
	protected $arguments = null;

	public function __construct(asserters\adapter $adapterAsserter, test\adapter $adapter, $functionName)
	{
		$this->adapterAsserter = $adapterAsserter;
		$this->adapter = $adapter;
		$this->functionName = (string) $functionName;
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this->adapterAsserter, $method) === false)
		{
			throw new exceptions\logic\invalidArgument('Method ' . get_class($this->adapterAsserter) . '::' . $method . '() does not exist');
		}

		return call_user_func_array(array($this->adapterAsserter, $method), $arguments);
	}

    /**
     * @return \mageekguy\atoum\asserters\adapter|null
     */
	public function getMockAsserter()
	{
		return $this->adapterAsserter;
	}

    /**
     * @return \mageekguy\atoum\test\adapter|null
     */
	public function getAdapter()
	{
		return $this->adapter;
	}

    /**
     * @return string
     */
	public function getFunctionName()
	{
		return $this->functionName;
	}

    /**
     * @return $this
     */
	public function withArguments()
	{
		$this->arguments = func_get_args();

		return $this;
	}

    /**
     * @return array
     */
	public function getArguments()
	{
		return $this->arguments;
	}

    /**
     * @return mixed|null
     */
	public function getFirstCall()
	{
		$calls = $this->adapter->getCalls($this->functionName, $this->arguments);

		return $calls === null ? null : key($calls);
	}

    /**
     * @return mixed|null
     */
	public function getLastCall()
	{
		$calls = $this->adapter->getCalls($this->functionName, $this->arguments);

		return $calls === null ? null : key(array_reverse($calls, true));
	}
}

?>
