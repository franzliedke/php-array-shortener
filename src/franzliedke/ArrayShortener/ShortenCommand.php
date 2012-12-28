<?php

namespace franzliedke\ArrayShortener;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShortenCommand extends Command
{
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

		$tokens = token_get_all($source);

		$balance = 0;
		$array_balancer_stack = array();

		foreach ($tokens as $token)
		{
			if (is_array($token))
			{
				$id = $token[0];
				$token = $token[1];

				if ($id == T_ARRAY)
				{
					$array_balancer_stack[] = $balance;
					$token = '';
				}
			}
			// Handle single characters
			else
			{
				if ($token == '(')
				{
					if (end($array_balancer_stack) == $balance)
					{
						$token = '[';
					}

					$balance++;
				}
				else if ($token == ')')
				{
					$balance--;

					if (end($array_balancer_stack) == $balance)
					{
						$token = ']';
						array_pop($array_balancer_stack);
					}
				}
			}

			$output->write($token);
		}

	}
}