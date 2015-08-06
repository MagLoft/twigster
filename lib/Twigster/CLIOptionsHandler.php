<?php

namespace Twigster;

use Symfony\Component\Yaml\Parser;

class CLIOptionsHandler {

    public function handle($twigster) {
        $args = getopt('hd:y:v', array(
            'help',
            'dir:',
            'yaml:',
            'version'
        ));
        
        foreach ($args as $option => $value) {
            switch ($option) {
                case 'h':
                case 'help':
                    $this->_handleUsageInfo();
                    break;
                case 'd':
                case 'dir':
                    $this->_handleDir($twigster, $value);
                    break;
                case 'y':
                case 'yaml':                    
                    $this->_handleYaml($twigster, $value);
                    break;
                case 'v':
                case 'version':
                    $this->_handleVersion();
                    break;
            }
        }
    }
    
    private function _handleYaml($twigster, $path) {
        $twigster->onStart(function($twigster, $scope) use ($path) {
            $yaml = new Parser();
            $yamlContents = file_get_contents($path);
            $values = $yaml->parse(file_get_contents($path));
            $twigster->setLocal($values);
        });
    }
    
    private function _handleDir($twigster, $dir) {
        $twigster->setDirName($dir);
    }
    
    private function _handleUsageInfo() {
        echo <<<USAGE
Usage: twigster [options]
twigster is a standalone parser for twig templates

Options:
  -h, --help     show this help message and exit
  -d, --dir      twig templates root directory (for resolving paths)
  -y, --yaml     path to a .yaml file that contains locals
  -v, --version  show Twigster version

USAGE;
        exit(0);
    }
    
    private function _handleVersion() {
        printf("Twigster %s\n", Twigster::VERSION);
        exit(0);
    }
}
