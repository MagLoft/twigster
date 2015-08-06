<?php

namespace Twigster;

use Twig_Loader_Filesystem;
use Twig_Environment;

class Twigster {
    const VERSION = "0.2.0";
    private $_locals = array();
    private $_dirName = ".";
    private $_template = null;
    
    public function __construct($template=null, $dirName=null) {
        if($template) {
            $this->setTemplate($template);
        }
        if($dirName) {
            $this->setDirName($dirName);
        }
    }
    
    public function setTemplate($template) {
        $this->_template = $template;
    }
    
    public function getFullTemplatePath() {
        return $this->_dirName . "/" . $this->_template;
    }
    
    public function setLocal($local, $value = null) {
        if (!is_array($local)) {
            $local = array(
                $local => $value
            );
        }
        $this->_locals = array_merge($this->_locals, $local);
    }
    
    public function setDirName($dirName) {
        $this->_dirName = $dirName;
    }
    
    public function render() {
        $loader = new Twig_Loader_Filesystem($this->_dirName);
        $twig   = new Twig_Environment($loader);
        return $twig->render($this->_template, $this->_locals);
    }
    
    public function showUsage() {
        echo <<<USAGE
Usage: twigster [options] template
twigster is a standalone parser for twig templates

Arguments:
  template       path to the twig temlpate to process

Options:
  -h, --help     show this help message and exit
  -d, --dir      twig templates root directory (for resolving paths)
  -y, --yaml     path to a .yaml file that contains locals
  -v, --version  show Twigster version

USAGE;
        exit(0);
    }
}
