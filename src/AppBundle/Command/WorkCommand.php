<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class WorkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:work')
            ->setDescription('Run Units of Work')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = $this->getContainer()->get('worker');
        $output->writeln(sprintf('Running worker %s', get_class($worker)));
        $worker->run();
    }
}
