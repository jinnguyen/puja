{% extends master.tpl %}
{% block body %}
if..elseif..else..endif:

	{% if var in array and c in d %}
		var in and c in d
	{% elseif e in f %}
		e in f
	{% else %}
		false
	{% endif %}

for..empty..endfor

	{% for cat in cat_list %}
		|_ {{ cat.name }}
		{% for news in cat.news %}
			|_ {{ news.name }}
		{% empty %}
			|_ No news
		{% endfor %}
	{% endfor %}
{% endblock %}