# MutaTesting

Mutation testing tool for PHP.

[![Build Status](https://secure.travis-ci.org/Halleck45/MutaTesting.png)](http://travis-ci.org/Halleck45/MutaTesting)
[![Latest Stable Version](https://poser.pugx.org/halleck45/mutatesting/v/stable.png)](https://packagist.org/packages/halleck45/mutatesting)

According to [Wikipedia](http://en.wikipedia.org/wiki/Mutation_testing):

> Mutation testing (or Mutation analysis or Program mutation) evaluates the 
  quality of software tests. Mutation testing involves modifying a program's 
  source code or byte code in small ways.


MutaTesting supports [PHPUnit](http://phpunit.de/manual/current/en/index.html) and [atoum](http://docs.atoum.org/).
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

In order to avoid to have too much mutants, MutaTesting uses [PhpMetrics](https://github.com/Halleck45/PhpMetrics) in order
to estimate number of bugs in each tested file, and focuses only on files that contain more anomalies.

You can use --level

There is only one strategy today : the Random strategy. 

To determine to probability of mutations, you can use the `--level` option. `1` = low, `5`= high (default:3)

```bash
./bin/mutatesting {tool} {binary} {test directory} --level=3
```

#### Formatters

To have a html report file, tou need to use the `--report-html` option.

```bash
./bin/mutatesting {tool} {binary} {test directory} --report-html=/tmp/file.html
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


#### Performance

MutaTesting is very slow : your unit tests will be runned as many times as there are mutant.
In order to increase performance, a cache file is created in `/tmp/muta-cache.php`.


## Copyright

Copyright (c) 2014 Jean-François Lépine. See LICENSE for details.
