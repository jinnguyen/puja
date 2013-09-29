{% extends master.tpl %}
{% block javascript %}
<script type="text/javascript" src="templates/jquery-1.3.2.min.js"></script>
<script>
	function checkCompress(doc){
		if(doc.folder.value==''){
			console.log('---')
			alert('Please enter the target folder');
			return false;
		}
		$.post('build_compress.php',$(doc).serialize(),function(data){
			if(data.status){
				$(data.list_file).each(function(k,v){
					$('#list_file').append('<p>'+v+'</p>');
				})	
			}else{
				$('#list_file').html(data.msg);
			}
		},'json')
		return false;
	}
</script>
{% endblock %}
{% block master %}
<form name="export" method="post" onsubmit="return checkCompress(this);">
	Target: <br />
	{{ target }}{{ directory_separator }}<input name="folder" value="cache" />{{ directory_separator }}<br />
	<input type="submit" value="Compress &gt;&gt;">
	<div id="list_file"></div>
</form>
{% endblock %}