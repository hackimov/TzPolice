function geteventbox(levent)
{
	var i = {}; 
	i.levent = levent;
	$.post("http://tzpolice.ru/_modules/history/geteventbox.php", i, function(data){
		$('#details').html(data);
	}).error(function() { alert("«агрузить таблицу настроек не удалось"); });	
};



$(document).ready(function() {

	$("#ltype").bind('select', function() {
		levent = $('#ltype').val();
		geteventbox(event);
		return false;
	});
});
