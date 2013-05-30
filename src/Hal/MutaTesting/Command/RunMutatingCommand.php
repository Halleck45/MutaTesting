<?php

namespace Hal\MutaTesting\Command;

use Hal\MutaTesting\Event\FirstRunEvent;
use Hal\MutaTesting\Event\MutationEvent;
use Hal\MutaTesting\Event\MutationsDoneEvent;
use Hal\MutaTesting\Event\ParseTestedFilesEvent;
use Hal\MutaTesting\Event\Subscriber\Format\TextSubscriber;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Hal\MutaTesting\Mutater\Factory\MutaterFactory;
use Hal\MutaTesting\Mutation\Factory\MutationFactory;
use Hal\MutaTesting\Runner\Adapter\AdapterFactory;
use Hal\MutaTesting\Runner\Process\ProcessManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunMutatingCommand extends Command
{

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
                        'processes', null, InputOption::VALUE_REQUIRED , 'number maximum of parallelized tests', 10
                )
                ->addOption(
                        'format', 'f', InputOption::VALUE_REQUIRED, 'Format (text)', 'text'
                )
        ;
    }

    protected function prepare(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->getDispatcher()
                ->addSubscriber(new TextSubscriber($input, $output));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // version, author
        $output->writeln('mutation testing tool for PHP, by Jean-François Lépine');
        $output->writeln('');
        
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
        $output->writeln('Executing first run...');
        $adapter->run(null, array(), $log);
        $units = $adapter->getSuiteResult($log);

        // event
        $event = new FirstRunEvent($units);
        $this->getApplication()->getDispatcher()->dispatch('mutate.firstrun', $event);


        // Get the tested files
        $output->writeln('Extracting tested files for each test...');
        foreach ($units->all() as $unit) {
            $adapter->parseTestedFiles($unit);
            $this->getApplication()->getDispatcher()->dispatch('mutate.parseTestedFiles', new ParseTestedFilesEvent($unit));
        }
        $this->getApplication()->getDispatcher()->dispatch('mutate.parseTestedFilesDone', new UnitsResultEvent($units));


        // mutation
        $output->writeln("");
        $output->writeln('Executing mutations...');
        $mutaterFactory = new MutaterFactory();
        $mutationFactory = new MutationFactory($mutaterFactory);

        $results = array();
        $processManager = new ProcessManager($input->getOption('processes'));
        $adapter->setProcessManager($processManager);
        foreach ($units->all() as $unit) {
            foreach ($unit->getTestedFiles() as $filename) {
                
                $mainMutation = $mutationFactory->factory(file_get_contents($filename), $filename, $unit->getFile());
                
                foreach ($mainMutation->getMutations() as $mutation) {

                    // processes
                    $dispatcher = $this->getApplication()->getDispatcher();
                    $adapter->runMutation($mutation, array(), null, null, function($unit) use ($dispatcher) {
                                $dispatcher->dispatch('mutate.mutation', new MutationEvent($unit));
                            }
                    );
                }

                $results[] = $mainMutation;
            }
        }

        $processManager->wait();
        $this->getApplication()->getDispatcher()->dispatch('mutate.mutationsDone', new MutationsDoneEvent($results));


        // terminate
        $output->writeln('');
        $output->writeln('');
        $output->writeln('<info>Done</info>');
    }

}
