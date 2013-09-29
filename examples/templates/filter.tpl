{% extends master.tpl %}
{% block body %}
	normal: {{ name }} => filter: {{ name|upper }}
	normal: {{ date_join }} => filter: {{ date_join|date:"d/m/Y" }}
{% endblock %}