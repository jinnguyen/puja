Coding Standards
=======

When writing Puja templates, we recommend you to follow these official coding standards:

- Put one (and only one) space after the start of a delimiter ({{, {%, and {#) and before the end of a delimiter (}}, %}, and #}):
{{ foo }}
<pre>
{# comment content #}
{% if foo %}do somthing{% endif %}
</pre>
- Put one (and only one) space before and after the following operators: comparison operators (==, !=, <, >, >=, <= , !), math operators (+, -, /, *, %), logic operators (not, and, or), ~, is, in:
<pre>{{ 1 + 2 }}
{{ a or b }}</pre>

- Do not put any spaces after an opening parenthesis and before a closing parenthesis in expressions:
<pre>{{ 1 + (2 * 3) }}</pre>

- Do not put any spaces before and after string delimiters:
<pre>{{ 'foo' }}
{{ "foo" }}</pre>

- Do not put any spaces before and after the following operators: |, .:
<pre>{{ foo|upper|lower }}
{{ user.name }}</pre>

- Use lower cased and underscored variable names:
<pre>{% set foo = 'foo' %}
{% set foo_bar = 'foo' %}</pre>

- Indent your code inside tags (use the same indentation as the one used for the main language of the file):
<pre>{% block foo %}
   {% if true %}
       true
   {% endif %}
{% endblock %}
</pre>
