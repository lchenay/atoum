<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters
;

/**
 * @method  mageekguy\atoum\asserters\adapter               adapter()
 * @method  mageekguy\atoum\asserters\afterDestructionOf    afterDestructionOf()
 * @method  mageekguy\atoum\asserters\boolean               boolean()
 * @method  mageekguy\atoum\asserters\castToString          castToString()
 * @method  mageekguy\atoum\asserters\dateTime              dateTime()
 * @method  mageekguy\atoum\asserters\error                 error()
 * @method  mageekguy\atoum\asserters\exception             exception()
 * @method  mageekguy\atoum\asserters\float                 float()
 * @method  mageekguy\atoum\asserters\hash                  hash()
 * @method  mageekguy\atoum\asserters\integer               integer()
 * @method  mageekguy\atoum\asserters\mock                  mock()
 * @method  mageekguy\atoum\asserters\mysqlDateTime         mysqlDateTime()
 * @method  mageekguy\atoum\asserters\object                object()
 * @method  mageekguy\atoum\asserters\output                output()
 * @method  mageekguy\atoum\asserters\phpArray              phpArray()
 * @method  mageekguy\atoum\asserters\phpClass              phpClass()
 * @method  mageekguy\atoum\asserters\sizeOf                sizeOf()
 * @method  mageekguy\atoum\asserters\stream                stream()
 * @method  mageekguy\atoum\asserters\string                string()
 * @method  mageekguy\atoum\asserters\testedClass           testedClass()
 * @method  mageekguy\atoum\asserters\variable              variable()
 */
class castToString extends asserters\string
{
	public function setWith($value, $label = null, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $label, $charlist, false);

		if ($checkType === true)
		{
			if (self::isObject($value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an object'), $this->getTypeOf($value)));
			}
			else
			{
				$this->pass();

				$this->value = (string) $this->value;
			}
		}

		return $this;
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
}

?>
