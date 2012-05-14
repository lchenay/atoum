<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class cli extends atoum\test
{
	public function testIsTerminalWhenStdoutIsUndefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = false)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyIsUndefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = true)
			->and($adapter->function_exists = false)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyReturnFalse()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = true)
			->and($adapter->function_exists = true)
			->and($adapter->constant = null)
			->and($adapter->posix_isatty = false)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyReturnTrue()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = true)
			->and($adapter->function_exists = true)
			->and($adapter->constant = null)
			->and($adapter->posix_isatty = true)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedAfterFirstCallToConstructor()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = false)
			->and($cli = new atoum\cli($adapter))
			->and($otherCli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
				->boolean($otherCli->isTerminal())->isFalse()
			->if(\mageekguy\atoum\cli::forceTerminal())
			->then
				->boolean($cli->isTerminal())->isTrue()
				->boolean($otherCli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedBeforeFirstCallToConstructor()
	{
		$this
			->if(\mageekguy\atoum\cli::forceTerminal())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->defined = false)
			->and($cli = new atoum\cli($adapter))
			->and($otherCli = new atoum\cli())
			->then
				->boolean($cli->isTerminal())->isTrue()
				->boolean($otherCli->isTerminal())->isTrue()
		;
	}
}

?>
