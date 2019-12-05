<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
var currently_edit=0;
var saving=0;
var currently_togle_id=0;
var currently_togle_q=0;
var new_admin_form=0;

function close_drop(element, elem_q, next){
	if(elem_q!=0){
		document.getElementById(element+"_"+next).style.display='none'; 
	}
	if(next==elem_q){ 
		document.getElementById(element+"_new").style.display='none';
	}
	if(elem_q==0){ 
		document.getElementById(element+"_new").style.display='none'; 
	}
	next++;
	setTimeout("close_drop('"+element+"', "+elem_q+", "+next+")", 1);
}

function togle(element, elem_q, next) {
	if((currently_togle_id!=0)&&(currently_togle_id!=element)){
		close_drop(currently_togle_id,currently_togle_q,1);
	}
	if(next<=elem_q){
		if(document.getElementById(element+"_"+next).style.display==''){ 
			document.getElementById(element+"_"+next).style.display='none'; 
		} else {
			document.getElementById(element+"_"+next).style.display='';
		}
		if(next==elem_q){ 
			if(document.getElementById(element+"_new").style.display==''){ 
				document.getElementById(element+"_new").style.display='none';
				currently_togle_id=0;
				currently_togle_q=0; 

			} else {
				document.getElementById(element+"_new").style.display=''; 
				currently_togle_id=element;
				currently_togle_q=elem_q;

			}
		}
		next++;
		setTimeout("togle('"+element+"', "+elem_q+", "+next+")", 1);
	}
	if(elem_q==0){ 
		if(document.getElementById(element+"_new").style.display==''){ 
			document.getElementById(element+"_new").style.display='none'; 
			currently_togle_id=0;
			currently_togle_q=0;
		} else {
			document.getElementById(element+"_new").style.display=''; 
			currently_togle_id=element;
			currently_togle_q=0;
		}
	}
}

function list_ld(t) {
	if(saving==0){
		document.getElementById('edit_area').innerHTML = "";
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('content').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
				{
					if (req.responseJS) {
						currently_togle_id=0;
						document.getElementById('content').innerHTML = req.responseText;
					}
				}
		}
		req.caching = false;
		req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
		req.send({action: 'list', type: t});	
		currently_edit=0;
	}
}

