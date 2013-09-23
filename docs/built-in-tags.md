Built-in template tags
===

<a href="#if">if..elseif..else..endif</a><br />
<a href="#for">for</a><br />
<a href="#for-empty">for..empty</a><br />
<a href="#extends">extends</a><br />
<a href="#block">block..endblock</a><br />
<a href="#set">set</a><br />
<a href="#include">include</a><br />
<a href="#get_file">get_file</a><br />
<a name="if"></a>
- <strong>if</strong>:<br />
<pre>{% if athlete_list %}
      Number of athletes: {{ athlete_list|length }}
{% elif athlete_in_locker_room_list %}
      Athletes should be out of the locker room soon!
{% else %}
      No athletes.
{% endif %}</pre>

<a name="for"></a>
- <strong>for</strong>:<br />
<pre>
{% for key, value in data.items %}
      {{ key }}: {{ value }}
{% endfor %}</pre>

<a name="for-empty"></a>
- <strong>for ... empty</strong>:<br />
The for tag can take an optional {% empty %} clause that will be displayed if the given array is empty or could not be found:
<pre>
{% for key, value in data.items %}
      {{ key }}: {{ value }}
{% empty %}
      Sorry, no item in this list.
{% endfor %}</pre>
The above is equivalent to – but shorter, cleaner, and possibly faster than – the following:
<pre>
{% if data.items|length %}
      {% for key, value in data.items %}
            {{ key }}: {{ value }}
      {% endfor %}
{% else %}
      Sorry, no item in this list.
{% endif %}
</pre>

<a name="extends"></a>
- <strong>extends</strong>:<br />
If this is the first time you come with Puja, you should see <a href="https://github.com/jinnguyen/puja/edit/master/docs#template-inheritance">Template inheritance</a> for basic usage.<br />
** Puja don't support you use multi extends tag in a template.
<pre>{% extends master1.tpl %}
{% extends master2.tpl %}
....</pre>
But you can extends multi level, that mean:<br />
<pre><strong>master1.tpl</strong>
{% block body %}
Master1 body
{% endblock %}<br /><br />
<strong>master2.tpl</strong>
{% extends master1.tpl %}
{% block body %}
Master2 body
      {% block sub_body %}
            master2 sub body
      {% endblock %}
{% endblock %}<br /><br />
<strong>index.tpl</strong>
{% extends master2.tpl %}
{% block sub_body %}
Index sub body
{% endblock %}
</pre>
And the result will be:
<pre>
Master2 body
      Index sub body

</pre>
<a name="block-endblock"></a>
- <strong>block..endblock</strong>:<br />
If this is the first time you come with Puja, you should see <a href="https://github.com/jinnguyen/puja/edit/master/docs#template-inheritance">Template inheritance</a> for basic usage.<br />
There are two ways to define a block:
<pre>
      1/. {% block block_name %}...{% endblock blockname %}
      2/. {% block block_name %}...{% endblock %}
</pre>
If a block has been extended by a child template, this block content will be the content of the block in child template.

<a name="set"></a>
- <strong>set</strong>:<br />
Inside code blocks you can also assign values to variables. Assignments use the set tag and can have multiple targets.<br />
Here is how you can assign the bar value to the foo variable:
<pre>
  {% set foo = 'bar' %}
  {% set foo = 'bar' max_point = 100 username=foo %}
</pre>
After the set call, the foo variable is available in the template like any other ones:
<pre>
    Foo: {{ foo }} // bar
    Max point: {{ max_point }} // 100
    Username: {{  username }} //bar
</pre>

<a name="include"></a>
- <strong>include</strong>:<br />
If this is the first time you come with Puja, you shoule see <a href="https://github.com/jinnguyen/puja/edit/master/docs#include">Including other Templates</a> for basic usage.<br />
** You can include file in a included file ( include multi level)
<pre>
<strong>index.tpl</strong>
....
{% include sub.tpl %}
....<br />
<strong>sub.tpl</strong>
== sub content ==<br />
{% include sub1.tpl %}
<strong>sub1.tpl</strong>
== sub1 content ==
</pre>
The reuslt will be:
<pre>
....
== sub content ==
== sub1 content ==
....
</pre>

<a name="get_file"></a>
- <strong>get_file</strong>:<br />
If this is the first time you come with Puja, you shoule see <a href="https://github.com/jinnguyen/puja/edit/master/docs#get_file">Including other Templates</a> for more information.<br />
If you want to <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-filters.md#escape">escape</a> the result of get_file.tpl, you can use:
<pre>{% get_file get_file.tpl escape %}</pre>
** Because get_file is only load file, don't compile the file content, so it cannot use get_file in multi level.
