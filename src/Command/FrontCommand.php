<?php

declare(strict_types=1);

namespace VerteXVaaR\Front\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use VerteXVaaR\Front\Parsing\Parser;

use function in_array;

class FrontCommand extends Command
{
    protected static $defaultName = 'front:run';

    protected function configure()
    {
        $this->setDescription('Runs the front command');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Type "quit" or "exit" to end this program');
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question('> ');
        while (!in_array($answer = $helper->ask($input, $output, $question), ['quit', 'exit'])) {
            $parser = new Parser();
            $parser->parse($answer ?? '');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }
}
