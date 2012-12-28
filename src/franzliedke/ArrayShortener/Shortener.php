<?php

namespace franzliedke\ArrayShortener;

class Shortener
{
	protected $balance = 0;
	protected $array_balancer_stack = array();

	protected $tokens = array();

	public function shorten($source)
	{
		$this->tokens = $this->tokenize($source);

		$output = '';
		foreach ($this->tokens as $index => $token)
		{
			$output .= $this->handleToken($index);
		}

		$this->tokens = array();

		return $output;
	}

	protected function tokenize($source)
	{
		$tokens = token_get_all($source);

		// Reset our parenthesis balancer
		$this->balance = 0;
		$this->array_balancer_stack = array();

		return $tokens;
	}

	protected function handleToken($index)
	{
		if (is_array($this->tokens[$index]))
		{
			return $this->handleArrayToken($index);
		}
		else
		{
			return $this->handleStringToken($index);
		}
	}

	protected function handleArrayToken($index)
	{
		$token = $this->tokens[$index];
		$id = $token[0];

		if ($id == T_ARRAY)
		{
			return $this->handleOldArray($index);
		}

		// All other tokens can be left untouched
		return $token[1];
	}

	protected function handleOldArray($index)
	{
		if ($this->isOldArrayDefinition($index))
		{
			$this->array_balancer_stack[] = $this->balance;

			// We just return an empty string here, as the actual square
			// brackets will be inserted when we encounter the opening paren
			// for this old-style array definition statement.
			return '';
		}

		return 'array';
	}

	protected function isOldArrayDefinition($index)
	{
		do
		{
			$next = $this->tokens[++$index];
		}
		while (is_array($next) && $next[0] == T_WHITESPACE);

		return $next == '(';
	}

	protected function handleStringToken($index)
	{
		$token = $this->tokens[$index];

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
		if (count($this->array_balancer_stack) && end($this->array_balancer_stack) == $this->balance)
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

		if (count($this->array_balancer_stack) && end($this->array_balancer_stack) == $this->balance)
		{
			array_pop($this->array_balancer_stack);
			return ']';
		}

		return ')';
	}
}