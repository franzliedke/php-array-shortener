PHP Array Shortener
===================

[![Build status](https://secure.travis-ci.org/franzliedke/php-array-shortener.png)](https://travis-ci.org/franzliedke/php-array-shortener)

A CLI tool that converts PHP files to use the new PHP 5.4+ shorthand array syntax.

It even runs on PHP 5.3. Go figure.


Installation
------------

After downloading, make sure you have [Composer](http://getcomposer.org) installed and run `composer install`.


Usage
-----

In your command line, run:

    php shortener shorten filename.php

This will print the converted code to the command line so that you can pipe it to another file, for example.

To convert all files in an entire directory, run:

    php shortener shorten dirname

To convert all files in an entire directory and all of its subdirectories, run either of the following two commands:

    php shortener shorten -r dirname
    php shortener shorten --recursive dirname
