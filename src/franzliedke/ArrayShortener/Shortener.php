<?php

namespace franzliedke\ArrayShortener;

class Shortener
{
	protected $balance = 0;
	protected $array_balancer_stack = array();

	public function shorten($source)
	{
		$tokens = $this->tokenize($source);

		$output = '';
		foreach ($tokens as $token)
		{
			$output .= $this->handleToken($token);
		}

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