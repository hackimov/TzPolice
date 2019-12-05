<?php

$join_access=0;
$join_admin=0;

if(AuthUserGroup == '100'){$join_admin=1;}
if (abs(AccessLevel) & AccessJoinAdmin){$join_admin=1;}

if(AuthUserGroup == '100'){$join_access=1;}
if (abs(AccessLevel) & AccessJoinModer){$join_access=1;}
#echo "($join_admin,$join_access)";
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<script language="javascript">

function load_page(page_load,page_target) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
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
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: page_load,target: page_target});
}

function togle_pa_info(){
	if(document.getElementById('pa_info').style.display=="none"){
		document.getElementById('pa_info').style.display="";
	} else {
		document.getElementById('pa_info').style.display="none";
	}
}
function set_radio(id,val){
	document.getElementById(id).value=val;
}
<?php if(($join_access==1)||($join_admin==1)){ ?>
function show_entry(act,tar,par,p) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					if(act=="get_entry"){
						show_entry('lists','free','','1');
					} else {
						document.getElementById('content').innerHTML = req.responseText;
					}
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: act,target: tar, param: par, page: p});
}
function take_archive(par) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
//					if(act=="get_entry"){
						show_entry('lists','my','','1');
//					} else {
//						document.getElementById('content').innerHTML = req.responseText;
//					}
				}
			}
	}
	req.caching = false;
//	alert (par);
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'take_arch', param: par});
}
function show_search(){
	if(document.getElementById('search_form').style.display==""){
		document.getElementById('search_form').style.display="none";
	} else {
		document.getElementById('search_form').style.display="";
	}
}
function show_reject(){
	if(document.getElementById('reject_form').style.display==""){
		document.getElementById('reject_form').style.display="none";
	} else {
		document.getElementById('reject_form').style.display="";
	}
}
function search_entry(){
	var n=document.getElementById('serach_nick').value;
	show_entry('lists','search',n,'');
}

function search_fio(){
	var n=document.getElementById('serach_fio').value;
	show_entry('lists','search_fio',n,'');
}

function set_status(entry,st,com){
	var comm="";
	if(com==1){
		comm=document.getElementById('comment_text').value;
	}
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					if(st==6){
						show_entry('lists','free','','1');
					} else {
						show_entry('show_entry','',entry,'1');
					}
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'set_status', id: entry, status: st, comment: comm});
}
function reload_user_nick_data(i){
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('user_nick_data_img').innerHTML = "<img src=\"_modules/inviz/progress.gif\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					if(req.responseText!=0){
						document.getElementById('user_nick_data').innerHTML = req.responseText;
						document.getElementById('user_nick_data_img').innerHTML = "<a href=\"javascript:{}\" onclick=\"reload_user_nick_data('"+i+"');\" />[обновить]</a>";
					} else {
						document.getElementById('user_nick_data_img').innerHTML = "<a href=\"javascript:{}\" onclick=\"reload_user_nick_data('"+i+"');\" />[обновить]</a>";
					}
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'reload_user_nick_data', id: i, hide_menu: '1'});
}
<?php } ?>

