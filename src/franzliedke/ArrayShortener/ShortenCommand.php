<?php

namespace franzliedke\ArrayShortener;

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
		     ->addArgument('file', InputArgument::REQUIRED, 'Which file do you want to convert?');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getArgument('file');
		$source = file_get_contents($file);

		$result = $this->shortener->shorten($source);

		$output->write($result);
	}
}