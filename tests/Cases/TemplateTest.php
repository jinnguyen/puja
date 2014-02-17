<?php

use Tests\TestCase;

class TemplateTest extends TestCase
{
    public function testHelloWorld()
    {
        $out = $this->tpl->parse('hello_world.tpl', [], true);
        $this->assertEquals($out, "hello world\n");
    }

    public function testVariable()
    {
        $this->assertTemplate("Hello world", [], "Hello world");
        $this->assertTemplate("Hello {{ world }}", ['world' => 'world'], "Hello world");

        // TODO add feature for strong check syntax
        $this->assertTemplate("Hello {{world }}", ['world' => 'world'], "Hello world");
        $this->assertTemplate("Hello {{    world}}", ['world' => 'world'], "Hello world");

        $this->assertTemplate("hello {{ a.b.c }}", [
            'a' => [
                'b' => [
                    'c' => 1
                ]
            ]
        ], "hello 1");

        $this->assertTemplate("hello {{ a.b.c }}", [
            'a' => (object) [
                'b' => [
                    'c' => 1
                ]
            ]
        ], "hello 1");

        $this->assertTemplate("hello {{ a.b.c.d.e }}", [], "hello ");

        $out = $this->tpl->parse('variable.tpl', [
            'username' => 'unknown',
            'user' => [
                'name' => "qwe",
                'spec' => 'something'
            ],
            'special_var' => 'spec'
        ], true);
        $this->assertEquals($out, <<<EOT
Simple: unknown
Element of array: qwe
Special variable( = user.age ): something
Set variable:  => show value: Puja - 20

EOT
        );
    }
}
