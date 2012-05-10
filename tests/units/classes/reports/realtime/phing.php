<?php

namespace mageekguy\atoum\tests\units\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields
;

require_once __DIR__ . '/../../../runner.php';

class phing extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($report = new reports\realtime\phing())
			->then
				->boolean($report->progressIsShowed())->isTrue()
				->boolean($report->codeCoverageIsShowed())->isTrue()
				->boolean($report->missingCodeCoverageIsShowed())->isTrue()
				->boolean($report->durationIsShowed())->isTrue()
				->boolean($report->memoryIsShowed())->isTrue()
				->variable($report->getCodeCoverageReportPath())->isNull()
				->variable($report->getCodeCoverageReportUrl())->isNull()
				->object($report->getDepedencies())->isInstanceOf('mageekguy\atoum\depedencies')
			->if($report = new reports\realtime\phing(false, false, false, false, false, $path = uniqid(), $url = uniqid(), $depedencies = new atoum\depedencies()))
			->then
				->boolean($report->progressIsShowed())->isFalse()
				->boolean($report->codeCoverageIsShowed())->isFalse()
				->boolean($report->missingCodeCoverageIsShowed())->isFalse()
				->boolean($report->durationIsShowed())->isFalse()
				->boolean($report->memoryIsShowed())->isFalse()
				->string($report->getCodeCoverageReportPath())->isEqualTo($path)
				->string($report->getCodeCoverageReportUrl())->isEqualTo($url)
				->object($report->getDepedencies())->isIdenticalTo($depedencies[$report])
		  ;
	}
}

?>
