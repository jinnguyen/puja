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
See <a href="https://github.com/jinnguyen/puja/edit/master/docs#template-inheritance">Template inheritance</a> for more information.<br />

<a name="block-endblock"></a>
- <strong>block..endblock</strong>:<br />
See <a href="https://github.com/jinnguyen/puja/edit/master/docs#template-inheritance">Template inheritance</a> for more information.<br />

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
See <a href="https://github.com/jinnguyen/puja/edit/master/docs#include">Including other Templates</a> for more information.<br />

<a name="get_file"></a>
- <strong>get_file</strong>:<br />
See <a href="https://github.com/jinnguyen/puja/edit/master/docs#get_file">Including other Templates</a> for more information.<br />

