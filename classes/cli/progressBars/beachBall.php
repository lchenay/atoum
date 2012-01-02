<?php

namespace mageekguy\atoum\cli\progressBars;

class beachBall
{
	protected $string = "\010";

	public function __toString()
	{
		switch ($this->string)
		{
			case "\010":
				return ($string = $this->string = '-');

			case '-':
				return ("\010" . ($this->string = '\\'));

			case '\\':
				return ("\010" . ($this->string = '|'));

			case '|':
				return ("\010" . ($this->string = '/'));

			case '/':
				return ("\010" . ($this->string = '-'));

			default:
				return (($this->string = "\010") . ' ');
		}
	}

	public function delete()
	{
		$this->string = null;

		return $this;
	}
}

?>
