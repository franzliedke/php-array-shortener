PHP Array Shortener
===================

![Build status](https://github.com/franzliedke/php-array-shortener/actions/workflows/ci.yml/badge.svg)

A CLI tool that converts PHP files to use the new PHP 5.4+ shorthand array syntax.


Installation
------------

After downloading, make sure you have [Composer](http://getcomposer.org) installed and run `composer install`.


Usage
-----

In your command line, run:

    php shortener shorten <filename.php>

This will print the converted code to the command line so that you can pipe it to another file, for example.

To convert all PHP files in an entire directory, run:

    php shortener shorten <dirname>

To convert all PHP files in an entire directory and all of its subdirectories, run either of the following two commands:

    php shortener shorten -r <dirname>
    php shortener shorten --recursive <dirname>

All converted files will be written to the `shortened_files` directory. If you want to change that behavior (e.g. to convert existing files directly), provide the `output` option:

    php shortener shorten <dirname> -o <myoutputdir>
    php shortener shorten <dirname> --output <myoutputdir>
