{% extends master.tpl %}
{% block body %}
Custom filters:
	normal: {{ name }} => filter: {{ name|urlize:10 }}
	normal: {{ file_name }} => filter: {{ file_name|ext }}
Custom tags:
	{% js_tag jquery.js v=1 %}
	{% css_tag style.css %}
{% endblock %}