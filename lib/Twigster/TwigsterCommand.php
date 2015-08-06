<?php

namespace Twigster;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser as YamlParser;

use Exception;

class TwigsterCommand extends Command {
    
    protected function configure() {
        $this
            ->setName('render')
            ->setDescription('Render twig template')
            ->addArgument(
                'template',
                InputArgument::REQUIRED,
                'Twig template to render'
            )
            ->addOption(
               'dir',
               'd',
               InputOption::VALUE_REQUIRED,
               'twig templates root directory (for resolving paths)',
               '.'
            )
            ->addOption(
               'yaml',
               'y',
               InputOption::VALUE_OPTIONAL,
               'path to a .yaml file that contains locals'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $template = $input->getArgument('template');
        $dirName = $input->getOption('dir');
        $yamlPath = $input->getOption('yaml');

        // Create new Twigster instance
        $twigster = new Twigster($template, $dirName);
        
        // Set root directory
        if(!file_exists($twigster->getFullTemplatePath())) {
            throw new Exception("The template file '$template' could not be found in directory '$dirName'", 1);
        }
        
        // Set locals if yamlPath is set
        if($yamlPath) {
            $yamlParser = new YamlParser();
            $yamlContents = file_get_contents($yamlPath);
            $values = $yamlParser->parse($yamlContents);
            $twigster->setLocal($values);
        }

        $result = $twigster->render();
        
        echo "<pre>";
        var_dump($result);
        echo "</pre>";
        die();

    }
}