function edit_ld(id){
	if(saving==0){
		if(currently_edit!=0){
			cancel_edit(currently_edit,1);
		}
		currently_edit=id;
		var ds=document.getElementById(id+'_cr_descr_short').value;
		var cl=document.getElementById(id+'_cr_clan').value;
		var cl_s="<select id='"+id+"_new_clan'><option value='police'";
		if(cl=="police"){cl_s=cl_s+" selected";}
		cl_s=cl_s+">police</option><option value='pa'";
		if(cl=="pa"){cl_s=cl_s+" selected";}
		cl_s=cl_s+">pa</option><option value='mp'";
		if(cl=="mp"){cl_s=cl_s+" selected";}
		cl_s=cl_s+">mp</option><option value='archive'";
		if(cl=="archive"){cl_s=cl_s+" selected";}
		cl_s=cl_s+">archive</option></select>";
		document.getElementById(id+'_descr_short').innerHTML="<input type='text' id='"+id+"_new_sd' size='92' value='"+ds+"'>";
		document.getElementById(id+'_edit_btn').innerHTML="<a href='#; return false;' onclick=\"save('"+id+"');\"><img src='_modules/inviz/img/ok.gif' border=0></a>&nbsp;<a href='#; return false;' onclick=\"cancel_edit('"+id+"',0);\"><img src='_modules/inviz/img/cancel.gif' border=0></a>";
		document.getElementById(id+'_descr').style.display="";
		document.getElementById(id+'_clan').innerHTML=cl_s;
	}
}
function cancel_edit(id,type){
	document.getElementById(id+'_descr').style.display="none";
	var ds=document.getElementById(id+'_cr_descr_short').value;
	var cl=document.getElementById(id+'_cr_clan').value;
	document.getElementById(id+'_descr_short').innerHTML=ds;
	document.getElementById(id+'_clan').innerHTML="<img border=0 src='_imgs/clansld/"+cl+".gif'>";
	document.getElementById(id+'_edit_btn').innerHTML="<a href='#; return false;' onclick=\"if(saving==0){edit_ld('"+id+"');}\"><img border=0 src='_imgs/edit.gif'></a>";
	if(type==0){currently_edit=0;}
}
function save(id) {
	saving=1;
	var sd=document.getElementById(id+'_new_sd').value;
	var c=document.getElementById(id+'_new_clan').value;
	var df=document.getElementById(id+'_new_descr').value;
	var i=document.getElementById(id+'_id').value;
	
	document.getElementById(id+'_new_sd').disabled="disabled";
	document.getElementById(id+'_new_clan').disabled="disabled";
	document.getElementById(id+'_new_descr').disabled="disabled";
	document.getElementById(id+'_edit_btn').innerHTML="<img src=\"_modules/inviz/progress.gif\">";
	var req = new Subsys_JsHttpRequest_Js();
	//document.getElementById('content').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					//document.getElementById('content').innerHTML = req.responseText;
					saving=0;
					list_ld('norm');
					
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
	req.send({action: 'save', clan: c, descr_short: sd, descr_full: df, uid: i});		
}
function new_entry(id) {
	saving=1;
	var ne=document.getElementById(id+'_new_entry').value;
	var i=document.getElementById(id+'_id').value;
	
	document.getElementById(id+'_new_entry').disabled="disabled";
	document.getElementById(id+'_new_btn').disabled="disabled";
	document.getElementById(id+'_edit_btn').innerHTML="<img src=\"_modules/inviz/progress.gif\">";
	var req = new Subsys_JsHttpRequest_Js();
	//document.getElementById('content').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					//document.getElementById('content').innerHTML = req.responseText;
					saving=0;
					list_ld('norm');
					
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
	req.send({action: 'add', new_descr: ne, uid: i});	
	currently_edit=0;
}
function new_ld_form() {
	if(saving==0){
		if(document.getElementById("new_ld_tr").style.display=='none'){
			document.getElementById("new_ld_tr").style.display=''; 
			document.getElementById('edit_area').innerHTML = "";
		} else {
			document.getElementById("new_ld_tr").style.display='none'; 
		}
	}
}
function new_ld_dave(id) {
	saving=1;
	var ne=document.getElementById('new_ld_nick').value;
	
	document.getElementById('new_ld_nick').disabled="disabled";
	document.getElementById('new_ld_btn').disabled="disabled";
	document.getElementById('new_ld_load').innerHTML="<img src=\"_modules/inviz/progress.gif\">";
	var req = new Subsys_JsHttpRequest_Js();
	//document.getElementById('content').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					//document.getElementById('content').innerHTML = req.responseText;
					document.getElementById("new_ld_tr").style.display='none'; 
					document.getElementById('new_ld_load').innerHTML="";
					document.getElementById('new_ld_nick').disabled="";
					document.getElementById('new_ld_btn').disabled="";
					saving=0;
					list_ld('norm');
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
	req.send({action: 'new_ld', nick: ne});	
	currently_edit=0;
}

