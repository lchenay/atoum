<?php

namespace mageekguy\atoum\annotations;

class extractor
{
    /**
     * @var array
     */
	protected $handlers = array();

    /**
     * @param string $comments
     * @return $this
     */
	public function extract($comments)
	{
		$comments = trim((string) $comments);

		if (substr($comments, 0, 3) == '/**' && substr($comments, -2) == '*/')
		{
			foreach (explode("\n", trim(trim($comments, '/*'))) as $comment)
			{
				$comment = trim(trim(trim($comment), '*/'));

				if (substr($comment, 0, 1) == '@')
				{
					$comment = preg_split("/\s+/", $comment);

					$sizeofComment = sizeof($comment);

					if ($sizeofComment >= 2)
					{
						$annotation = substr($comment[0], 1);
						$value = $sizeofComment == 2 ? $comment[1] : join(' ', array_slice($comment, 1));

						foreach ($this->handlers as $handlerAnnotation => $handlerValue)
						{
							if (strtolower($annotation) == strtolower($handlerAnnotation))
							{
								call_user_func_array($handlerValue, array($value));
							}
						}
					}
				}
			}
		}

		return $this;
	}

    /**
     * @param string $annotation
     * @param closure $handler
     * @return extractor
     */
	public function setHandler($annotation, \closure $handler)
	{
		$this->handlers[$annotation] = $handler;

		return $this;
	}

    /**
     * @param $annotation
     * @return $this
     */
	public function unsetHandler($annotation)
	{
		if (isset($this->handlers[$annotation]) === true)
		{
			unset($this->handlers[$annotation]);
		}

		return $this;
	}

    /**
     * @return array
     */
	public function getHandlers()
	{
		return $this->handlers;
	}

    /**
     * @return $this
     */
	public function resetHandlers()
	{
		$this->handlers = array();

		return $this;
	}

    /**
     * Convert on/off string value to true/false
     *
     * @static
     * @param string $value
     * @return bool
     */
	public static function toBoolean($value)
	{
		return strcasecmp($value, 'on') == 0;
	}

    /**
     * @static
     * @param string $value
     * @return array
     */
	public static function toArray($value)
	{
		return array_values(array_unique(preg_split('/\s+/', $value)));
	}
}

?>
