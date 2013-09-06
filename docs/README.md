The Puja template language
=======

A template is simply a text file. It can generate any text-based format (HTML, XML, CSV, LaTeX, etc.).<br /><br />
A template contains variables or expressions, which get replaced with values when the template is evaluated, and tags, which control the logic of the template.<br /><br />
Below is a minimal template that illustrates a few basics. We will cover the details later on:
<pre>
&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;title&gt;My Webpage&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;ul id="navigation"&gt;
        {% for item in navigation %}
            &lt;li&gt;&lt;a href="{{ item.href }}"&gt;{{ item.caption }}&lt;/a&gt;&lt;/li&gt;
        {% endfor %}
        &lt;/ul&gt;

        &lt;h1&gt;My Webpage&lt;/h1&gt;
        {{ variable }}
    &lt;/body&gt;
&lt;/html&gt;

</pre>

<strong>I. Variables</strong><br />
Variables look like this: {{ variable }}. When the template engine encounters a variable, it evaluates that variable and replaces it with the result. 
<br />
Example: {{ section.title }}
<br />

In the above example, {{ section.title }} will be replaced with the <strong>title</strong> attribute of the <strong>section</strong> object.<br />
If you use a variable that doesn’t exist, the template system will insert the value <strong>NULL</strong>.
<pre>
<strong><i>Behind the scenes</i></strong><br />
Technically, when the template system encounters a dot, it will get key of array. 
That mean after compiled <strong>{{ section.title }}</strong> will be <strong>$section['title']</strong>

If $section is a <strong>object</strong>, the Puja will change $section to <strong>array</strong> before compile variable.
So for speed purpose, you should put $section as array and set mode <strong>only_array</strong> = true to avoid Puja converts array automaticly.
</pre>
** <strong>Note</strong>: Variable names consist of any combination of alphanumeric characters and the underscore ("_"). The dot (".") also appears in variable sections, although that has a special meaning, as indicated below.<br />
** <strong>Importantly</strong>, you cannot have spaces or punctuation characters in variable names.
Use a dot (.) to access attributes of a variable.

- <strong>Setting Variables</strong><br />
You can assign values to variables inside code blocks. Assignments use the :
<pre>
{% set foo = 'foo' %}
{% set foo = 5 %}</pre>

<strong>II. Oparators </strong><br />
- <strong>Math</strong><br />
Puja allows you to calculate with values. This is rarely useful in templates but exists for completeness' sake. The following operators are supported:
<pre>
<strong>+</strong>: Adds two objects together (the operands are casted to numbers). {{ 1 + 1 }} is 2.
<strong>-</strong>: Subtracts the second number from the first one. {{ 3 - 2 }} is 1.
<strong>/</strong>: Divides two numbers. The returned value will be a floating point number. {{ 1 / 2 }} is {{ 0.5 }}.
<strong>%</strong>: Calculates the remainder of an integer division. {{ 11 % 7 }} is 4.
<strong>*</strong>: Multiplies the left operand with the right one. {{ 2 * 2 }} would return 4.
</pre>
- <strong>Logic</strong><br />
You can combine multiple expressions with the following operators:
<pre>
$a <strong>and</strong> $b: Returns true if $a and $b are both true.
$a <strong>&&</strong> $b: Returns true if $a and $b are both true.
$a <strong>or</strong> $b: Returns true if $a or $b is true.
$a <strong>||</strong> $b: Returns true if $a or $b is true.
<strong>not</strong> $a: Returns true if $a is false
<strong>!</strong>$a: Returns true if $a is false
$a <strong>in</strong> $array: Returns true if $a in array $array
</pre>
- <strong>Comparisons</strong><br />
The following comparison operators are supported in any expression: ==, ===, !=, !==, &lt;, &gt;, &gt;=, and &lt;=

