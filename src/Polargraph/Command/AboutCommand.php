<?php

namespace Polargraph\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    protected function configure()
    {
        $this->setName('about');
        $this->setDescription('Shows the short information about polargraph');
        $this->setHelp("<info>php polargraph about</info>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Polargraph - Polar Photograph Command Line Tool</info>');
        $output->writeln('<comment>Polargraph help you to make photograph width polar effect.</comment>');
        $output->writeln('<comment>See http://polargraph.chaos.xin/ for more information.</comment>');
    }
}