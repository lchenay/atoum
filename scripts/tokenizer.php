<?php

namespace mageekguy\atoum\scripts\tokenizer;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts
;

require_once __DIR__ . '/../classes/autoloader.php';

$tokenizer = new scripts\tokenizer(__FILE__);

set_error_handler(
	function($error, $message, $file, $line) use ($tokenizer) {
		if (error_reporting() !== 0)
		{
			$tokenizer->writeError($message);

			exit($error);
		}
	}
);

try
{
	$tokenizer->run();
}
catch (\exception $exception)
{
	$tokenizer->writeError($exception->getMessage());

	exit($exception->getCode());
}

exit(0);

?>
