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
				$('#list_file').html('');
				$(data.list_file).each(function(k,v){
					$('#list_file').append('<p rel="'+v+'" class="source">'+v+'<span>Loading...</span></p>');
				})	
			}else{
				$('#list_file').html(data.msg);
			}
			
			$('.source').each(function(){
				var file = $(this).attr('rel');
				var obj = this;
				$.get('build_compress',{'src':file,'folder':data.folder},function(data){
					$(obj).find('span').html('OK');
				})
			})
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