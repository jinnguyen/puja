Puja
====

Inspired in Django. Puja is a lightweight, flexible and easy PHP  template engine. Especial, Puja support validate template syntax!

<strong>Some of Puja's features</strong>:
* <strong>VALIDATE TEMPLATE SYNTAX</strong>
* it is extremely fast
* no template parsing overhead, only compiles once.
* it is smart about recompiling only the template files that have changed.
* unlimited nesting of sections, conditionals, etc.
* built-in caching of template output.

<strong>Validate syntax:</strong><br />
Puja support validate syntax before the parser run compiler. This will helpfull for you to write template syntax.

Example:
file template: index.tpl:
<pre>
Hello {{ username },
Welcome you go to the very first exmplate of Puja template.</pre>

The result will be:
<pre>
TemplateSyntaxError: <strong>missing }}</strong>
1. Hello <strong>{{ username </strong>,
2. Welcome you go to the very first exmplate of Puja template.</pre>

Puja only show debug when mode <strong>debug</strong> is enabled<br />
**  We recommend you should only enable mode <strong>debug</strong>  when your app is in develop. And disable it when your app go to production. It will save a lot time to template engine parser.
<br /><br />
<strong>Basic API Usage</strong>:<br />
- template file: index.tpl
<pre>Hello <strong>{{ username }}</strong>,
Welcome you go to the very first exmplate of Puja template.</pre>

- php file: index.php
<pre>
  &lt;?php
  require_once '/path/to/puja.php';
  $tpl = new Puja;
  $data = array(
  	'username'=>'Jin Nguyen',
  );
  $tpl->parse($data, $template_file = 'index.tpl');
  ?&gt;
</pre>

The result will show:
<pre>
Hello <strong>Jin Nguyen</strong>,
Welcome you go to the very first exmplate of Puja template.</pre>

<a href="#">more detail &gt;&gt; </a>










