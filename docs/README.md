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



