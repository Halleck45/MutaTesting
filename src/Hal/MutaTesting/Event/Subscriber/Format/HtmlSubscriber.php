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

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../Resources/views/');
        $twig = new \Twig_Environment($loader, array());
        $diff = new \Hal\MutaTesting\Diff\DiffHtml();
        $serviceFile = new \Hal\MutaTesting\Mutation\Consolidation\SourceFileService(($event->getMutations()));
        $serviceTotal = new \Hal\MutaTesting\Mutation\Consolidation\TotalService(($event->getMutations()));

        // total
        $total = (object) array(
                    'score' => $serviceTotal->getScore()
                    , 'scoreStep' => ceil($serviceTotal->getScore() / 25) * 25
                    , 'survivors' => $serviceTotal->getSurvivors()->count()
                    , 'mutants' => $serviceTotal->getMutants()->count()
        );

        // by file
        $byFile = array();
        $files = $serviceFile->getAvailableFiles();
        foreach ($files as $file) {
            $byFile[$file] = (object) array(
                        'score' => $serviceFile->getScore($file)
                        , 'scoreStep' => ceil($serviceFile->getScore($file) / 25) * 25
                        , 'survivors' => $serviceFile->getSurvivors($file)->count()
                        , 'mutants' => $serviceFile->getMutants($file)->count()
                        , 'survivedMutations' => array()
            );
        }

        // by file, with diff
        foreach ($event->getMutations() as $mutation) {
            $src = $mutation->getSourceFile();
            foreach ($mutation->getMutations()->getSurvivors()->all() as $survivor) {
                $byFile[$src]->survivedMutations[] = (object) array(
                            'mutant' => $survivor
                            , 'diff' => $diff->diff($mutation->getTokens()->asPhp(), $survivor->getTokens()->asPhp())
                );
                
            }
        }

        // render html
        $html = $twig->render('report.html.twig', array(
            'files' => $byFile
            , 'total' => $total
        ));

        // write file
        $filename = $this->directory . 'mutation.html';
        file_put_contents($filename, $html);
        $this->output->writeln(sprintf('<info>file "%s" created', $filename));
    }

}
