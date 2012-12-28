<?php

namespace franzliedke\ArrayShortener;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShortenCommand extends Command
{
	protected $balance = 0;
	protected $array_balancer_stack = array();

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

		$tokens = $this->tokenize($source);

		foreach ($tokens as $token)
		{
			$convertedToken = $this->handleToken($token);
			$output->write($convertedToken);
		}
	}

	protected function tokenize($source)
	{
		$tokens = token_get_all($source);

		// Reset our parenthesis balancer
		$this->balance = 0;
		$this->array_balancer_stack = array();

		return $tokens;
	}

	protected function handleToken($token)
	{
		if (is_array($token))
		{
			return $this->handleArrayToken($token);
		}
		else
		{
			return $this->handleStringToken($token);
		}
	}

	protected function handleArrayToken($token)
	{
		$id = $token[0];

		if ($id == T_ARRAY)
		{
			return $this->handleOldArray();
		}

		// All other tokens can be left untouched
		return $token[1];
	}

	protected function handleOldArray()
	{
		$this->array_balancer_stack[] = $this->balance;

		// We just return an empty string here, as the actual square brackets
		// will be inserted when we encounter the parentheses belonging to this
		// old-style array.
		return '';
	}

	protected function handleStringToken($token)
	{
		if ($token == '(')
		{
			return $this->handleOpeningParen();
		}
		else if ($token == ')')
		{
			return $this->handleClosingParen();
		}

		// All other tokens can be left untouched
		return $token;
	}

	protected function handleOpeningParen()
	{
		if (end($this->array_balancer_stack) == $this->balance)
		{
			$this->balance++;
			return '[';
		}

		$this->balance++;
		return '(';
	}

	protected function handleClosingParen()
	{
		$this->balance--;

		if (end($this->array_balancer_stack) == $this->balance)
		{
			array_pop($this->array_balancer_stack);
			return ']';
		}

		return ')';
	}
}