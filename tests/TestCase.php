<?php

namespace Tests;

use Puja;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Puja
     */
    public $tpl;

    public function setUp()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

        $this->tpl = new Puja;
        $this->tpl->template_dir = $dir . 'templates' . DIRECTORY_SEPARATOR;
        $this->tpl->cache_dir = $dir . 'cache' . DIRECTORY_SEPARATOR;
        $this->tpl->parse_executer = 'eval';
        $this->tpl->debug = true;
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString . '.tmp';
    }

    public function assertTemplate($content, $data = [], $expected)
    {
        // TODO parseString(string $content, array $data) return compiled template
        $name = $this->generateRandomString();
        $filePath = $this->tpl->template_dir . $name;
        file_put_contents($filePath, $content);

        $out = $this->tpl->parse($name, $data, true);
        $this->assertEquals($out, $expected);
        unlink($filePath);
    }

    public function tearDown()
    {
        $unlinkFiles = glob($this->tpl->template_dir . '*.tmp');
        foreach($unlinkFiles as $file) {
            unlink($file);
        }
    }
}
