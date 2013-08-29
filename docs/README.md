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

In the above example, {{ section.title }} will be replaced with the <strong>title</strong> attribute of the <strong>section</strong> object:
<pre></pre>
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

<strong>II. Filters</strong><br />
You can modify variables for display by using filters.<br />
Example:
<pre>
    {{ address|lower }}
    {{ address|lower|cutnword:4 }}
    {{ today|date:"d/m/Y" }}
</pre>
If address value is <strong>HERE IS THE PUJA FILTER SAMPLES</strong> and today value is <strong>2013-08-29 08:20:05</strong> The result of above example will be:
<pre>
    here is the puja filter samples
    here is the puja ...
    29/08/2013
</pre>
See the <a href="https://github.com/jinnguyen/puja/blob/master/docs/filters.md">built-in filter</a> reference for the complete list.<br />
Or you can also create your own custom template filters; see <a href="https://github.com/jinnguyen/puja/blob/master/docs/custom-template-tags.md">Custom template tags and filters</a>.

<br />
<strong>III. Tags</strong><br />
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

<strong>IV. Comments</strong><br />
To comment-out part of a line in a template, use the comment syntax: {# #}.<br />

For example, this template would render as 'hello':
<pre>{# greeting #}hello</pre>
A comment can contain any template code, invalid or not. For example:

<pre>{# 
{% if foo %}bar{% else %} 
#}</pre>

<strong>V. Including other Templates</strong><br />
1. <strong>include</strong>:
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

<strong>get_file</strong><br />
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


See the <a href="https://github.com/jinnguyen/puja/blob/master/docs/tags.md">built-in tag</a> reference for the complete list.
You can also create your own custom template tags; see <a href="https://github.com/jinnguyen/puja/blob/master/docs/custom-template-tags.md">Custom template tags and filters</a>.
