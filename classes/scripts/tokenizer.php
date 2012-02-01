<?php

namespace mageekguy\atoum\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\exceptions
;

class tokenizer extends atoum\script
{
	protected $files = array();
	protected $iterators = array();

	public function addFile($path)
	{
		$this->files[] = (string) $path;

		return $this;
	}

	public function getFiles()
	{
		return $this->files;
	}

	public function addDirectory($directory)
	{
		try
		{
			foreach (new \recursiveIteratorIterator(new \recursiveDirectoryIterator($directory)) as $file)
			{
				if (substr($file->getBasename(), 0, 1) !== '.')
				{
					$this->addFile($file);
				}
			}
		}
		catch (\exception $exception)
		{
			throw new exceptions\runtime('Unable to open directory \'' . $directory . '\'');
		}

		return $this;
	}

	public function run(array $arguments = array())
	{
		parent::run($arguments);

		$tokenizer = new php\tokenizer();

		foreach ($this->files as $file)
		{
			$this->iterators[$file] = $tokenizer->tokenize(file_get_contents($file))->getIterator();
		}

		return $this;
	}

	protected function setArgumentHandlers()
	{
		$this
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) != 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					$script->help();
				},
				array('-h', '--help'),
				null,
				$this->locale->_('Display this help')
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) == 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					foreach ($values as $file)
					{
						$script->addFile($file);
					}
				},
				array('-f', '--files'),
				'<file>...',
				$this->locale->_('Add all <file>')
			)
			->addArgumentHandler(
				function($script, $argument, $values) {
					if (sizeof($values) == 0)
					{
						throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
					}

					foreach ($values as $directory)
					{
						$script->addDirectory($directory);
					}
				},
				array('-d', '--directories'),
				'<directory>...',
				$this->locale->_('Add all <directory>')
			)
		;

		return $this;
	}
}

?>
