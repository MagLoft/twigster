<?php

namespace Twigster;

use Twig_Loader_Filesystem;
use Twig_Environment;

class Twigster {
    const VERSION = "0.1.0";
    private $_locals = array();
    private $_startHooks = array();
    private $_failureHooks = array();
    private $_dirName = ".";
    private $_template = null;
    
    public function __construct($template) {
        $this->_template = $template;
    }
    
    public function onStart($hook) {
        $this->_startHooks[] = $hook;
    }
    
    public function onFailure($hook) {
        $this->_failureHooks[] = $hook;
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
    
    public function start() {
        $result = $this->_runHooks($this->_startHooks);
        $loader = new Twig_Loader_Filesystem($this->_dirName);
        $twig   = new Twig_Environment($loader);
        echo $twig->render($this->_template, $this->_locals);
    }
    
    private function _runHooks($hooks) {
        extract($this->_locals);
        
        foreach ($hooks as $__hook) {
            if (is_string($__hook)) {
                eval($__hook);
            } elseif (is_callable($__hook)) {
                call_user_func($__hook, $this, get_defined_vars());
            } else {
                throw new \RuntimeException(sprintf('Hooks must be closures or strings of PHP code. Got [%s].', gettype($__hook)));
            }
            
            // hooks may set locals
            extract($this->_locals);
        }
        
        return get_defined_vars();
    }
}
