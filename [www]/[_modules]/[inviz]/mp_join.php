<?php
require_once('backends/mp_join_config.php'); // todo - actualize

$imageSourceForLoadingAction = $siteLocation.'_modules/inviz/loading.gif';
$imageSourceForProgressLoadingAction = $siteLocation.'_modules/inviz/progress.gif';

$mp_join_functions = "_modules/inviz/backends/mp_join_functions.php"; // todo - comment
//$mp_join_functions = "_modules/inviz/backends/mp_join_functions.php"; // todo restore

?>
<SCRIPT src='http://www.tzpolice.ru/_modules/xhr_js.js'></SCRIPT> <!-- todo -->
<script language="javascript">
function load_page(page_load,page_target) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">"; //todo
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: page_load,target: page_target});
}
function set_radio(id,val){
	document.getElementById(id).value=val;
}

<?php if($join_access){ ?>
function generateStatistics(from, to) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'statistics',target: 'show', statisticsStart: from,statisticsEnd: to});
}

function show_entry(act,tar,par,p) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: act,target: tar, param: par, page: p});
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
function show_comment(){
	if(document.getElementById('comment_form').style.display==""){
		document.getElementById('comment_form').style.display="none";
	} else {
		document.getElementById('comment_form').style.display="";
	}
}
function search_entry(){
	var n=document.getElementById('serach_nick').value;
	show_entry('lists','search',n,'');
}
function set_status(entry,st,com){
	var comm="";
	if(com==1){
		comm=document.getElementById('comment_text').value;
	}
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'set_status', id: entry, status: st, comment: comm});
}

function add_comment(entry,com){
	var comm="";
	if(com==1){
		comm=document.getElementById('comment_text_area').value;
	}
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					show_entry('show_entry','',entry,'1');
				}
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'add_comment', id: entry, comment: comm});
}

function reload_user_nick_data(i){
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('user_nick_data_img').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'reload_user_nick_data', id: i, hide_menu: '1'});
}
<?php } ?>

<?php if($join_admin){ ?>
function change_access(id, okAccess, okAdmin) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					load_page('edit_security','show');
				}
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'edit_security', target: 'changeAccess', target_id: id, okAccess: (okAccess ? '1' : '0'), okAdmin: (okAdmin ? '1' : '0')});
}

function delete_access(id) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					load_page('edit_security','show');
				}
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'edit_security', target: 'deleteAccess', target_id: id});
}

function actualy_dissmiss(id,reason) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				if (req.responseJS) {
					load_page('show_actualy','show');
				}
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'show_actualy', target: 'dissmiss', target_id: id, reason: reason});
}

function actualy_del_comment(id,nick) {	var reason = prompt("Уволить "+nick+" из рядов МП? Введите причину, или если не уверены, нажмите `отмена`","");
    if(reason && id) {    	actualy_dissmiss(id,reason);
    }

}

function add_access(nick) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
			{
				document.getElementById('content').innerHTML = req.responseText;
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'edit_security', target: 'addAccess', targetNick: nick});
}

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
								req2.open('POST', '<?php echo $mp_join_functions; ?>', true);
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
			req.open('POST', '<?php echo $mp_join_functions; ?>', true);
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
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
	req.send({action: 'edit_questions', target: 'delete', q_id: str});
}
function edit_question(str) {
	var req = new Subsys_JsHttpRequest_Js();
	document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
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
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
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
					req2.open('POST', '<?php echo $mp_join_functions; ?>', true);
					req2.send({action: 'edit_questions',target: 'show'});
				}
			}
	}
	req.caching = false;
	req.open('POST', '<?php echo $mp_join_functions; ?>', true);
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
	$SQL="SELECT * FROM mp_join_questions order by id";
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
		window.alert("Вы ответели не на все вопросы.");
	} else {
		var req = new Subsys_JsHttpRequest_Js();
		document.getElementById('add_btn').value="  подождите  ";
		document.getElementById('add_btn').disabled=true;
		document.getElementById('add_load').innerHTML = "<img src=\"<?php echo $imageSourceForProgressLoadingAction; ?>\">";
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
		req.open('POST', '<?php echo $mp_join_functions; ?>', true);
		req.send({action: 'add_entry', target: 'add', answers: answ});
	}
}
<?php
}
?>















</script>

<h1>Вступление в Military Police</h1>
<center>
<div id="content"></div>
</center>
<script language="javascript">
load_page('main_user','');
</script>