<strong>III. Filters</strong><br />
You can modify variables for display by using filters.<br />
Example:
<pre>
    {{ address|lower }}
    {{ address|lower|truncatechars:4 }}
    {{ today|date:"d/m/Y" }}
</pre>
If address value is <strong>HERE IS THE PUJA FILTER SAMPLES</strong> and today value is <strong>2013-08-29 08:20:05</strong> The result of above example will be:
<pre>
    here is the puja filter samples
    here is the puja ...
    29/08/2013
</pre>
See the <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-filters.md">built-in filter</a> reference for the complete list.<br />
Or you can also create your own custom template filters; see <a href="https://github.com/jinnguyen/puja/blob/master/docs/custom-template-tags.md">Custom template tags and filters</a>.

<br />
<strong>IV. Tags</strong><br />
Tags look like this: {% tag %}. Tags are more complex than variables: Some create text in the output, some control flow by performing loops or logic, and some load external information into the template to be used by later variables.

Some tags require beginning and ending tags (i.e. {% tag %} ... tag contents ... {% endtag %}).<br />
Here are some of the more commonly used tags:<br />
- <strong>for</strong><br />
Loop over each item in an array. For example, to display a list of athletes provided in athlete_list:
<pre>
    &lt;ul&gt;
	{% for news in news_list %}
	    &lt;li&gt;{{ news.name }}&lt;/li&gt;
	{% endfor %}
	&lt;/ul&gt;
</pre>

- <strong>if</strong> and <strong>else</strong><br />
Evaluates a variable, and if that variable is “true” the contents of the block are displayed:
<pre>{% if news_list %}
    Number of news: {{ news|length }}
{% else %}
    No athletes.
{% endif %}</pre>
In the above, if news_list is not empty, the number of athletes will be displayed by the {{ news_list|length }} variable.<br />
You can also use filters and various operators in the if tag:
<pre>{% if news_list|length > 1 %}
   Muli: {% for news in news_list %} ... {% endfor %}
{% else %}
   One: {{ news_list.0.name }}
{% endif %}</pre>
While the above example works, be aware that most template filters return strings, so mathematical comparisons using filters will generally not work as you expect. length is an exception.

- <strong>block</strong> and <strong>extends</strong><br />
Set up template inheritance (<a href="#template-inheritance">see below</a>), a powerful way of cutting down on “boilerplate” in templates.<br />

See the <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-tags.md">built-in tag</a> reference for the complete list.<br />
You can also create your own custom template tags; see <a href="https://github.com/jinnguyen/puja/blob/master/docs/custom-template-tags.md">Custom template tags and filters</a>.

