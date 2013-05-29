<?php

namespace Hal\MutaTesting\Event\Subscriber\Format;

use Hal\MutaTesting\Event\FirstRunEvent;
use Hal\MutaTesting\Event\MutationEvent;
use Hal\MutaTesting\Event\ParseTestedFilesEvent;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TextSubscriber implements EventSubscriberInterface
{

    private $input;
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'mutate.firstrun' => array('onFirstRun', 0)
            , 'mutate.parseTestedFiles' => array('onParseTestedFiles', 0)
            , 'mutate.parseTestedFilesFinished' => array('onParseTestedFilesEnd', 0)
            , 'mutate.mutation' => array('onMutation', 0)
            , 'mutate.mutationsDone' => array('onMutationsDone', 0)
        );
    }

    public function onFirstRun(FirstRunEvent $event)
    {
        $units = $event->getUnits();
        $this->output->writeln(sprintf("  %d tests executed (%d assertions)"
                        , sizeof($units->all())
                        , $units->getNumOfAssertions()
        ));
        if ($units->getNumOfFailures() || $units->getNumOfErrors()) {
            $this->output->writeln(sprintf('<error>  Be careful, some tests fails ! There was %d failures and %d errors</error>'
                            , $units->getNumOfFailures()
                            , $units->getNumOfErrors()
            ));
        } else {
            $this->output->writeln('  <info>OK</info>');
        }
    }

    public function onParseTestedFiles(ParseTestedFilesEvent $event)
    {
        $this->output->write('.');
    }

    public function onParseTestedFilesEnd(UnitsResultEvent $event)
    {
//        $units = $event->getUnits();
//        $nbFiles = array_reduce($units->all(), function($n, $unit) {
//                    $n += sizeof($unit->getTestedFiles());
//                    return $n;
//                });
//        $this->output->writeln(sprintf(PHP_EOL . '  %d source files are used by tests', $nbFiles));
    }

    public function onMutation(MutationEvent $event)
    {
        if(!$event->getUnit()) {
            $this->output->write('<error>E</error>');
            return;
        }
        if ($event->getUnit()->getNumOfFailures() == 0 && $event->getUnit()->getNumOfErrors() == 0) {
            $this->output->write('L');
        } else {
            $this->output->write('.');
        }
    }

    public function onMutationsDone(\Hal\MutaTesting\Event\MutationsDoneEvent $event)
    {
        $found = 0;
        $nbMutants = 0;
        $diff = new \SebastianBergmann\Diff;
        foreach ($event->getMutations() as $mutation) {

            $nbMutants += sizeof($mutation->getMutations());

            foreach ($mutation->getMutations() as $mutated) {
                $unit = $mutated->getUnit();
                if ($unit->getNumOfFailures() == 0 && $unit->getNumOfErrors() == 0) {
                    $found++;
                    $this->output->writeln('');
                    $this->output->writeln('');
                    $this->output->writeln(sprintf('    Mutation survived in %s', $mutation->getSourceFile()));
                    $this->output->writeln("\t" . str_replace(PHP_EOL, PHP_EOL . "\t", $diff->diff($mutation->getTokens()->asPhp(), $mutated->getTokens()->asPhp())));
                }
            }
        }

        $this->output->writeln('Result:');
        $this->output->writeln(sprintf("\t%d mutants tested.", $nbMutants));

        if ($found == 0) {
            $this->output->writeln("\t<info>no mutant survived</info>");
        } else {
            $this->output->writeln(sprintf("\t<error>%d mutants survived</error>", $found));
        }
    }

}
