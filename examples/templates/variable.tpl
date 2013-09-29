{% extends master.tpl %}
{% block body %}
	Simple: {{ username }}
	Element of array: {{ user.name }}
	Special variable( = user.age ): {{ user.{$ special_var $} }}
	Set variable: {% set my_name = 'Puja' my_age=20 %} => show value: {{ my_name }} - {{ my_age }}
{% endblock %}