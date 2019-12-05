
function editrec(idrec)
{
	var i = {}; 
	i.id = idrec;
	i.action = "geteditstr";
	i.module = $('#module').val();
	$.post("_modules/am_function.php", i, setrecord, "json");
}

function undorec(idrec)
{
	var i = {}; 
	i.id = idrec;
	i.action = "getstr";
	i.module = $('#module').val();
	$.post("_modules/am_function.php", i, setrecord, "json");
}

function saverec(idrec)
{
	var i = {}; 
	
	i.id = idrec;
	i.name = $('#'+idrec).find('#name').val();
	i.name_type = $('#'+idrec).find('#name_type').val();
	
	if ($('#'+idrec).find('#admin').attr("checked"))
	{
		i.admin = 1;
	} else {
		i.admin = 0;
	}
	
	i.action = "setrules";
	i.module = $('#module').val();

	i.rules = 0;

	var c = 1;

	$('#'+idrec).find('.rules').each(function(){
		if ($(this).attr("checked"))
		{
			i.rules = i.rules + c;
		}
		
		c = c * 2;
	});

	if (confirm("Сохранить изменения?")) {
		$.post("_modules/am_function.php", i, setrecord, "json");
	}

}

function addrec()
{
	var i = {}; 
	i.id = "new";
	i.action = "geteditstr";
	i.module = $('#module').val();
	$.post("_modules/am_function.php", i, setrecord, "json");
}

function setrecord(data)
{
	if (data.id == "new")
	{
		$('#access_record_new').attr('id', 'access_record_'+data.newid);
		$('#access_record_'+data.newid).html(data.result);
	} else {
		$('#access_record_'+data.id).html(data.result);
	}
	
	
}





$(document).ready(function() {


	$(".editbtn").live('click', (function() {
		editrec(this.parentNode.parentNode.id);
		return false;
	}));

	$(".undobtn").live('click', (function() {
		undorec(this.parentNode.parentNode.id);
		return false;
	}));

	$(".savebtn").live('click', (function() {
		saverec(this.parentNode.parentNode.id);
		//delrec(this.id);
		return false;
	}));

	$(".addbtn").live('click', (function() {
		addrec();
		return false;
	}));

	$(".delbtn").live('click', (function() {
		alert("В разработке");
		//delrec(this.id);
		return false;
	}));






});
