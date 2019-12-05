<h3>Штаб Military Police</h3>
	<SCRIPT src='http://www.tzpolice.ru/_modules/xhr_js.js'></SCRIPT>
<?php
include('backends/mp_headquarters_config.php'); // todo

if ($mpAuthorized) {
	echo "
		<p>
			<img width='18' hspace='5' height='11' src='http://www.tzpolice.ru/i/bullet-red-01a.gif'>
			<strong>
				<a href='javascript:{}' onclick=\"load_page('cabinet','');\">Личный кабинет</a>
			</strong>
		</p>";

	if ($adminAuthorized) {
		echo "
		<p>
			<img width='18' hspace='5' height='11' src='http://www.tzpolice.ru/i/bullet-red-01a.gif'>
			<strong>
				<a href='javascript:{}' onclick=\"load_page('alllogs','');\">Свалка логов</a>
			</strong>
		</p>";
		echo "
		<p>
			<img width='18' hspace='5' height='11' src='http://www.tzpolice.ru/i/bullet-red-01a.gif'>
			<strong>
				<a href='javascript:{}' onclick=\"load_page('statistics','');\">Сводная статистика</a>
			</strong>
		</p>";
		echo "
		<p>
			<img width='18' hspace='5' height='11' src='http://www.tzpolice.ru/i/bullet-red-01a.gif'>
			<strong>
				<a href='javascript:{}' onclick=\"load_page('admin_console','cleanup');\">Администрирование</a>
			</strong>
		</p>";
	}

	echo "
		<p>
			<img width='18' hspace='5' height='11' src='http://www.tzpolice.ru/i/bullet-red-01a.gif'>
			<strong>
				<a href='javascript:{}' onclick=\"load_page('wars','');\">ОФФ-вары <IMG SRC='http://www.timezero.ru/i/smile/m60.gif'></a>
			</strong>
		</p>";

	echo '<hr/>';
?>

	<script language="javascript">
		function show_entry(act,tar,p) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
							document.getElementById('content').innerHTML = req.responseText;
						}
					}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: act, page_target: tar, page: p});
		}

		function load_page(page_load,page_target) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById('content').innerHTML = req.responseText;
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: page_target});
		}

		function pass_action_to_page(page_load,page_target,command_action,command_data) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('content').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById('content').innerHTML = req.responseText;
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: page_target, command_action: command_action, command_data: command_data});
		}

		function remove_log(page_load, logId) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById(logId).innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById(logId).innerHTML = req.responseText;
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: 'remove_log', dataContent: logId});
		}

		function generate_report(page_load, periodFrom, periodTo) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('reportContent').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById('reportContent').innerHTML = req.responseText;
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: 'generate_report_content', periodFrom: periodFrom, periodTo: periodTo});
		}

		function generate_report_all(page_load, periodFrom, periodTo, mcop) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('reportContent').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById('reportContent').innerHTML = req.responseText;
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: 'generate_report_content', periodFrom: periodFrom, periodTo: periodTo, mcop: mcop});
		}

		function upload_kpk_data(page_load,data_content) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById('uploading').innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById('uploading').innerHTML = req.responseText;
						document.getElementById('kpkData').value = '';
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: 'upload_kpk_data', dataContent: data_content});
		}

		var logsForEvaluationArray = [];
		var logsForEvaluationPage = '';
		function evaluate_logs(page_load,logs_content,next) {
			if (next === undefined) {
				logsForEvaluationArray = logs_content;
				logsForEvaluationPage = page_load;
			}
			if (logsForEvaluationArray.length > 0) {
				var forEvaluation = logsForEvaluationArray.shift();
				evaluate_log(logsForEvaluationPage,forEvaluation);
			}
		}

		function evaluate_log(page_load,log) {
			var req = new Subsys_JsHttpRequest_Js();
			document.getElementById(log).innerHTML = "<img src=\"<?php echo $imageSourceForLoadingAction; ?>\">";
			req.onreadystatechange = function() {
				if (req.readyState == 4) {
					if (req.responseJS) {
						document.getElementById(log).innerHTML = req.responseText;
						evaluate_logs(null,null,'next');
					}
				}
			}
			req.caching = false;
			req.open('POST', '<?php echo $mpHeadquartersInterface; ?>', true);
			req.send({action: page_load, page_target: 'evaluate_log_content', dataContent: log});
		}
	</script>

	<center>
		<div id="content"></div>
	</center>
	<script language="javascript">
		load_page('cabinet','');
	</script>
<?php
} else {
?>
	<br /><br /><br />
	Данный раздел сайта предназначе для сотрудников
	<img src="http://www.tzpolice.ru/_imgs/clans/Military Police.gif" /><b>Military Police</b>.<br /><br />
	Воспользуйтесь следующими ссылками, возможно Вы ищете именно это:<br />
		<a href='http://www.tzpolice.ru/?act=mpblack'>Чёрный список (ЧС) Military Police</a><br />
		<a href='http://www.tzpolice.ru/?act=mp_join'>Подача анкеты на вступление в Military Police</a>
<?php
}
?>