<strong>V. Comments and Escaping</strong>
- <strong>Comments</strong><br />
To comment-out part of a line in a template, use the comment syntax: {# #}.<br />
For example, this template would render as 'hello':
<pre>{# greeting #}hello</pre>
A comment can contain any template code, invalid or not. For example:
<pre>{# 
{% if foo %}bar{% else %} 
#}</pre>
- <strong>Escaping</strong><br />
It is sometimes desirable or even necessary to have Twig ignore parts it would otherwise handle as variables or blocks. <br />
For example if the default syntax is used and you want to use {{ as raw string in the template and not start a variable you have to use a trick.
The easiest way is to output the variable delimiter ({{) by using a variable expression:
<pre>
\\{{ variable \\}}
</pre>


<strong>VI. Including other Templates</strong><br />
<a name="include"></a>
<strong>1. include</strong>:
The tag is useful to include a template and return the rendered content of that template into the current one.<br />

Example:
<pre>
{% include sidebar.tpl %}</pre>
Per default included templates are passed the current context.<br />
The context that is passed to the included template includes variables defined in the template or force in include tag.<br />
The filename of the template depends on the template loader. <br />
Example:
<pre>$username = 'Jin';</pre>
and sitebar.tpl's content:
<pre>
Include username: {{ username }}
</pre>
Get variable from parent template:
<pre>
<strong>index.tpl's content:</strong>
Welcome {{ username }},
{% include sidebar.tpl %}
</pre>
The result will be:
<pre>
Welcome Jin,
Include username: Jin
</pre>
You can pass additional variable to the template using keyword arguments:
<pre>
<strong>index.tpl's content:</strong>
Here is username in index.tpl: {{ username }},
{% include sidebar.tpl username="Abc" %}
Here is username in index.tpl: {{ username }} after include.
</pre>

The result will be:
<pre>
Here is username in index.tpl: Jin,
Include username: Abc
Here is username in index.tpl: Jin after include.
</pre>
<a name="get_file"></a>
<strong>2. get_file</strong><br />
Same with include, but return the NO RENDER content.<br />
Example:
<pre>
<strong>index.tpl's content:</strong>
Welcome {{ username }},
{% get_file sidebar.tpl %}
</pre>
The resule will be:
<pre>
Welcome Jin,
Include username: {{ username }}
</pre>

<a name="template-inheritance"></a>
<strong>VII. Template Inheritance</strong><br />
The most powerful part of Puja is template inheritance. Template inheritance allows you to build a base "skeleton" template that contains all the common elements of your site and defines blocks that child templates can override.<br />


Let's define a base template, master.tpl, which defines a simple HTML skeleton document that you might use for a simple two-column page:
<pre>&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
      	&lt;title&gt;{% block title %}{% endblock title %} - My Webpage&lt;/title&gt;
      	&lt;link rel="stylesheet" href="style.css" /&gt;
        {% block css %}{% endblock css %}
    &lt;/head&gt;
    &lt;body&gt;
        &lt;div id="content"&gt;
        	{% block content %}{% endblock content %}
      	&lt;/div&gt;
        &lt;script src="/path/to/jquery.js"&gt;&lt;script&gt;
        {% block javascript %}{% endblock javascript %}
    &lt;/body&gt;
&lt;/html&gt;</pre>
In this example, the tags define four blocks that child templates can fill in. All the block tag does is to tell the template engine that a child template may override those portions of the template.<br />
A child template might look like this:
<pre><strong>{% extends master.tpl %}</strong>
{% block title %}Index{% endblock title css %}
{% block css %}
    &lt;style type="text/css"&gt;
        .important { color: #336699; }
    &lt;/style&gt;
{% endblock css %}
{% block javascript %}
	&lt;script&gt;
		$(document).ready(function(){
			console.log('All are already');
		})
	&lt;/script&gt;
{% endblock javascript %}
{% block content %}
    &lt;h1&gt;Index&lt;/h1&gt;
    &lt;p class="important"&gt;
        Welcome to my awesome homepage.
    &lt;/p&gt;
{% endblock content %}</pre>
And here is result:
<pre>&lt;html&gt;
    &lt;head&gt;
      	&lt;title&gt;Index - My Webpage&lt;/title&gt;
      	&lt;link rel="stylesheet" href="style.css" /&gt;
         &lt;style type="text/css"&gt;
        	.important { color: #336699; }
    	&lt;/style&gt;
    &lt;/head&gt;
    &lt;body&gt;
        &lt;div id="content"&gt;
        	&lt;h1&gt;Index&lt;/h1&gt;
		    &lt;p class="important"&gt;
		        Welcome to my awesome homepage.
		    &lt;/p&gt;
      	&lt;/div&gt;
        &lt;script src="/path/to/jquery.js"&gt;&lt;script&gt;
       	&lt;script&gt;
       	$(document).ready(function(){
			console.log('All are already');
		})
		&lt;/script&gt;
    &lt;/body&gt;
&lt;/html&gt;</pre>

** We recommend that you should use inherit template to user master base instead include header.tpl, footer.tpl<br />
<strong>VIII. Coding standards</strong><br />
See <a href="https://github.com/jinnguyen/puja/blob/master/docs/coding-standards.md">Coding standards</a> for more detail.
