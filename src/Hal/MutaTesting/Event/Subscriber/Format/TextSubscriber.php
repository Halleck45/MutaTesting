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
    private $cursor = 80;

    public function __construct(InputInterface $input, OutputInterface $output, $filename)
    {
        $this->input = $input;
        $this->output = $output;
        $this->filename = $filename;
        if (!file_exists(dirname($this->filename))) {
            throw new \LogicException('Please create the [TextReport] destination folder first');
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
        $text = '';
        $found = 0;
        $nbMutants = 0;
        $diff = new \SebastianBergmann\Diff\Diff;
        foreach ($event->getMutations() as $mutation) {

            $nbMutants += sizeof($mutation->getMutations());

            foreach ($mutation->getMutations() as $mutated) {
                $unit = $mutated->getUnit();
                if ($unit->getNumOfFailures() == 0 && $unit->getNumOfErrors() == 0) {
                    $found++;
                    $text .= PHP_EOL.PHP_EOL;
                    $text .= sprintf('    Mutation survived in %s', $mutation->getSourceFile());
                    $text .= "\t" . str_replace(PHP_EOL, PHP_EOL . "\t", $diff->diff($mutation->getTokens()->asString(), $mutated->getTokens()->asString()));
                }
            }
        }

        // write file
        file_put_contents($this->filename, $text);
        $this->output->writeln(sprintf('<info>file "%s" created', $this->filename));
    }

}