<?php if($join_admin==1){ ?>
function add_question(new_q){
	var t_id=0;
	if(new_q==1){var tar="save_new";}
	if(new_q==0){
		var tar="save_changes";
		t_id=document.getElementById('target_id').value;
	}
	var n=document.getElementById('q_num').value;
	var t=document.getElementById('q_txt').value;
	var ft=document.getElementById('field_type').value;
	var fo=document.getElementById('field_options').value;
	if(n.length>0){
		if(t.length>0){
			document.getElementById('add_q_btn').disabled=true;
			document.getElementById('add_q_btn').value="  подождите  ";
			var req = new Subsys_JsHttpRequest_Js();
			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
					{
						if (req.responseJS) {
							if((req.responseText==0)||((new_q==0)&&(t_id==n))){
								var req2 = new Subsys_JsHttpRequest_Js();
								req2.onreadystatechange = function()
								{
									if (req2.readyState == 4)
										{
											if (req2.responseJS) {
												load_page('edit_questions','show');
											}
										}
								}
								req2.caching = false;
								req2.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
								req2.send({action: 'edit_questions', target: tar, q_num: n, q_txt: t, target_id: t_id, field_type: ft, field_options: fo});
							} else {
								window.alert("Вопрос с таким номером уже существует.");
								document.getElementById('add_q_btn').disabled=false;
								if(new_q==1){ document.getElementById('add_q_btn').value="  добавить  "; }
								if(new_q==2){ document.getElementById('add_q_btn').value="  сохранить  "; }
							}
						}
					}
			}
			req.caching = false;
			req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
			req.send({action: 'edit_questions', target: 'check_q_ex', q_num: n, hide_menu: '1'});
		} else {
			window.alert("Введите текст вопроса.");
		}
	} else {
		window.alert("Введите номер вопроса.");
	}
}
function delete_question(str) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					load_page('edit_questions','show');
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'edit_questions', target: 'delete', q_id: str});
}
function edit_question(str) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"_modules/inviz/loading.gif\">";
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
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'edit_questions', target: 'edit', q_id: str});
}
function togle_dept_status(i,a,n){
	if(a==0){var act="delete";}
	if(a==1){var act="insert";}
	var req = new Subsys_JsHttpRequest_Js();
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					var req2 = new Subsys_JsHttpRequest_Js();
					req2.onreadystatechange = function()
					{
						if (req2.readyState == 4)
							{
								if (req2.responseJS) {
									document.getElementById('content').innerHTML = req2.responseText;
								}
							}
					}
					req2.caching = false;
					req2.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
					req2.send({action: 'edit_questions',target: 'show'});
				}
			}
	}
	req.caching = false;
	req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
	req.send({action: 'edit_questions', target: 'depts', make: act, id: i, name: n});
}
<?php }
if(AuthStatus==1){
?>
function minimum_agreement(){
	if(document.getElementById('minimum_agreement').checked==true){
		document.getElementById('add_btn').disabled=false;
	} else {
		document.getElementById('add_btn').disabled=true;
	}
}
function add_entry(){
	var error=0;
	var answ="";
	<?php
	$SQL="SELECT * FROM police_join_questions order by id";
	$r=mysql_query($SQL);
	while($d=mysql_fetch_assoc($r)) {
		extract($d,EXTR_PREFIX_ALL,"d");
		echo "var a_".$d['id']."=document.getElementById('answer_".$d['id']."').value;\n";
		echo "if(a_".$d['id'].".length==0){error=1;}\n";
		if($d['id']>1) { echo "answ+='@#$%^&*next@#$%^&*answer@#$%^&*';"; }
		echo "answ+=a_".$d['id'].";";
	}
	?>
	if(error==1) {
		window.alert("Вы ответили не на все вопросы.");
	} else {
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('add_btn').value="  подождите  ";
		document.getElementById('add_btn').disabled=true;
		document.getElementById('add_load').innerHTML = "<img src=\"_modules/inviz/progress.gif\">";
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
				{
					if (req.responseJS) {
						window.alert("Анкета принята, следите за статусом вашей заявки.");
						load_page('main_user','');
					}
				}
		}
		req.caching = false;
		req.open('POST', '_modules/inviz/backends/police_join_functions.php', true);
		req.send({action: 'add_entry', target: 'add', answers: answ});
	}
}
<?php
}
?>

</script>

<h1>Прием в полицию</h1>
<div align=right><a href="javascript:{}" onclick="load_page('main_user','');">Условия вступления</a> | <a href="javascript:{}" onclick="load_page('add_entry','form');">Подать заявку</a></div>
<center>
<div id="content"></div>
</center>
<script language="javascript">
load_page('main_user','');
</script>