function show_admins() {
	if(saving==0){
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('content').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
				{
					if (req.responseJS) {
						document.getElementById('content').innerHTML = req.responseText;
					}
				}
		}
		req.caching = false;
		req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
		req.send({action: 'show_admins'});	
	}
}
function admin_form() {
	if(saving==0){
		if(new_admin_form==0){
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('edit_area').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
					{
						if (req.responseJS) {
							document.getElementById('edit_area').innerHTML = req.responseText;
							new_admin_form=1;
						}
					}
			}
			req.caching = false;
			req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
			req.send({action: 'admin_form'});	
		} else {
			document.getElementById('edit_area').innerHTML = "";
			new_admin_form=0;
		}
	}
}
function admin_add(){
	saving=1;
	var n=document.getElementById('na_nick').value;
	var p=document.getElementById('na_police').value;
	var npa=document.getElementById('na_pa').value;
	var nmp=document.getElementById('na_mp').value;
	var a=document.getElementById('na_admin').value;
	
	document.getElementById('na_nick').disabled="disabled";
	document.getElementById('na_police').disabled="disabled";
	document.getElementById('na_pa').disabled="disabled";
	document.getElementById('na_mp').disabled="disabled";
	document.getElementById('na_admin').disabled="disabled";
	document.getElementById('admin_add_btn').disabled="disabled";
	document.getElementById('waiting').innerHTML="<img src=\"_modules/inviz/progress.gif\">";
	
	var req = new Subsys_JsHttpRequest_Js();
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					saving=0;
					document.getElementById('edit_area').innerHTML = "";
					show_admins();
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
	req.send({action: 'add_admin', nick: n, police: p, pa: npa, mp: nmp, admin: a});	
}
function exec_checked(id){
	if(document.getElementById(id).checked==true){
		document.getElementById(id).value="1";
	} else {
		document.getElementById(id).value="0";
	}
}
function edit_admin(n) {
	if(saving==0){
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('edit_area').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
				{
					if (req.responseJS) {
						document.getElementById('edit_area').innerHTML = req.responseText;
					}
				}
		}
		req.caching = false;
		req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
		req.send({action: 'edit_admin', nick: n});	
	}
}
function admin_save(){
	saving=1;
	var n=document.getElementById('na_nick').value;
	var p=document.getElementById('na_police').value;
	var npa=document.getElementById('na_pa').value;
	var nmp=document.getElementById('na_mp').value;
	var a=document.getElementById('na_admin').value;
	
	document.getElementById('na_nick').disabled="disabled";
	document.getElementById('na_police').disabled="disabled";
	document.getElementById('na_pa').disabled="disabled";
	document.getElementById('na_mp').disabled="disabled";
	document.getElementById('na_admin').disabled="disabled";
	document.getElementById('admin_add_btn').disabled="disabled";
	document.getElementById('waiting').innerHTML="<img src=\"_modules/inviz/progress.gif\">";
	
	var req = new Subsys_JsHttpRequest_Js();
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					saving=0;
					document.getElementById('edit_area').innerHTML = "";
					show_admins();
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
	req.send({action: 'save_admin', nick: n, police: p, pa: npa, mp: nmp, admin: a});	
}
function delete_admin(n) {
	if(saving==0){
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('edit_area').innerHTML = "<center><img src=\"_modules/inviz/loading.gif\"></center>";
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
				{
					if (req.responseJS) {
						document.getElementById('edit_area').innerHTML = "";
						show_admins();
					}
				}
		}
		req.caching = false;
		req.open('POST', '_modules/inviz/backends/ld_functions.php', true);
		req.send({action: 'delete_admin', nick: n});	
	}
}
//!-->
</SCRIPT>
<H1>Личные дела сотрудников полицейского департамента</H1>
<?php
$SQL="SELECT admin FROM police_ld_users where nick='".AuthUserName."'";
$r=mysql_query($SQL);
$d=mysql_fetch_array($r);
$ex= mysql_num_rows($r);
if($ex>0 || AuthUserGroup=='100') {
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="33%"><a href="#; return false;" onclick="new_ld_form();">Добавить новое личное дело</a></td>
			<td width="33%"><?php if($d['admin']==1 || AuthUserGroup=='100'){ ?><a href="#; return false;" onclick="show_admins();">Управление доступом</a><?php } ?></td>
		</tr>
		<tr id="new_ld_tr" style="display:none;" height="20" valign="top">
			<td width="33%">Ник: <input type="text" id="new_ld_nick" size="30" /><input type="button" id="new_ld_btn" value="OK" onclick="new_ld_dave();" /><span id="new_ld_load"></span></td>
			<td width="33%"></td>
		</tr>
		<tr>
			<td width="33%"><a href="#; return false;" onclick="if(saving==0){list_ld('norm');}">Текущие дела</a></td>
			<td width="33%"><a href="#; return false;" onclick="if(saving==0){list_ld('archive');}">Архив</a></td>
		</tr>
	</table>
	<?php 
}
?>
<div id="edit_area"></div>
<div id="content"></div>

<script language="javascript">list_ld('norm');</script>