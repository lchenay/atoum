<?php

namespace mageekguy\atoum\php\tokenizer;

class iterator extends tokens
{
	const type = 'script';

	private $parent = null;

	public function setParent(iterator $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getType()
	{
		return static::type;
	}
}

?>
