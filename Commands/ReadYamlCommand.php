<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ReadYamlCommand extends Command
{
    protected function configure()
    {
        $this->setName('update');
        $this->addArgument('file');
        $this->addArgument('node-path');
        $this->addArgument('value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $yaml = Yaml::parse(file_get_contents($file));

        $path = explode('.', $input->getArgument('node-path'));

        $result = igorw\update_in($yaml, $path, function()use($input){
            return $input->getArgument('value');
        });

        file_put_contents($file, Yaml::dump($result));
    }
}
