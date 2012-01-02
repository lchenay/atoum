<?php

namespace mageekguy\atoum\tests\units\cli\progressBars;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\cli\progressBars
;

class beachBall extends atoum\test
{
	public function test__toString()
	{
		$this->assert
			->if($beachBall = new progressBars\beachBall())
			->then
				->castToString($beachBall)->isEqualTo('-')
				->castToString($beachBall)->isEqualTo("\010" . '\\')
				->castToString($beachBall)->isEqualTo("\010" . '|')
				->castToString($beachBall)->isEqualTo("\010" . '/')
				->castToString($beachBall)->isEqualTo("\010" . '-')
				->castToString($beachBall->delete())->isEqualTo("\010" . ' ')
				->castToString($beachBall)->isEqualTo('-')
		;
	}
}

?>
