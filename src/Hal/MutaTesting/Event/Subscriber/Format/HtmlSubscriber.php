<?php

namespace Hal\MutaTesting\Event\Subscriber\Format;

use Hal\MutaTesting\Event\FirstRunEvent;
use Hal\MutaTesting\Event\MutationEvent;
use Hal\MutaTesting\Event\ParseTestedFilesEvent;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HtmlSubscriber implements EventSubscriberInterface
{

    private $input;
    private $output;
    private $directory;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->directory = $input->getOption('out');
        if (strlen($this->directory) == 0) {
            throw new \Exception(sprintf('You need to use the --out option when the HTML formater is ised'));
        }
        $this->directory = rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!file_exists($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'mutate.mutationsDone' => array('onMutationsDone', -1)
        );
    }

    public function onMutationsDone(\Hal\MutaTesting\Event\MutationsDoneEvent $event)
    {

        $html = '<html><head><title>MutaTesting</title>'
                .'<style>
                    body { font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; margin0; padding: 0; background-color:#EEE;}
                    .diff { border:1px solid #CCC; background-color:#FFF; width:800px; padding:5px; }
                    .infos { font-size:0.9em; paddin-left:30px; color:#333;}
                </style>'
                .'</head><body>%1$s';

        $found = 0;
        $nbMutants = 0;
        $diff = new \Hal\MutaTesting\Diff\DiffHtml();
        foreach ($event->getMutations() as $mutation) {

            $nbMutants += sizeof($mutation->getMutations());

            foreach ($mutation->getMutations() as $mutated) {
                $unit = $mutated->getUnit();
                if ($unit->getNumOfFailures() == 0 && $unit->getNumOfErrors() == 0) {

                    $html .= sprintf('<h2>%s</h2>', $mutation->getSourceFile())
                            . sprintf('<p class="infos">tested with <span>%s</span>', $unit->getFile())
                            . sprintf('<p class="infos">Duration: <span>%s</span>, Assertions: %s', $unit->getTime(), $unit->getNumOfAssertions())
                            . sprintf('<div class="diff">%s</div>', $diff->diff($mutation->getTokens()->asPhp(), $mutated->getTokens()->asPhp()))
                    ;


                    $found++;
                }
            }
        }

        $html = sprintf($html, sprintf('<h1>%d mutants, %d survived</h1>', $nbMutants, $found));

        $filename = $this->directory . 'mutation.html';
        file_put_contents($filename, $html);
        $this->output->writeln(sprintf('<info>file "%s" created', $filename));
    }

}
