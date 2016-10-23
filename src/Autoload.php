<?php
class PujaAutoLoad
{
    protected $classMap;
    public function __construct()
    {
        $this->loadClassMap();
        if (function_exists('spl_autoload_register')) {
            spl_autoload_register(array($this, 'getClassMap'));
        } else {
            trigger_error('You should update your PHP(' . PHP_VERSION . ') to PHP 5.1.2 or later to make your app run faster', E_USER_NOTICE);
            $this->manualLoad();
        }

    }

    protected function loadClassMap()
    {
        $dir = dirname(__FILE__) . '/';
        $this->classMap = array(
            'PujaCache' => $dir . 'Cache.php',
            'PujaCompiler' => $dir . 'Compiler.php',
            'PujaDebug' => $dir . 'Debug.php',
            'PujaException' => $dir . 'Exception.php',
            'PujaTags' => $dir . 'Tags.php',
            'PujaFilter' => $dir . 'Filter.php',
            'Puja' => $dir . 'Puja.php',

        );
    }

    protected function getClassMap($className)
    {
        if (empty($this->classMap[$className])) {
            throw new Exception('Cannot load ' . $className . '');
        }

        require_once $this->classMap[$className];
    }

    public function manualLoad()
    {
        foreach ($this->classMap as $file) {
            require $file;
        }
    }

}

new PujaAutoLoad;
