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


### As phar archive

Simply download the [phar archive](build/mutatesting.phar) and run the following command :

```
php mutatesting.phar {tool} {binary}  {test directory}
```

### With Composer

Edit your `composer.json`:

```json
"require": {
    "halleck45/mutatesting" : "@dev"
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

#### Strategy

In order to avoid to have too much mutants, you can use specific strategies 
to determine if a mutant will bu used or not.

There is only one strategy today : the Random strategy. 

To determine to probability of mutations, you can use the `--level` option. `1` = low, `5`= high (default:3)

```bash
./bin/mutatesting {tool} {binary} {test directory} --level=3
```

#### Formatters

To have a html report file, tou need to use the `--format` option. 
Remember to give also a `--out` option for the destination directory.

```bash
./bin/mutatesting {tool} {binary} {test directory} --format=html --out=./logFolder
```

#### Testing options

If your tests need options, you can pass them with `--options`

```bash
./bin/mutatesting phpunit phpunit.phar  myTestFolder --options="-c phpunit.xml"
```

#### Parallelization

You can change the number of parallelized tests with the `processes` options :
```bash
./bin/mutatesting {tool} {binary} {test directory} --processes=10
```


## Copyright

Copyright (c) 2013 Jean-François Lépine. See LICENSE for details.
