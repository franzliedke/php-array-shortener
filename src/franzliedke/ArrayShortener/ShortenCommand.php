<?php

namespace franzliedke\ArrayShortener;

use CallbackFilterIterator;
use DirectoryIterator;
use EmptyIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShortenCommand extends Command
{
	protected $shortener;

	protected $balance = 0;
	protected $array_balancer_stack = array();

	public function setShortener(Shortener $shortener)
	{
		$this->shortener = $shortener;
	}

	protected function configure()
	{
		$this->setName('shorten')
		     ->setDescription('Convert a PHP file to use the new PHP 5.4+ shorthand array syntax.')
		     ->addArgument('file', InputArgument::REQUIRED, 'Which file or folder do you want to convert?')
		     ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'The folder where the converted files should be placed. Defaults to shortened_files/.', 'shortened_files/')
		     ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Should subfolders be converted, too?');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getArgument('file');
		$iterator = $this->getIterator($file, $input);

		$numFiles = 0;

		foreach ($iterator as $element)
		{
			$filename = $element->getPathname();

			$output->writeln('Process file '.$filename);
			$result = $this->processFile($filename);
			$this->writeResult($input->getOption('output'), $filename, $result);

			$numFiles++;
		}

		$output->writeln('');
		$output->writeln($numFiles.' file(s) processed.');
	}

	protected function getIterator($file, InputInterface $input)
	{
		if (is_dir($file))
		{
			// We shall only process PHP files
			$callback = function($current, $key, $iterator)
			{
				return $current->getExtension() == 'php';
			};

			if ($input->hasOption('recursive'))
			{
				$inner = new RecursiveDirectoryIterator($file);
				$iterator = new RecursiveIteratorIterator($inner, RecursiveIteratorIterator::CHILD_FIRST);
			}
			else
			{
				$iterator = new DirectoryIterator($file);
			}
		}
		else if (file_exists($file))
		{
			// We may only run the single file that was requested.
			$callback = function ($current, $key, $iterator) use ($file)
			{
				return basename($file) == $current->getBasename();
			};

			$iterator = new DirectoryIterator(dirname($file));
		}
		else
		{
			return new EmptyIterator;
		}

		return new CallbackFilterIterator($iterator, $callback);
	}

	protected function processFile($filename)
	{
		return $this->shortener->shorten(file_get_contents($filename));
	}

	protected function writeResult($outputDir, $filename, $str)
	{
		$outputDir = rtrim($outputDir, '/').'/';
		$outputFile = $outputDir.$filename;

		$subdir = dirname($outputFile);
		$this->makeDir($subdir);
		
		file_put_contents($outputFile, $str);
	}

	protected function makeDir($name)
	{
		if (!file_exists($name))
		{
			return mkdir($name, 0777, true);
		}

		return true;
	}
}