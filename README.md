# MutaTesting

Mutation testing tool for PHP.

[![Build Status](https://secure.travis-ci.org/Halleck45/MutaTesting.png)](http://travis-ci.org/Halleck45/MutaTesting)


According to [Wikipedia](http://en.wikipedia.org/wiki/Mutation_testing):

> Mutation testing (or Mutation analysis or Program mutation) evaluates the 
  quality of software tests. Mutation testing involves modifying a program's 
  source code or byte code in small ways.


MutaTesting supports [http://phpunit.de/manual/current/en/index.html](PHPUnit) and [http://docs.atoum.org/](atoum). 
You can create an adapter for any PHP testing framework.

## Requirements

You only need PHP 5.3 . No specific PHP extension is required...

## Installation

Edit your `composer.json`:

```json
"require": {
    "halleck45/MutaTesting" : "master"
}
```

And run Composer:

```bash
php composer.phar update halleck45/mutaTesting
```

## Usage

```bash
./bin/mutatesting {tool} {binary}  {test directory}
```

Example for PHPUnit:

```bash
./bin/mutatesting phpunit phpunit.phar myTestFolder
```

Example for atoum:

```bash
./bin/mutatesting atoum mageekguy.atoum.phar myTestFolder
```

Note that you don't need to use the `-d` or `-f` option with atoum...



### Advanced usage

To have a html report file, tou need to use the `--format` option. 
Remember to give also a `--out` option for the destination directory.

```bash
./bin/mutatesting phpunit phpunit.phar  myTestFolder --format=html --out=./logFolder
```

If your tests need options, you can pass them with `--options`

```bash
./bin/mutatesting phpunit phpunit.phar  myTestFolder --options="-c phpunit.xml"
```

You can change the number of parallelized tests with the `processes` options :
```bash
./bin/mutatesting phpunit phpunit.phar  myTestFolder --processes=10
```


## Copyright

Copyright (c) 2013 Jean-François Lépine. See LICENSE for details.
