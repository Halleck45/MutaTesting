<?php

namespace Hal\MutaTesting\Command;

use Exception;
use Hal\Component\OOP\Extractor\ClassMap;
use Hal\Component\OOP\Extractor\Extractor;
use Hal\Component\Token\Tokenizer;
use Hal\Component\Token\TokenType;
use Hal\Metrics\Complexity\Structural\CardAndAgresti\FileSystemComplexity;
use Hal\Metrics\Complexity\Text\Halstead\Halstead;
use Hal\MutaTesting\Cache\OOPInfo;
use Hal\MutaTesting\Cache\UnitInfo;
use Hal\MutaTesting\Event\FirstRunEvent;
use Hal\MutaTesting\Event\MutationCreatedEvent;
use Hal\MutaTesting\Event\MutationEvent;
use Hal\MutaTesting\Event\MutationsDoneEvent;
use Hal\MutaTesting\Event\ParseTestedFilesEvent;
use Hal\MutaTesting\Event\Subscriber\Format\ConsoleSubscriber;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Hal\MutaTesting\Mutater\Factory\MutaterFactory;
use Hal\MutaTesting\Mutation\Factory\MutationFactory;
use Hal\MutaTesting\Runner\Adapter\AdapterFactory;
use Hal\MutaTesting\Runner\Process\ProcessManager;
use Hal\MutaTesting\Specification\FactorySpecification;
use Hal\MutaTesting\Specification\RandomSpecification;
use Hal\MutaTesting\Specification\ScoreSpecification;
use Hal\MutaTesting\Specification\SubscribableSpecification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunMutatingCommand extends Command
{

    private $success = true;
    private $strategy = true;

    protected function configure()
    {
        $this
                ->setName('mutate')
                ->setDescription('Run mutations')
                ->addArgument(
                        'tool', InputArgument::REQUIRED, 'What is your unit testing tool ?'
                )
                ->addArgument(
                        'binary', InputArgument::REQUIRED, 'What is your unit testing tool binary (phpunit.phar, phpunit, atoum.phar...) ?'
                )
                ->addArgument(
                        'path', InputArgument::REQUIRED, 'Directory of your unit tests'
                )
                ->addOption(
                        'options', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Default options used as argument to run your tests'
                )
                ->addOption(
                        'processes', null, InputOption::VALUE_REQUIRED, 'number maximum of parallelized tests', 5
                )
                ->addOption(
                        'report-text', 'rt', InputOption::VALUE_OPTIONAL, 'Destination of HTML report file (ex: /tmp/file.html)'
                )
                ->addOption(
                        'report-html', 'rh', InputOption::VALUE_OPTIONAL, 'Destination of TXT report file (ex: /tmp/file.txt)'
                )

                ->addOption(
                        'bugs', 'bl', InputOption::VALUE_OPTIONAL, 'Mutation is runned only if the estimated number of bugs in tested file is greater than <bugs>.', '.35'
                )
        ;
    }

    protected function prepare(InputInterface $input, OutputInterface $output)
    {
        $availables = array('text', 'html');
        $dispatcher = $this->getApplication()->getDispatcher();
        foreach($availables as $format) {

            if(strlen($input->getOption('report-'.$format)) > 0) {
                $filename = $input->getOption('report-'.$format);

                $class = sprintf('\Hal\MutaTesting\Event\Subscriber\Format\%sSubscriber', ucfirst(strtolower($format)));
                if (!class_exists($class)) {
                    throw new Exception(sprintf('invalid formater "%s" given', $format));
                }
                $dispatcher->addSubscriber(new $class($input, $output, $filename));
            }
        }

        $dispatcher->addSubscriber(new ConsoleSubscriber($input, $output, null));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // version, author
        $output->writeln('Mutation testing tool for PHP, by Jean-François Lépine <http://www.lepine.pro>');
        $output->writeln('');

        //
        // Cache
        $cache = new UnitInfo();

        // get adapter
        $this->prepare($input, $output);
        $factory = new AdapterFactory();
        $adapter = $factory->factory(
                $input->getArgument('tool')
                , $input->getArgument('binary')
                , $input->getArgument('path')
                , $input->getOption('options')
        );

        // First run
        $log = tempnam(sys_get_temp_dir(), 'ru-mutate');
        $output->writeln('Executing test suite...');
        $adapter->run(null, array(), $log);
        $units = $adapter->getSuiteResult($log);
        unlink($log);

        // event
        $event = new FirstRunEvent($units);
        $this->getApplication()->getDispatcher()->dispatch('mutate.firstrun', $event);


        // Get the tested files
        $output->writeln('Extracting tested files...');
        foreach ($units as $k => $unit) {
            if(!$cache->has($unit)) {
                $adapter->parseTestedFiles($unit);
                $cache->persist($unit);
            } else {
                $unit = $cache->get($unit);
                $units->set($k, $unit);
            }
            $this->getApplication()->getDispatcher()->dispatch('mutate.parseTestedFiles', new ParseTestedFilesEvent($unit));
        }
        $this->getApplication()->getDispatcher()->dispatch('mutate.parseTestedFilesDone', new UnitsResultEvent($units));
        $cache->flush();


        $this->strategy = new ScoreSpecification(
            new Halstead(new Tokenizer(), new TokenType())
            , $input->getOption('level')
        );
        $this->getApplication()->getDispatcher()->addSubscriber($this->strategy);

        //
        // Preparing mutations
        $output->writeln('');
        $output->writeln('');
        $output->writeln('Building mutations. This will take few minutes...');
        $mutaterFactory = new MutaterFactory();
        $mutationFactory = new MutationFactory($mutaterFactory, $this->strategy);
        $mutations = array();
        $nbMutations = 0;
        foreach ($units as $unit) {
            foreach ($unit->getTestedFiles() as $filename) {
                $mainMutation = $mutationFactory->factory( $filename, $unit->getFile());

                $childs = array();
                foreach ($mainMutation->getMutations() as $mutation) {
                    $childs[] = $mutation;
                }
                $mutations[] = (object) array(
                    'mainMutation' => $mainMutation
                    , 'childs' => $childs
                );

                $nbMutations += sizeof($childs);
            }
            $this->getApplication()->getDispatcher()->dispatch('mutate.mutationCreated', new MutationCreatedEvent($unit));
        }
        $this->getApplication()->getDispatcher()->dispatch('mutate.mutationCreatedDone', new MutationCreatedEvent($unit));


        //
        // Executing mutations
        $output->writeln('');
        $output->writeln('');
        $output->writeln(sprintf('Executing %d mutations. This will take few minutes. Coffee time ?', $nbMutations));
        $mutaterFactory = new MutaterFactory();
        $mutationFactory = new MutationFactory($mutaterFactory, $this->strategy);

        $results = array();
        $processManager = new ProcessManager($input->getOption('processes'));
        $adapter->setProcessManager($processManager);


        foreach($mutations as $mutationInfo) {

            $mainMutation = $mutationInfo->mainMutation;
            $childs = $mutationInfo->childs;
            foreach($childs as $mutation) {
                $dispatcher = $this->getApplication()->getDispatcher();
                $adapter->runMutation($mutation, array(), null, null, function($unit) use ($dispatcher) {
                        $event = $dispatcher->dispatch('mutate.mutation', new MutationEvent($unit));
                        $this->success &= $event->getUnit() && !$event->getUnit()->hasFail();
                    }
                );
            }
            $results[] = $mainMutation;
        }

        $processManager->wait();
        $this->getApplication()->getDispatcher()->dispatch('mutate.mutationsDone', new MutationsDoneEvent($results));


        // terminate
        $output->writeln('');
        $output->writeln('');
        $output->writeln('<info>Done</info>');

        return ($this->success ? 0 : 1);
    }

}
