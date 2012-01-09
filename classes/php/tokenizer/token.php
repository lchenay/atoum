<?php

namespace mageekguy\atoum\php\tokenizer;

class token
{
	protected $value ='';
	protected $name = null;

	public function __construct($value, $name = null)
	{
		$this->name = $name;
		$this->value = (string) $value;
	}

	public function __toString()
	{
		return $this->getValue();
	}

	public function getName()
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}
}

?>
