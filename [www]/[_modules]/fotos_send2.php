<h1>���������� ����������</h1>
<?if(AuthUserName != '') {?>
<b>����������:</b><br>
 - ���������� ������ ���� � ������� JPG<br>
 - ������ ����� �� ������ ��������� 500��<br>
 - ���������� �� ������ ���� ����� ��������� � �� ��������� ���������� 1024�768<br>
<br>���������� �������� �� ����� <b>������ ����� ��������</b> �����������!!! ���� �� �������� ���� � �� �� ���������� 72 ���� - ������� ��� �� ��������! �������� ����� �������� �� 12 ����� (���������, ����� ��� � ������ ��������� ��������).
<br>�� ����� �� ��������: ������� ��������� ����, �������������, ������� ����� ����������, ���������� �� ��������� 18+.
<hr><br>
<center>
<div style="font-size: 15px"><a href="http://www.tzpolice.ru/?act=data2&type=manuals&Id=123" target=_blank>��������: ��� ��������� �� ���� ������</a></div>
<br><br>
���� � ��� ��������� ��������� ��� ������������� ����� ������� - ���������� <a href='/?act=fotos_send'>������ ������</a> ����������.<br>��� ������ ����� ������� Firefox, IE ��� Opera � ������������� Flash player ������ 9 ��� ����.</span>
</center><br>
<hr>
<div style="font-size: 20px; color: red; text-decoration: blink;" align="center">���������� �������� �� ����� <b>������ ����� ��������</b> �����������!!!</div>
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
				file_types_description : "��������",
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
				button_text : '<span class="button">�������� ����������<br><font size="-1">(�� 500 KB ������)</font></span>',
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
            <tr><td align="right">���:&nbsp;</td><td><input style="width:125px;" type="text" id="f_name" name="f_name" /></td></tr>
            <tr><td align="right">�����:&nbsp;</td><td><input style="width:125px;" type="text" id="city" name="city" /></td></tr>
            <tr><td align="right">�������:&nbsp;</td><td><input style="width:125px;" type="text" id="age" name="age" /></td></tr>
            <tr><td align="right">���:&nbsp;</td><td><select size="1" id="f_gener" name="f_gener"><option selected value="1">���</option><option value="2">���</option></select></td></tr>

            <tr><td align="right">�����������:&nbsp;</td><td><input style="width:125px;" type="text" id="comment" name="comment" /></td></tr>                                    
            <tr><td colspan="2" style="border: solid 2px #5e3e00; background-color: #ffe1a6; padding: 0px; margin: 0px;" align="center" valign="middle">
					<span id="spanButtonPlaceHolder"></span>
				</div></td></tr>
            <tr><td><input id="btnGo" type="button" value="������ ��������" onclick="initiateUpload();" disabled="disabled" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; padding: 5px; height: 39px; width: 125px;" /></td><td align="right">
				<input id="btnCancel" type="button" value="�������� ��� ��������" onclick="swfu.cancelQueue();" disabled="disabled" style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; padding: 5px; height: 39px; width: 125px;" />
			</td></tr>
            </table>
            <br />
			<div class="fieldset flash" id="fsUploadProgress">
			<b>������� ��������</b>
			</div>
	</form>
	</td><td valign="top"><div id="thumbnails"></div></td></tr></table>
<?}else echo ('��� ���������� ���������� ���������� �����������');?>