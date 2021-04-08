<?php

use franzliedke\ArrayShortener\Shortener;

class ShortenerTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @dataProvider exampleProvider
	 */
	public function testShorten($input, $expected)
	{
		$shortener = new Shortener;
		$output = $shortener->shorten($input);

		$this->assertEquals($expected, $output);
	}

	public function exampleProvider()
	{
		$examples = array();

		foreach (new DirectoryIterator(__DIR__.'/examples') as $file)
		{
			if ($file->isDir() && !$file->isDot())
			{
				$input = file_get_contents($file->getPathname().'/input.php');
				$output = file_get_contents($file->getPathname().'/output.php');
				$examples[] = array($input, $output);
			}
		}
		
		return $examples;
	}
}
