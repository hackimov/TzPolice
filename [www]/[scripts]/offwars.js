var cliplist = new ZeroClipboard.Client();

function showmembers(e, divid)
{
	if ($("#d"+divid).is(":visible") == false)
	{
		$("#d"+divid).css({
top:e.pageY+'px',
left:e.pageX+10+'px'
		});
		$("#d"+divid).show(200);
	} else {
		$("#d"+divid).hide(200);
	}

}

function showsettings() {
	
	if ($("#settings").is(":hidden")) {
		$("#settings").slideDown(400);
		$("#turnsettings").html("Скрыть дополнительные настройки списка...");
	} else {
		$("#settings").slideUp(400);
		$("#turnsettings").html("Раскрыть дополнительные настройки списка...");
	}
	
	setTimeout( clipreposition, 400 );
	
}

function setactive(id)
{
	
	$(".activetab").toggleClass("tab");
	$(".activetab").toggleClass("activetab");
	$("#"+id).toggleClass("tab");
	$("#"+id).toggleClass("activetab");

	var i = {}; 
	i.dataset = id;
	$.post("_modules/offwars.php", i, setmaimdata);

}

function setmaimdata(data)
{
	$('#main').html(data);
}

function showhistory()
{
	var i = {}; 
	i.dataset = "history_level2";
	i.start = $('#start').val();
	i.finish = $('#finish').val();
	i.clan = $('#clan').val();
	$.post("_modules/offwars.php", i, sethistory);

}

function sethistory(data)
{
	$('#hlevel').html(data);
}

function getlist()
{
	var i = {}; 
	i.dataset = "lists_level2";
	i.clan = $("#clanselect :selected").val();
	i.level = $("#levelselect :selected").val();
	i.pvprang = $("#pvpselect :selected").val();
	i.levelfrom = $("#levelfrom :selected").val();
	i.levelto = $("#levelto :selected").val();
	i.ratingfrom = $("#ratingfrom :selected").val();
	i.ratingto = $("#ratingto :selected").val(); 
	i.notingame = $('#notingame').val();
	
	i.uselevel = $('#cbvp').is(':checked');
	i.userating = $('#cbrt').is(':checked');
	i.usetime = $('#cbtime').is(':checked');
	
	$.post("_modules/offwars.php", i, setlist);

}

function setlist(data)
{
	$('#llevel').html(data);
	cliplist.glue('copylist');
	
}

function filterset(active, id)
{
	$("#"+id).toggleClass("lfilter");
	$("#"+id).toggleClass("lfilterda");
	
	$("#"+id+" *").attr("disabled", !active);

}

function showitem(id) {
	
	var n = id.substr(3,id.length-1);
	
	if ($("#"+id).is(':checked')) {
		$("#item"+n).addClass("rowlist");
		$("#item"+n).removeClass("rowhide");
	} else {
		$("#item"+n).addClass("rowhide");
		$("#item"+n).removeClass("rowlist");
	}
		
}

function settext() {
	var text = $(".rowlist").text();
	cliplist.setText(text);
}

function clipreposition() {
	cliplist.reposition();
}

function savesubscr() {
	
	var i = {}; 
	i.dataset = "subscribe_level2";
	i.c1 = $('#cbdw').is(':checked');
	i.c2 = $('#cbsw').is(':checked');
	i.c3 = $('#cbfw').is(':checked');
	
	$.post("_modules/offwars.php", i, subscranswer);

}

function subscranswer(data)
{
	alert(data);
}

$(document).ready(function() {

	setactive("curwar");
	
	$(".tab").live('click', (function() {
		
		if (this.id == 'lists') {
			cliplist.show();	
		} else {
			cliplist.hide();
		}
		
		setactive(this.id);
		return false;
	}));
	
	$(".amembers").live('click', (function(e) {
		showmembers(e, this.id);
		return false;
	}));

	$("#filterbtn").live('click', (function() {
		showhistory();
		return false;
	}));
	
	$("#turnsettings").live('click', (function() {
		showsettings();
		return false;
	}));
	$("#cbvp").live('click', (function() {
		filterset($("#cbvp").attr("checked"), "setvp");
	}));
	
	$("#cbrt").live('click', (function() {
		filterset($("#cbrt").attr("checked"), "setrt");
	}));
	
	$("#cbtime").live('click', (function() {
		filterset($("#cbtime").attr("checked"), "settime");
	}));
	
	$("#clanselect").live('change', (function() {
		var q = "https://www.timezero.ru/i/clans/"+$("#clanselect :selected").val()+".gif";
		$("#clanimg").attr("src",q);
		return false;
	}));
	
	$("#levelselect").live('change', (function() {
		var q = parseInt($("#levelselect :selected").val())-2;
		var w = q+2;
		if (q < 1) q = 1;
		
		$("#levelfrom [value='"+q+"']").attr("selected", "selected");
		$("#levelto [value='"+w+"']").attr("selected", "selected");
		$("#vptext").html(q+" до "+w);
		return false;
	}));
	
	$("#pvpselect").live('change', (function() {
		var pvprang = parseInt($("#pvpselect :selected").val()) + 1;
		var q = "https://www.timezero.ru/i/rank/"+pvprang+".gif";
		$("#pvpimg").attr("src",q);
		return false;
	}));
	
	$("#show").live('click', (function() {
		getlist();
		return false;
	}));
	
	$("#savesubscr").live('click', (function() {
		savesubscr();
		return false;
	}));
	
	$("#unsubscr").live('click', (function() {
		
		if (confirm("После отмены вы не будете получать уведомления о военных событиях вашего клана. Отменить подписку?")) {
			$('#cbdw').removeAttr("checked");
			$('#cbsw').removeAttr("checked");
			$('#cbfw').removeAttr("checked");
			savesubscr();
		}
		
		return false;
		
	}));	
	
	$(".cblist").live('click', (function() {
		showitem(this.id);
	}));
	
	cliplist.addEventListener( 'complete', function(client, text) {
		alert("Список успешно скопирован в буфер обмена!");
	} );
	
	$(window).resize(function(){
		clipreposition();
	});
	
	cliplist.addEventListener( 'mouseDown', settext );

});
