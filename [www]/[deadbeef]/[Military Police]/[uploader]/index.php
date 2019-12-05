<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>:: Фотогалерея - загрузчик ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link href="img_uploader/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="img_uploader/swfupload.js"></script>
<script type="text/javascript" src="img_uploader/js/swfupload.queue.js"></script>
<script type="text/javascript" src="img_uploader/js/fileprogress.js"></script>
<script type="text/javascript" src="img_uploader/js/handlers.js"></script>
<script type="text/javascript">
		var swfu;

		window.onload = function() {
			var settings = {
				flash_url : "/deadbeef/uploader/img_uploader/swfupload.swf",
				upload_url: "/deadbeef/uploader/img_uploader/upload.php",	// Relative to the SWF file
				post_params: {"PHPSESSID" : "<?php echo session_id(); ?>"},
				file_size_limit : "2 MB",
				file_types : "*.jpeg; *.jpg; *.gif; *.png",
				file_types_description : "Картинки",
				file_upload_limit : 100,
				file_queue_limit : 100,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
//				button_image_url : "http://www.tzpolice.ru/deadbeef/uploader/img_uploader/images/view.png",	// Relative to the SWF file
				button_placeholder_id : "spanButtonPlaceHolder",
				button_width: 190,
				button_height: 36,
				button_text : '<span class="button">Выберите фотографии<br><font size="-1">(до 2MB каждая)</font></span>',
				button_text_style : '.button {text-align: center; font-family: Arial, Helvetica, sans-serif; height:30px; border-color:#c2e254 #9bb838 #9bb838 #c2e254; border-style:solid; border-width:1px; background:#c2e254; cursor:pointer;}',
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,

				// The event handler functions are defined in handlers.js
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete
//				queue_complete_handler : queueComplete	// Queue plugin event
			};

			swfu = new SWFUpload(settings);
	     };
	</script>
</head>
<body>
	<table border=0 cellspacing=5 cellpadding=5>
	<tr><td width=250>
	<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
			<table border=0 cellspacing=0 cellpadding=0>
            <tr><td align="right">Имя:&nbsp;</td><td><input style="width:125px;" type="text" id="f_name" name="f_name" /></td></tr>
            <tr><td align="right">Город:&nbsp;</td><td><input style="width:125px;" type="text" id="city" name="city" /></td></tr>
            <tr><td align="right">Возраст:&nbsp;</td><td><input style="width:125px;" type="text" id="age" name="age" /></td></tr>
            <tr><td align="right">Комментарий:&nbsp;</td><td><input style="width:125px;" type="text" id="comment" name="comment" /></td></tr>                                    
            <tr><td colspan="2" style="border: solid 2px #5e3e00; background-color: #ffe1a6; padding: 0px; margin: 0px;" align="center" valign="middle">
					<span id="spanButtonPlaceHolder"></span>
				</div></td></tr>
            <tr><td><input id="btnGo" type="button" value="Начать загрузку" onclick="initiateUpload();" disabled="disabled" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; padding: 5px; height: 39px; width: 125px;" /></td><td align="right">
				<input id="btnCancel" type="button" value="Отменить все загрузки" onclick="swfu.cancelQueue();" disabled="disabled" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; padding: 5px; height: 39px; width: 125px;" />
			</td></tr>
            </table>
            <br />
			<div class="fieldset flash" id="fsUploadProgress">
			<b>Очередь загрузки</b>
			</div>
	</form>
	</td><td><div id="thumbnails"></div></td></tr></table>
</body>
</html>
