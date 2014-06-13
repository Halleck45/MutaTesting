<?php

namespace Hal\MutaTesting\Event\Subscriber\Format;

use Hal\MutaTesting\Event\FirstRunEvent;
use Hal\MutaTesting\Event\MutationCreatedEvent;
use Hal\MutaTesting\Event\MutationEvent;
use Hal\MutaTesting\Event\ParseTestedFilesEvent;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleSubscriber implements EventSubscriberInterface
{

    private $input;
    private $output;
    private $cursor = 80;

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
            , 'mutate.parseTestedFilesDone' => array('onParseTestedFilesEnd', 0)
            , 'mutate.mutation' => array('onMutation', 0)
            , 'mutate.mutationsDone' => array('onMutationsDone', 0)
            , 'mutate.mutationCreated' => array('onMutationCreated', 0)
            , 'mutate.mutationCreatedDone' => array('onMutationCreatedDone', 0)
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
        $this->progress('.');
    }


    public function onMutationCreated(MutationCreatedEvent $event)
    {
        $this->progress('.');
    }

    public function onMutationCreatedDone(MutationCreatedEvent $event)
    {
        $this->cursor = 0;
    }

    public function onParseTestedFilesEnd(UnitsResultEvent $event)
    {
        $this->cursor = 0;
    }


    public function onMutation(MutationEvent $event)
    {

        if (!$event->getUnit()) {
            $this->progress('<error>E</error>');
            return;
        }
        if (!$event->getUnit()->hasFail()) {
            $this->progress('L');
        } else {
            $this->progress('.');
        }
    }

    public function onMutationsDone(\Hal\MutaTesting\Event\MutationsDoneEvent $event)
    {

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln('Result:');


        // total
        $service = new \Hal\MutaTesting\Mutation\Consolidation\TotalService(($event->getMutations()));


        switch(true) {
            case ($service->getScore() > 80):
                $color = 'info';
                break;
            case ($service->getSurvivors()->count() > 50):
                $color = 'warning';
                break;
            default:
                $color = 'error';
                break;
        }

        $this->output->writeln(sprintf("\t".'<%2$s>score: %1$s%%</%2$s>', $service->getScore(), $color));
        $this->output->writeln(sprintf("\t%d mutants.", $service->getMutants()->count()));
        $this->output->writeln(sprintf("\t%d survivors.", $service->getSurvivors()->count()));
    }

    public function progress($char)
    {
        $this->cursor++;
        if ($this->cursor > 80) {
            $this->cursor = 0;
            $this->output->write(PHP_EOL);
        }
        $this->output->write($char);
    }

}
