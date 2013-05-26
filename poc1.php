<?php

//var_dump(token_get_all('<?php 1*2;'));exit;
require_once __DIR__ . '/vendor/autoload.php';

function token2php($tokens, $tag = false)
{
    $ret = "";
    foreach ($tokens as $token) {
        if (is_string($token)) {
            $ret.= $token;
        } else {
            list($id, $text) = $token;

            $ret.= $text;
        }
    }
    return ($tag ? '<?php ' : '' )
            . trim(str_replace(array('<?php', '?>'), array('', ''), $ret));
}

$filename = '/tmp/junit-all.xml';
`phpunit.phar --log-junit $filename`;


$testsuites = array();

// extract tests
echo "extraction...\n";
$xml = simplexml_load_file($filename);
foreach ($xml->xpath('//testsuite/testsuite') as $info) {

    $test = new StdClass;
    $test->name = (string) $info['name'];
    $test->file = (string) $info['file'];
    $test->tests = (string) $info['tests'];
    $test->failures = (string) $info['failures'];
    $test->assertions = (string) $info['assertions'];
    $test->time = (string) $info['time'];

    array_push($testsuites, $test);
}

// get tested files
echo "analysing coverage...\n";
foreach ($testsuites as &$test) {

    $test->includedFiles = array();

    $filename = '/tmp/cover.xml';
    $command = sprintf('phpunit.phar --coverage-clover %s %s', $filename, $test->file);
    `$command`;

    $xml = simplexml_load_file($filename);
    foreach ($xml->xpath('//file') as $file) {

        if (preg_match('!phpunit.phar$!', $file['name'])) {
            continue;
        }
        array_push($test->includedFiles, (string) $file['name']);
    }
}


//
// mutaters. Il faudra utiliser des tokens, et ne pas faire tout d'un coup, mais un par un
$mutaters = array();
array_push($mutaters, function($code) {
            return str_replace('true', 'false', $code);
        });
array_push($mutaters, function($code) {
            return str_replace('*', '/', $code);
        });
//[283] => T_IS_EQUAL
//[282] => T_IS_NOT_EQUAL
// mutate code
$output = '';
echo "code mutating...\n";
foreach ($testsuites as &$test) {

    // create mutations
    $content = '';
    foreach ($test->includedFiles as $file) {

        $tokens = token_get_all(file_get_contents($file));

        // todo here
        $mutatedContent = token2php($tokens, true);
        foreach ($mutaters as $mutater) {
            $mutatedContent = $mutater($mutatedContent);
        }
        //
        $content .= $mutatedContent;
    }


    //
    // Mocking file system to avoid to reload original files
    $content .= '?><?php $globalFilesMutator = ' . var_export($test->includedFiles, true);
    $content .= file_get_contents(__DIR__ . '/resource-mutator-stream.php');


    $bootstrap = '/tmp/bootstrap.php';
    file_put_contents($bootstrap, $content);

    // runs tests with my own php content
    $filename = '/tmp/junit-one.xml';
    $command = sprintf('phpunit.phar --log-junit %s --bootstrap %s %s', $filename, $bootstrap, $test->file);
    `$command`;

    $xml = simplexml_load_file($filename);
    foreach ($xml->xpath(sprintf('//testsuites/testsuite[@file="%s"]', $test->file)) as $info) {
        $nbFailures = (integer) $info['failures'];

        if ($nbFailures == 0) {
            echo 'S'; // no failures: mutation survived

            $output .= sprintf(PHP_EOL . 'survivor in %s', $test->name);
        } else {
            echo '.';  // mutation killed
        }
    }
}

if ($output) {
    echo str_repeat(PHP_EOL, 2) . $output;
}

die(PHP_EOL . 'done' . PHP_EOL);