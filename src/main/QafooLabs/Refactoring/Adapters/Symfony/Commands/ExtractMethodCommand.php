<?php

namespace QafooLabs\Refactoring\Adapters\Symfony\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use QafooLabs\Refactoring\Application\ExtractMethod;
use QafooLabs\Refactoring\Adapters\PHPParser\ParserVariableScanner;
use QafooLabs\Refactoring\Adapters\TokenReflection\StaticCodeAnalysis;
use QafooLabs\Refactoring\Adapters\Patches\PatchEditor;
use QafooLabs\Refactoring\Adapters\Symfony\OutputPatchCommand;

use QafooLabs\Refactoring\Domain\Model\LineRange;
use QafooLabs\Refactoring\Domain\Model\File;

/**
 * Symfony Adapter to execute the Extract Method Refactoring
 */
class ExtractMethodCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('extract-method')
            ->setDescription('Extract a list of statements into a method.')
            ->addArgument('file', InputArgument::REQUIRED, 'File that contains list of statements to extract')
            ->addArgument('range', InputArgument::REQUIRED, 'Line Range of statements that should be extracted.')
            ->addArgument('newmethod', InputArgument::REQUIRED, 'Name of the new method.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = File::createFromPath($input->getArgument('file'), getcwd());
        $range = LineRange::fromString($input->getArgument('range'));
        $newMethodName = $input->getArgument('newmethod');

        $scanner = new ParserVariableScanner();
        $codeAnalysis = new StaticCodeAnalysis();
        $editor = new PatchEditor(new OutputPatchCommand($output));

        $extractMethod = new ExtractMethod($scanner, $codeAnalysis, $editor);
        $extractMethod->refactor($file, $range, $newMethodName);
    }
}
