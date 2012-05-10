<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines
;

class isolate extends engines\concurrent
{
	protected $score = null;

	public function __construct(atoum\depedencies $depedencies = null)
	{
		parent::__construct($depedencies);

		$this->score = $this->depedencies['score']($this->depedencies);
	}

	public function run(atoum\test $test)
	{
		parent::run($test);

		$this->score = parent::getScore();

		while ($this->score === null)
		{
			$this->score = parent::getScore();
		}

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}
}
