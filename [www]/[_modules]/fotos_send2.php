<h1>Добавление фотографий</h1>
<?if(AuthUserName != '') {?>
<b>Требования:</b><br>
 - Фотография должна быть в формате JPG<br>
 - Размер файла не должен превышать 500кб<br>
 - Фотография не должна быть очень маленькой и не превышать разрешение 1024х768<br>
<br>Фотографии появятся на сайте <b>только после проверки</b> модератором!!! Если вы добавите одну и ту же фотографию 72 раза - быстрее она не появится! Проверка может занимать до 12 часов (праздники, новый год и прочие стихийные бедствия).
<br>На сайте не появятся: слишком маленькие фото, пересвеченные, слишком тёмные фотографии, фотографии из категории 18+.
<hr><br>
<center>
<div style="font-size: 15px"><a href="http://www.tzpolice.ru/?act=data2&type=manuals&Id=123" target=_blank>Девушкам: как выглядеть на фото хорошо</a></div>
<br><br>
Если у Вас возникают трудности при использовании этого раздела - попробуйте <a href='/?act=fotos_send'>старую версию</a> загрузчика.<br>Для работы нужен браузер Firefox, IE или Opera с установленным Flash player версии 9 или выше.</span>
</center><br>
<hr>
<div style="font-size: 20px; color: red; text-decoration: blink;" align="center">Фотографии появятся на сайте <b>только после проверки</b> модератором!!!</div>
<script type="text/javascript" src="/_modules/backends/gallery_uploader/swfupload.js"></script>
<script type="text/javascript" src="/_modules/backends/gallery_uploader/js/swfupload.queue.js"></script>
<script type="text/javascript" src="/_modules/backends/gallery_uploader/js/fileprogress.js"></script>
<script type="text/javascript" src="/_modules/backends/gallery_uploader/js/handlers.js"></script>
<script type="text/javascript">
		var swfu;
		window.onload = function() {
			var settings = {
				flash_url : "/_modules/backends/gallery_uploader/swfupload.swf",
				upload_url: "/_modules/backends/gallery_uploader/upload.php",
				file_size_limit : "500 KB",
				file_types : "*.jpeg; *.jpg; *.gif; *.png",
				file_types_description : "Картинки",
				file_upload_limit : 100,
				file_queue_limit : 100,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				button_placeholder_id : "spanButtonPlaceHolder",
				button_width: 190,
				button_height: 36,
				button_text : '<span class="button">Выберите фотографии<br><font size="-1">(до 500 KB каждая)</font></span>',
				button_text_style : '.button {text-align: center; font-family: Arial, Helvetica, sans-serif; height:30px; border-color:#c2e254 #9bb838 #9bb838 #c2e254; border-style:solid; border-width:1px; background:#c2e254; cursor:pointer;}',
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,

				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete
			};

			swfu = new SWFUpload(settings);
	     };
	</script>
	<table border=0 cellspacing=5 cellpadding=5>
	<tr><td width=250>
	<form id="form1" action="index.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="uname" id="uname" value="<?=str_replace("'",';',$_COOKIE['CUser'])?>" />
	<input type="hidden" name="upass" id="upass" value="<?=str_replace("'",';',$_COOKIE['CPass'])?>" />
			<table border=0 cellspacing=0 cellpadding=0>
            <tr><td align="right">Имя:&nbsp;</td><td><input style="width:125px;" type="text" id="f_name" name="f_name" /></td></tr>
            <tr><td align="right">Город:&nbsp;</td><td><input style="width:125px;" type="text" id="city" name="city" /></td></tr>
            <tr><td align="right">Возраст:&nbsp;</td><td><input style="width:125px;" type="text" id="age" name="age" /></td></tr>
            <tr><td align="right">Пол:&nbsp;</td><td><select size="1" id="f_gener" name="f_gener"><option selected value="1">Муж</option><option value="2">Жен</option></select></td></tr>

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
	</td><td valign="top"><div id="thumbnails"></div></td></tr></table>
<?}else echo ('Для добавления фотографий необходима авторизация');?>