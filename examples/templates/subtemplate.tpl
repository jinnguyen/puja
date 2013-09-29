{% extends master.tpl %}
{% block body %}
- include file without variable:
{% include include.tpl %}

- include file with variable:
{% include include.tpl username="Jin" %}

- get_file:
{% get_file include.tpl %}

- get_file with escape:
{% get_file include.tpl escape %}
{% endblock %}