<?
//error_reporting (E_ALL);
$bg[0]='i/bgr-grid-sand.gif';
$bg[1]='i/bgr-grid-sand1.gif';
foreach ($_GET as $key=>$value)
{
	$_GET[$key]=mysql_real_escape_string($value);
}

$section = array('manuals' => '�������', 'manuals2' => '������', 'art' => '����������', 'manuals_new' => '������� ��� 1-5 �������', 'quests_new' => '������ ��� 1-5 �������', 'mine_maps' => '����� ����');

$subsection['manuals']		= array(10 => '��� ����', 20 => '�������� ���������', 30 => '���������', 40 => '�������� ��������� (�����, ������, ��������� � �.�.)', 50 => '��������� (�����, ��������, ������������ � �.�.)', 60 => '������� ����� (������� � �.�.)', 70 => '���������� ��� (������, ��������, ����� ���� � �.�.)', 80 => '�������', 90 => '������', 100 => '����������');
//$subsection['manuals2']	= array(1 => '������ ��������', 2 => '������ ������', 3 => '��� ������ � ������� �������', 4 => '��� ������� � ������� �������', 5 => '�����������', 6=> '����������������', 7=> '��������', 8=> '����������', 9=> '!�����', 10=> '!�� ���������', 11=> '!����������������', 12=> '!�����������', 13=> '!���������', 14=> '!��������', 15=> '!���������� ����', 16=> '!����������');
$subsection['manuals2']		= array(1=> '�����', 2=> '�� ���������', 3=> '����������������', 4=> '�����������', 5=> '���������', 6=> '��������', 7=> '���������� ����', 8=> '����������');
$subsection['manuals_new']	= array(1 => '������� ��� 1-5 ������', 2 => '� ������, ����� ����');
$subsection['quests_new']	= array(1 => '������ Coldverge', 2 => '��������� ������', 3 => '���������', 4 => '����������');
$subsection['art']		= array(1 => '����', 2 => '��������', 3 => '������', 4 => '������', 5 => '��������', 6 => '��������');
$subsection['mine_maps']	= array(1 => '����� ����');
$subsection['faq']	= array(1 => '����� ���������� �������');
//$subsection['interview']	= array(1 => '��������');


if (isset($_POST['add_subm']) && (abs(AccessLevel) & AccessArticles)):
// ����������
	$_POST['prof'] = str_replace('|','/',$_POST['prof']);
	$_POST['prize'] = str_replace('|','/',$_POST['prize']);
	$_POST['reputation'] = str_replace('|','/',$_POST['reputation']);
	$_POST['other'] = str_replace('|','/',$_POST['other']);
	$_POST['poster'] = str_replace('|','/',$_POST['poster']);
	$_POST['cooldown'] = str_replace('|','/',$_POST['cooldown']);
	$_POST['complexity'] = str_replace('|','/',$_POST['complexity']);
	$_POST['fraction'] = str_replace('|','/',$_POST['fraction']);
	$_POST['quests'] = str_replace('|','/',$_POST['quests']);
	$extra = (int)$_POST['b_l'].'|'.(int)$_POST['e_l'].'|'.$_POST['prof'].'|'.$_POST['prize'].'|'.$_POST['reputation'].'|'.$_POST['other'].'|'.$_POST['poster'].'|'.$_POST['cooldown'].'|'.$_POST['complexity'].'|'.$_POST['fraction'].'|'.$_POST['quests'];


	if (isset($_GET['Id'])):
		$sql = "UPDATE `site_data` SET `id_sec` = '{$_POST['id_sec']}', `title` = '{$_POST['title']}', `text` = '{$_POST['NewsText']}', `allow_comments` = '{$_POST['allow_comments']}', `markup` = '1', `subsection` = '{$_POST['subsection']}', `extra` = '$extra'  WHERE `id` = '{$_GET['Id']}'";
	else:
		$sql = "INSERT INTO `site_data` (`id`, `id_sec`, `title`, `text`, `post_date`, `poster_id`, `allow_comments`, `markup`, `subsection`, `extra`) VALUES ('', '{$_POST['id_sec']}', '{$_POST['title']}', '{$_POST['NewsText']}', '".time()."', '".AuthUserId."', '{$_POST['allow_comments']}', '1', '{$_POST['subsection']}', '$extra')";
	endif;
	 mysql_query($sql) or die(mysql_error());
	?>
	<script language="javascript">self.location.href = "<?=$_SERVER['PHP_SELF'].'?act='.$_GET['act'].'&type='.$_POST['id_sec']?>";</script>
	<?

endif;

if (@$_GET['type'] == 'manuals' || @$_GET['type'] == 'manuals_new'):
	if (!isset($_GET['action']) && !isset($_GET['Id'])):

		echo '<h1>'.$section[$_GET['type']].'</h1>';
		echo '<table>
		<tr bgColor="#f4ecd4" align="center">
		<td width=1%><b>�</b></td>
		<td width=70%><b>��������</b></td>
		<td width=29%><b>�����</b></td>';

		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);

		echo '<td width=5%><b>��������</b></td>';
		}

		echo '</tr>';

		$c=1;
//		for ($i=1; $i<=count($subsection[$_GET['type']]); $i++):
foreach ($subsection[$_GET['type']] as $i => $title)
	{
			$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `subsection`, `extra` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '$i' ORDER BY `title` ASC");
			if (mysql_num_rows($query)!=0) echo '<tr><td colspan="3" background="i/bgr-grid-sand1.gif"><p style="line-heigth: 20px; font-weight: bold; padding: 5px;">'.$subsection[$_GET['type']][$i].'</p></td></tr>';
			while ($q = mysql_fetch_array($query)):
				if (@$q['extra'] != '')
					{
						$extra = explode('|', $q['extra']);
					}
				else
					{
						$extra = '';
					}
				echo '<tr>';
				echo '<td background="i/bgr-grid-sand.gif" align="center">'.$c++.'</td>';
				echo '<td background="i/bgr-grid-sand.gif">'.($q['text']!=''?'<a href="'.$_SERVER['SCRIPT_NAME'].'?act='.$_GET['act'].'&type='.$_GET['type'].'&Id='.$q['id'].'">'.stripslashes($q['title']).'</a>':stripslashes($q['title'])).'</td>';
				@$extra[6] = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", @$extra[6]);
				@$extra[6] = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", @$extra[6]);
				echo '<td background="i/bgr-grid-sand.gif" nowrap>'.@$extra[6].'</td>';

				if(abs(AccessLevel) & AccessArticles) {
					$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
					$rr=mysql_query($SQL2);
					$dd=mysql_fetch_assoc($rr);

				echo '<td background="i/bgr-grid-sand.gif" align="center"><a href="?act='.$_GET['act'].'&Id='.$q['id'].'&action=edit"><img src="images/edit.gif" alt="" border="0"></a> <a onclick="if(confirm(\'������� �������/����� �� ����?\')){top.location=\'?act='.$_GET['act'].'&type=manuals2&Id='.$q['id'].'&action=delete\'}" href="javascript:{}"><img src="images/delete.gif" alt="" border="0"></a></td>';
				}
				echo '</tr>';
			endwhile;
//		endfor;
	}
		echo '</table><br />';
		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);


		echo '<p><a href="?act='.$_GET['act'].'&type='.$_GET['type'].'&action=edit">�������� �������</a></p>';
		}
		$query = mysql_query("SELECT `id`, `id_sec`, `title`, `subsection`, `extra` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '' ORDER BY `title` ASC");
		while ($q = mysql_fetch_array($query)):
			echo '<div class="verdana11"><img src="i/bullet-red-01a.gif" width="18" hspace="5"><a href="'.$_SERVER['SCRIPT_NAME'].'?act='.$_GET['act'].'&type='.$_GET['type'].'&Id='.$q['id'].'&action=edit">'.stripslashes($q['title']).'</a></div>';
		endwhile;
	endif;
elseif (@$_GET['type'] == 'art' || @$_GET['type'] == 'mine_maps' || @$_GET['type'] == 'faq'):
	if (!isset($_GET['action']) && !isset($_GET['Id'])):

		echo '<h1>'.$section[$_GET['type']].'</h1>';
		echo '<table>
		<tr bgColor="#f4ecd4" align="center">
		<td width=5%><b>�</b></td>
		<td width=50%><b>��������</b></td>
		<td width=35%><b>�����</b></td>
		<td width=10%><b>����</b></td>';

		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);

		echo '<td width=5%><b>��������</b></td>';
		}

		echo '</tr>';
		$c=1;
		for ($i=1; $i<=count($subsection[$_GET['type']]); $i++):
			$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `subsection`, `extra`, `post_date` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '$i' ORDER BY `post_date` DESC, `title` ASC");
			if (mysql_num_rows($query)!=0) echo '<tr><td colspan="11" background="i/bgr-grid-sand1.gif"><p style="line-heigth: 20px; font-weight: bold; padding: 5px;">'.$subsection[$_GET['type']][$i].'</p></td></tr>';
			while ($q = mysql_fetch_array($query)):
				if (@$q['extra'] != '')
					{
						$extra = explode('|', $q['extra']);
					}
				else
					{
						$extra = '';
					}
				echo '<tr>';
				echo '<td background="i/bgr-grid-sand.gif" align="center">'.$c++.'</td>';
				echo '<td background="i/bgr-grid-sand.gif">'.($q['text']!=''?'<a href="'.$_SERVER['SCRIPT_NAME'].'?act='.$_GET['act'].'&type='.$_GET['type'].'&Id='.$q['id'].'">'.stripslashes($q['title']).'</a>':stripslashes($q['title'])).'</td>';
				@$extra[6] = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", @$extra[6]);
				@$extra[6] = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", @$extra[6]);
				echo '<td background="i/bgr-grid-sand.gif" nowrap>'.@$extra[6].'</td>';
				echo '<td background="i/bgr-grid-sand.gif" nowrap align="center">'.date("d.m.Y", @$q['post_date']).'</td>';

				if(abs(AccessLevel) & AccessArticles) {
					$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
					$rr=mysql_query($SQL2);
					$dd=mysql_fetch_assoc($rr);

				echo '<td background="i/bgr-grid-sand.gif" align="center"><a href="?act='.$_GET['act'].'&Id='.$q['id'].'&action=edit"><img src="images/edit.gif" alt="" border="0"></a> <a onclick="if(confirm(\'������� �������/����� �� ����?\')){top.location=\'?act='.$_GET['act'].'&type=manuals2&Id='.$q['id'].'&action=delete\'}" href="javascript:{}"><img src="images/delete.gif" alt="" border="0"></a></td>';
				}
				echo '</tr>';
			endwhile;
		endfor;
		echo '</table><br />';
		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);


		echo '<p><a href="?act='.$_GET['act'].'&type=art&action=edit">�������� ������</a></p>';
		}
		$query = mysql_query("SELECT `id`, `id_sec`, `title`, `subsection`, `extra` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '' ORDER BY `title` ASC");
		while ($q = mysql_fetch_array($query)):
			echo '<div class="verdana11"><img src="i/bullet-red-01a.gif" width="18" hspace="5"><a href="'.$_SERVER['SCRIPT_NAME'].'?act='.$_GET['act'].'&type='.$_GET['type'].'&Id='.$q['id'].'&action=edit">'.stripslashes($q['title']).'</a></div>';
		endwhile;

	endif;
elseif (@$_GET['type'] == 'manuals2' || @$_GET['type'] == 'quests_new'):
	if (!isset($_GET['action']) && !isset($_GET['Id'])):

		echo '<h1>'.$section[$_GET['type']].'</h1>';
		echo '<table>';
		$c=1;
		for ($i=1; $i<=count($subsection[$_GET['type']]); $i++):
			//$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `subsection`, `extra` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '$i' ORDER BY `extra`, `title` ASC");
			$query = mysql_query("SELECT CAST(SUBSTRING_INDEX(`extra`, \"|\", 1) as unsigned) as `num`, `id`, `id_sec`, `title`, `text`, `subsection`, `extra` FROM `site_data` WHERE `id_sec` = '{$_GET['type']}' AND `subsection` = '$i' ORDER BY `num`, `title` ASC");
			if (mysql_num_rows($query)!=0)
				{
					echo '<tr><td colspan="13" background="i/bgr-grid-sand7.gif"><p align="center" style="line-heigth: 20px; font-weight: bold; font-size: 16px; padding: 5px;">'.$subsection[$_GET['type']][$i].'</p></td></tr>';

//header

		echo '<tr bgColor="#f4ecd4" align="center">
		<td width=1% rowspan=2><b><small>�</small></b></td>
		<td width=24% rowspan=2><b><small>��������</small></b></td>
		<td width=10% colspan=2><b><small>�������</small></b></td>

		<td width=5% rowspan=2><b><small>�������</small></b></td>
		<td width=5% rowspan=2><b><small>���������</small></b></td>
		<td width=5% rowspan=2><b><small>���������</small></b></td>
		<td width=5% rowspan=2><b><small>�������</small></b></td>
		<td width=5% rowspan=2><b><small>���������</small></b></td>
		<td width=13% rowspan=2><b><small>������</small></b></td>
		<td width=10% rowspan=2><b><small>�������</small></b></td>
		<td width=10% rowspan=2><b><small>�����</small></b></td>
		<td width=7% rowspan=2><b><small>����������</small></b></td>';

		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);

			echo '<td width=5% rowspan=2><b><small>��������</small></b></td>';
		}

		echo '</tr>

		<tr bgColor="#f4ecd4" align="center">
		<td width=1% valign=top><small>���</small></td>
		<td width=1% valign=top><small>����</small></td>
		</tr>';


//end header
				}
			while ($q = mysql_fetch_array($query)):
				if (@$q['extra'] != '')
					{
						$extra = explode('|', $q['extra']);
					}
				else
					{
						$extra = '';
					}
				echo '<tr>';
				echo '<td background="'.$bg[($c+1)%2].'" align="center"><small>'.$c++.'</small></td>';
				echo '<td background="'.$bg[$c%2].'">'.($q['text']!=''?'<a href="'.$_SERVER['SCRIPT_NAME'].'?act='.$_GET['act'].'&type='.$_GET['type'].'&Id='.$q['id'].'">'.stripslashes($q['title']).'</a>':stripslashes($q['title'])).'</td>';
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.(@$extra[0]!=0?@$extra[0]:'').'</small></td>';
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.(@$extra[1]!=0?@$extra[1]:'').'</small></td>';
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[7].'</small></td>'; //Cooldown :: 7
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[8].'</small></td>'; //Complexity :: 8
				@$extra[2] = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'> ", @$extra[2]);
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[2].'</small></td>';
//				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[9].'</small></td>'; //Fraction :: 9
				if (@$extra[9] == "Freedom")
					{
						echo '<td background="'.$bg[$c%2].'" align="center"><img src="/_imgs/fre.png" alt="Freedom"></td>';
					}
				else if (@$extra[9] == "Ordnung")
					{
						echo '<td background="'.$bg[$c%2].'" align="center"><img src="/_imgs/ord.png" alt="Ordnung"></td>';
					}
				else
					{
						echo '<td background="'.$bg[$c%2].'" align="center">&nbsp;</td>';
					}
				@$extra[4] = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'> ", @$extra[4]);
				if (@$extra[4] == "Freedom" || @$extra[4] == "���")
					{
						$extra[4]='<img src="/_imgs/fre.png" alt="Freedom">';
					}
				else if (@$extra[4] == "Ordnung" || @$extra[4] == "���")
					{
						$extra[4]='<img src="/_imgs/ord.png" alt="Ordnung">';
					}
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[4].'</small></td>';
				echo '<td background="'.$bg[$c%2].'" align="center"><small>'.@$extra[10].'</small></td>'; //Quests :: 10
				echo '<td background="'.$bg[$c%2].'"><small>'.@$extra[3].'</small></td>';
				@$extra[6] = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", @$extra[6]);
				@$extra[6] = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", @$extra[6]);
				echo '<td background="'.$bg[$c%2].'" nowrap><small>'.@$extra[6].'</small></td>';
				@$extra[5] = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", @$extra[5]);
				echo '<td background="'.$bg[$c%2].'"><small>'.@$extra[5].'</small></td>';
				if(abs(AccessLevel) & AccessArticles) {
					$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
					$rr=mysql_query($SQL2);
					$dd=mysql_fetch_assoc($rr);

					echo '<td background="'.$bg[$c%2].'" align="center"><a href="?act='.$_GET['act'].'&Id='.$q['id'].'&action=edit"><img src="images/edit.gif" alt="" border="0"></a> <a onclick="if(confirm(\'������� �������/����� �� ����?\')){top.location=\'?act='.$_GET['act'].'&type=manuals2&Id='.$q['id'].'&action=delete\'}" href="javascript:{}"><img src="images/delete.gif" alt="" border="0"></a></td>';
				}
				echo '</tr>';
			endwhile;
		endfor;
		echo '</table>';
		if(abs(AccessLevel) & AccessArticles) {
			$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
			$rr=mysql_query($SQL2);
			$dd=mysql_fetch_assoc($rr);


		echo '<p><a href="?act='.$_GET['act'].'&type='.$_GET['type'].'&action=edit">�������� �����</a></p>';
		}
	endif;
endif;




if (!isset($_GET['action']) && isset($_GET['Id'])):

	$id = (int)$_GET['Id'];
	$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `post_date`, `poster_id`, `allow_comments`, `markup`, `subsection`, `extra` FROM `site_data` WHERE `id` = '$id'");
	$d = mysql_fetch_array($query);

	echo '
	<table width="100%" border="0" cellspacing="3" cellpadding="3"><tr>
	<td height="20" background="i/bgr-grid-sand.gif"><p><img src="i/bullet-red-01.gif" width="18" height="11" hspace="5"><strong>['.date("d.m.Y - H:i", $d['post_date']).'] </strong> <span class="top-header"><a href="?act='.$_GET['act'].'&type='.$_GET['type'].'">'.$section[$_GET['type']].'</a> / '.stripslashes($d['title']).'</span></p></td>
	</tr><tr><td>';

		if ($d['markup'] == 0) {
			ParseNews($d['text'],1);
			$markup_vers = '';
		} elseif ($d['markup'] == 1) {
			ParseNews2a($d['text']);
			$markup_vers = '2';
		}
	if (@$d['extra'] != '')
		{
			$extra = explode('|', $d['extra']);
			$realauthor = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", @$extra[6]);
			$realauthor = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $realauthor);
 		}
	echo '</td></tr><tr><td height=20 align="right" background="i/bgr-grid-sand1.gif">'.$realauthor;
	if(abs(AccessLevel) & AccessArticles) {
	$poster = mysql_fetch_assoc(mysql_query("SELECT `id`, `user_name` FROM `site_users` WHERE `id` = '{$d['poster_id']}'"));
	echo '/ '.GetUser($d['poster_id'],$poster['user_name'],AuthUserGroup).' /
	';
//		$SQL2 = 'SELECT id,clan,user_name FROM site_users WHERE id=\''.$d['checker_id'].'\'';
//		$rr=mysql_query($SQL2);
//		$dd=mysql_fetch_assoc($rr);


	echo '
	<a href="?act='.$_GET['act'].'&Id='.$d['id'].'&action=edit">�������������</a> /
	<a onclick="if(confirm(\'������� �������/����� �� ����?\')){top.location=\'?act='.$_GET['act'].'&Id='.$d['id'].'&action=delete\'}" href="javascript:{}">�������</a> /';
	}
	echo '</td></tr></table>';

endif;





if (isset($_GET['action'])):
	if ($_GET['action']=='edit'):

		$id = (int)$_GET['Id'];
		$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `post_date`, `poster_id`, `allow_comments`, `markup`, `subsection`, `extra` FROM `site_data` WHERE `id` = '$id'");
		$content = mysql_fetch_array($query);

		$content['text'] = str_replace('?act=data&', '?act=data2&', $content['text']);
		$content['text'] = str_replace('</li>', '', $content['text']);
		if ($content['markup']==0):
			$content['text'] = stripslashes($content['text']);
			$content['text'] = str_replace('<br />', '', $content['text']);
			$content['text'] = str_replace('<br>', '', $content['text']);
			$content['text'] = str_replace('<li>', '[*]', $content['text']);
			$content['text'] = str_replace('<i>', '[i]', $content['text']);
			$content['text'] = str_replace('</i>', '[/i]', $content['text']);
			$content['text'] = str_replace('?act=data&', '?act=data2&', $content['text']);
			$content['text'] = str_replace("#; return false;", "javascript:{}", $content['text']);
			$content['text'] = str_replace('return false;"', "javascript:{}", $content['text']);

			$content['text'] = preg_replace('#<(/?)strong[^>]*>#i', '[$1b]', $content['text']);
			$content['text'] = preg_replace('#<(/?)b[^>]*>#i', '[$1b]', $content['text']);
			$content['text'] = preg_replace('#<(/?)em[^>]*>#i', '[$1i]', $content['text']);
			$content['text'] = preg_replace('#<(/?)u[^>]*>#i', '[$1u]', $content['text']);
			$content['text'] = preg_replace('#<font.*color=\'(.*)\'.*>(.*)</font>#iU', '[color=$1]$2[/color]', $content['text']);								// ����
			$content['text'] = preg_replace('#<font.*color="(.*)".*>(.*)</font>#iU', '[color=$1]$2[/color]', $content['text']);									// ����
			$content['text'] = preg_replace('#<font.*color=(.*)>(.*)</font>#iU', '[color=$1]$2[/color]', $content['text']);										// ����
			$content['text'] = preg_replace('#<img[^>]* src="http://www.tzpolice.ru/_imgs/pro/i(\w*).gif"[^>]*/>#i', '[pro=$1]', $content['text']);				// �����
			$content['text'] = preg_replace('#<img[^>]* src="http://www.tzpolice.ru/_imgs/clans/([-a-z0-9%_]*).gif"[^>]*/>#i', '[clan=$1]', $content['text']);	// ����
			$content['text'] = str_replace('[clan=%20]', '', $content['text']);																					// ������� ����
			$content['text'] = preg_replace('#<img[^>]*src="([^"]*)"[^>]*>#i', '[image]$1[/image]', $content['text']);											// ����������� � ������� ��������
			$content['text'] = preg_replace('#<img[^>]*src=\'([^\']*)\'[^>]*>#i', '[image]$1[/image]', $content['text']);										// ����������� � ��������� ��������

		elseif ($content['markup']==1):

		endif;

			// ����� ��������
			echo '<SCRIPT src="_modules/xhr_js.js"></SCRIPT>';
			echo '<SCRIPT src="_modules/news_js.js"></SCRIPT>';
			?>
			<script language="JavaScript1.2">
			<!--
			function str_replace(search, replace, subject) {
				var f = search, r = replace, s = subject;
				var ra = r instanceof Array, sa = s instanceof Array, f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;

				while (j = 0, i--) {
					if (s[i]) {
						while (s[i] = (s[i]+'').split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
					}
				};
				return sa ? s : s[0];
			}


			function loadNick() {
				var query = '' + document.getElementById('ins_nick').value;
				var req = new Subsys_JsHttpRequest_Js();
				req.onreadystatechange = function()
				{
					if (req.readyState == 4) {
							if (req.responseJS) {
								if (req.responseJS.res == 'OK') {
									insertAtCaret(document.getElementById('NewsText'), req.responseText);
									document.getElementById('plzwait').innerHTML = '';
								} else {
									document.getElementById('plzwait').innerHTML = req.responseText;
								}
							}
					}
				}
				req.caching = false;
				req.open('POST', '_modules/backends/charinfo.php', true);
				document.getElementById("plzwait").innerHTML = '<center><b>����������, ���������</b></center>';
				req.send({ n: query });
			}

			function preview_news() {
				var q_title = '' + document.getElementById('title').value;
				var q_text = '' + document.getElementById('NewsText').value;
				q_text = str_replace('+','&#43;',q_text);
				var req = new Subsys_JsHttpRequest_Js();
				req.onreadystatechange = function() {
					if (req.readyState == 4) {
			//			if (req.responseJS) {
							document.getElementById('preview_area').innerHTML = req.responseText;
			//			}
					}
				}
				req.caching = false;
				req.open('POST', '_modules/backends/news2_preview.php', true);
				document.getElementById("preview_area").innerHTML = '<center><br>����������, ���������<br><br></center>';
				req.send({ ti: q_title, te: q_text, ta: "<?=AuthUserName?>" });
			}

			var smileWin = 0;
			var picsWin = 0;
			function smileWindow(obj) {
				if(smileWin) {
					if(!smileWin.closed) smileWin.close();
				}
				win_left = obj.clientX + 15;
				win_top = obj.clientY + 15;
				smileWin = open('_modules/icons/smiles2.php', 'smiles', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=no,width=350,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
			}

			function picsWindow(obj) {
				if(picsWin) {
					if(!picsWin.closed) picsWin.close();
				}
				win_left = obj.clientX + 15;
				win_top = obj.clientY + 15;
				picsWin = open('_modules/icons/upload2.php', 'pics', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=yes,width=400,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
			}
			//-->
			</script>

			<!-- Editor area start -->
			<table width="100%"  border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td colspan="2"><form name="manual" method="post" action="">
					<?=(@$content['id']!=''?'<input type="hidden" name="id" value="'.$content['id'].'">':'')?>
					<b>���������:</b> <br>
					<input name="title" id="title" type="text" style="width:95%" value="<?=(str_replace('"','&quot;',@$content['title']))?>">
					<br><br>
					<select size="1" name="id_sec">
						<option value="manuals"<?=((@$content['id_sec']=='manuals') || (@$_GET['type']=='manuals')?' selected':'')?>>�������</option>
						<option value="manuals2"<?=((@$content['id_sec']=='manuals2') || (@$_GET['type']=='manuals2')?' selected':'')?>>������</option>
						<option value="manuals3"<?=((@$content['id_sec']=='manuals3') || (@$_GET['type']=='manuals3')?' selected':'')?>>��������</option>
						<option value="quests_new"<?=((@$content['id_sec']=='quests_new') || (@$_GET['type']=='quests_new')?' selected':'')?>>������ ��� ��������</option>
						<option value="manuals_new"<?=((@$content['id_sec']=='manuals_new') || (@$_GET['type']=='manuals_new')?' selected':'')?>>������� ��� ��������</option>
						<option value="art"<?=((@$content['id_sec']=='art') || (@$_GET['type']=='art')?' selected':'')?>>����������</option>
						<option value="faq"<?=((@$content['id_sec']=='faq') || (@$_GET['type']=='faq')?' selected':'')?>>����</option>
					</select>
					<br><br>
					<b>�����:</b>
					<br>����:
							<select name="fc" onChange="fontstyle('[color=' + this.form.fc.options[this.form.fc.selectedIndex].value + ']', '[/color]','NewsText');this.selectedIndex=0;">
								<option value="none">�� ���������</option>
								<option style="color:darkred;" value="darkred">Ҹ���-�������</option>
								<option style="color:red;" value="red">�������</option>
								<option style="color:orange;" value="orange">���������</option>
								<option style="color:brown;" value="brown">����������</option>
								<option style="color:green;" value="green">������</option>
								<option style="color:olive;" value="olive">���������</option>
								<option style="color:blue;" value="blue">�����</option>
								<option style="color:darkblue;" value="darkblue">Ҹ���-�����</option>
								<option style="color:indigo;" value="indigo">������</option>
								<option style="color:violet;" value="violet">����������</option>
								<option style="color:white;" value="white">�����</option>
								<option style="color:black;" value="black">׸����</option>
							</select>
					&nbsp;
							<select name="ins_clan" id="ins_clan" size="1" onChange="AddClan(this.form.ins_clan.options[this.form.ins_clan.selectedIndex].value, 'NewsText');this.selectedIndex=0;">
								<option value="none" selected>�����</option>
					<?
					$smiles_dir = "_imgs/clans/";
					$d = opendir($smiles_dir);
					$counter = 0;
					while (($CurFile=readdir($d)) !== false) if (is_file("$smiles_dir/$CurFile")) {
						$FileType = GetImageSize("$smiles_dir/$CurFile");
						$CurFile = explode(".",$CurFile);
						if($FileType[2]==1):
							$clans[$counter] = $CurFile[0];
							$counter++;
						endif;
					}

					closedir($d);
					natcasesort($clans);
					reset($clans);
					while (list($key, $val) = each($clans)):
						if ($val !== "0"):
							echo ('<option value="'.$val.'">'.$val.'</option>');
						endif;
					endwhile;
					?>
							</select>
					&nbsp;
						���: <input name="ins_nick" id="ins_nick" type="text" size="16"><input name="in_subm" type="button" class="ed_dark" value="��" onClick="loadNick()"><br>
						<div id="plzwait"><br></div>
						<table width="100%" border="0">
						<tr><td width="*">
						<textarea ONSELECT="storeCaret(this);" ONCLICK="storeCaret(this);" ONKEYUP="storeCaret(this);" name="NewsText" id="NewsText" rows="45" wrap="VIRTUAL" style="width:95%"><?=@htmlspecialchars(stripslashes(@$content['text']))?></textarea>
						</td>
						<td width="180">
							<a href="JavaScript:AddSmile('congr')"><img border=0 src="/_imgs/smiles/congr.gif"></a>
							<a href="JavaScript:AddSmile('king')"><img border=0 src="/_imgs/smiles/king.gif"></a>
							<a href="JavaScript:AddSmile('crazy')"><img border=0 src="/_imgs/smiles/crazy.gif"></a>
							<a href="JavaScript:AddSmile('horse')"><img border=0 src="/_imgs/smiles/horse.gif"></a>
							<a href="JavaScript:AddSmile('popcorn')"><img border=0 src="/_imgs/smiles/popcorn.gif"></a>
							<a href="JavaScript:AddSmile('friday')"><img border=0 src="/_imgs/smiles/friday.gif"></a>
							<a href="JavaScript:AddSmile('wink')"><img border=0 src="/_imgs/smiles/wink.gif"></a>
							<a href="JavaScript:AddSmile('ok')"><img border=0 src="/_imgs/smiles/ok.gif"></a>
							<a href="JavaScript:AddSmile('shuffle')"><img border=0 src="/_imgs/smiles/shuffle.gif"></a>
							<a href="JavaScript:AddSmile('mol')"><img border=0 src="/_imgs/smiles/mol.gif"></a>
							<a href="JavaScript:AddSmile('boks')"><img border=0 src="/_imgs/smiles/boks.gif"></a>
							<a href="JavaScript:AddSmile('budo')"><img border=0 src="/_imgs/smiles/budo.gif"></a>
						</tr></table>
				              <a href="javascript:AddTag('[b]','[/b]','NewsText')"><img border="0" src="_imgs/editor/bold.gif" alt="����������" width="24" height="24"></a><a href="javascript:AddTag('[i]','[/i]','NewsText')"><img border="0" src="_imgs/editor/italic.gif" alt="������" width="24" height="24"></a><a href="javascript:AddTag('[u]','[/u]','NewsText')"><img border="0" src="_imgs/editor/underline.gif" alt="������������" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[left]','[/left]','NewsText')"><img border="0" src="_imgs/editor/left.gif" alt="�� ������ ����" width="24" height="24"></a><a href="javascript:AddTag('[center]','[/center]','NewsText')"><img border="0" src="_imgs/editor/center.gif" alt="�� ������" width="24" height="24"></a><a href="javascript:AddTag('[right]','[/right]','NewsText')"><img border="0" src="_imgs/editor/right.gif" alt="�� ������� ����" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[list]\n[*]\n[*]\n[*]\n','[/list]','NewsText')"><img border="0" src="_imgs/editor/u_list.gif" alt="�������������� ������" width="24" height="24"></a><a href="javascript:AddTag('[list=x]\n[*]\n[*]\n[*]\n','[/list=x]','NewsText')"><img border="0" src="_imgs/editor/o_list.gif" alt="������������ ������" width="24" height="24"></a><a href="javascript:AddTag('[*]','','NewsText')"><img border="0" src="_imgs/editor/list_item.gif" alt="������� ������" width="24" height="24"></a>&nbsp;<a href="#; return false;" onClick="picsWindow(event);"><img border="0" src="_imgs/editor/image1.gif" alt="�����������" width="24" height="24"></a><a href="#; return false;" onClick="AddImgOuter();"><img border="0" src="_imgs/editor/image2.gif" alt="����������� � ������� �����" width="24" height="24"></a><a href="#; return false;" onClick="AddImgOuterLeft();"><img border="0" src="_imgs/editor/image3.gif" alt="����������� � ������� ����� � ������������� �����" width="24" height="24"></a><a href="javascript:AddUrl('NewsText')"><img border="0" src="_imgs/editor/hyperlink.gif" alt="������" width="24" height="24"></a><a href="#; return false;" onClick="smileWindow(event);"><img border="0" src="_imgs/editor/smile.gif" alt="��������" width="24" height="24"></a><a href="javascript:AddTag('[small]','[/small]','NewsText')"><img border="0" src="_imgs/editor/small.gif" alt="����������� �����" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[quote]','[/quote]','NewsText')"><img border="0" src="_imgs/editor/quote.gif" alt="������" width="75" height="24"></a><a href="javascript:AddTag('[dialog=&quot;***.jpg&quot;]','[/dialog]','NewsText')"><img border="0" src="_imgs/editor/dialog.gif" alt="������" width="48" height="24"></a><a href="javascript:AddTag('[log]','[/log]','NewsText')"><img border="0" src="_imgs/editor/log.gif" alt="��� ���" width="24" height="24"></a><a href="javascript:AddHidden('NewsText')"><img border="0" src="_imgs/editor/hidden.gif" alt="������� �����" width="75" height="24"></a><?if (AuthUserGroup > 1 || (abs(AccessLevel) & AccessNewsEditor)) {?><a href="javascript:AddTag('[pers]','[/pers]','NewsText')"><img border="0" src="_imgs/editor/pers.gif" alt="��������" width="50" height="24"></a><?}?>&nbsp;
						<?
						if (@$content['extra'] != '') $extra = explode('|', $content['extra']);
						?>
						<br>
							<a href="javascript:AddPro('0', 'NewsText');"><img border="0" src="_imgs/pro/i0.gif" width="15" height="15"></a><a href="javascript:AddPro('1', 'NewsText');"><img border="0" src="_imgs/pro/i1.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('2', 'NewsText');"><img border="0" src="_imgs/pro/i2.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('3', 'NewsText');"><img border="0" src="_imgs/pro/i3.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('4', 'NewsText');"><img border="0" src="_imgs/pro/i4.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('5', 'NewsText');"><img border="0" src="_imgs/pro/i5.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('6', 'NewsText');"><img border="0" src="_imgs/pro/i6.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('7', 'NewsText');"><img border="0" src="_imgs/pro/i7.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('8', 'NewsText');"><img border="0" src="_imgs/pro/i8.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('9', 'NewsText');"><img border="0" src="_imgs/pro/i9.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('10', 'NewsText');"><img border="0" src="_imgs/pro/i10.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('11', 'NewsText');"><img border="0" src="_imgs/pro/i11.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('12', 'NewsText');"><img border="0" src="_imgs/pro/i12.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('16', 'NewsText');"><img border="0" src="_imgs/pro/i16.gif" alt="���-�����" width="15" height="15"></a><a href="javascript:AddPro('14', 'NewsText');"><img border="0" src="_imgs/pro/i14.gif" alt="���-�������" width="15" height="15"></a><a href="javascript:AddPro('15', 'NewsText');"><img border="0" src="_imgs/pro/i15.gif" alt="���-������" width="15" height="15"></a><a href="javascript:AddPro('13', 'NewsText');"><img border="0" src="_imgs/pro/i13.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('17w', 'NewsText');"><img border="0" src="_imgs/pro/i17w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('30', 'NewsText');"><img border="0" src="_imgs/pro/i30.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('26', 'NewsText');"><img border="0" src="_imgs/pro/i26.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('27', 'NewsText');"><img border="0" src="_imgs/pro/i27.gif" alt="�����" width="15" height="15"></a><a href="javascript:AddPro('28', 'NewsText');"><img border="0" src="_imgs/pro/i28.gif" alt="�����" width="15" height="15"></a><br>
							<a href="javascript:AddPro('0w', 'NewsText');"><img border="0" src="_imgs/pro/i0w.gif" width="15" height="15"></a><a href="javascript:AddPro('1w', 'NewsText');"><img border="0" src="_imgs/pro/i1w.gif" alt="������" width="15" height="15"></a><a href="javascript:AddPro('2w', 'NewsText');"><img border="0" src="_imgs/pro/i2w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('3w', 'NewsText');"><img border="0" src="_imgs/pro/i3w.gif" alt="�������� :)" width="15" height="15"></a><a href="javascript:AddPro('4w', 'NewsText');"><img border="0" src="_imgs/pro/i4w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('5w', 'NewsText');"><img border="0" src="_imgs/pro/i5w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('6w', 'NewsText');"><img border="0" src="_imgs/pro/i6w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('7w', 'NewsText');"><img border="0" src="_imgs/pro/i7w.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('8w', 'NewsText');"><img border="0" src="_imgs/pro/i8w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('9w', 'NewsText');"><img border="0" src="_imgs/pro/i9w.gif" alt="����������" width="15" height="15"></a><a href="javascript:AddPro('10w', 'NewsText');"><img border="0" src="_imgs/pro/i10w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('11w', 'NewsText');"><img border="0" src="_imgs/pro/i11w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('12w', 'NewsText');"><img border="0" src="_imgs/pro/i12w.gif" alt="�������" width="15" height="15"></a><a href="javascript:AddPro('16w', 'NewsText');"><img border="0" src="_imgs/pro/i16w.gif" alt="���-�����" width="15" height="15"></a><a href="javascript:AddPro('14w', 'NewsText');"><img border="0" src="_imgs/pro/i14w.gif" alt="���-�������" width="15" height="15"></a><a href="javascript:AddPro('15w', 'NewsText');"><img border="0" src="_imgs/pro/i15w.gif" alt="���-������" width="15" height="15"></a><a href="javascript:AddPro('13w', 'NewsText');"><img border="0" src="_imgs/pro/i13w.gif" alt="���������" width="15" height="15"></a><a href="javascript:AddPro('17w', 'NewsText');"><img border="0" src="_imgs/pro/i17w.gif" alt="��������" width="15" height="15"></a><a href="javascript:AddPro('30w', 'NewsText');"><img border="0" src="_imgs/pro/i30w.gif" alt="�����" width="15" height="15"></a><br>
							<div style="margin: 10px 0; line-height: 30px;">
							<?
							!isset($_GET['Id'])?$id_sec=$_GET['type']:$id_sec=$content['id_sec'];
							?>
							<select size="1" name="subsection">
								<? foreach ($subsection[$id_sec] as $key => $value): ?>
									<option value="<?=$key?>"<?=(@$content['subsection']==$key?' selected':'')?>><?=$value?></option>
								<? endforeach; ?>
							</select>
							���. �������: <input name="b_l" type="text" value="<?=@$extra[0]?>" style="width: 30px;" maxlength="2">
							����. �������: <input name="e_l" type="text" value="<?=@$extra[1]?>" style="width: 30px;" maxlength="2"><br />
							���������: <input name="prof" type="text" value="<?=@$extra[2]?>">
							����: <input name="prize" type="text" value="<?=@$extra[3]?>">
							���������: <input name="reputation" type="text" value="<?=@$extra[4]?>">
							����������: <input name="other" type="text" value="<?=@$extra[5]?>">
							�����: <input name="poster" type="text" value="<?=@$extra[6]?>">
							<br>
							������� : <input name="cooldown" type="text" value="<?=@$extra[7]?>">
							���������: <input name="complexity" type="text" value="<?=@$extra[8]?>">
							�������: <select name="fraction"><option value="">�������</option><option value="Freedom"<? if (@$extra[9] == 'Freedom') echo (' selected');?>>Freedom</option><option value="Ordnung"<? if (@$extra[9] == 'Ordnung') echo (' selected');?>>Ordnung</option></select>
							������: <input name="quests" type="text" value="<?=@$extra[10]?>">
							</div>
							<!--p>��������� �����������: <label><input name="allow_comments" type="radio" value="0"<?=($d['allow_comments']==0?' checked':'')?>> ���</label> <label><input name="allow_comments" type="radio" value="1"<?=($d['allow_comments']==1?' checked':'')?>> ��</label></p-->
							<input name="prv_subm" type="button" value="��������������� ��������" onClick="preview_news();">
							<input name="add_subm" type="submit" value="��������">
						</form>
						</td>
						</tr>
					</table>
			<!-- Editor area end -->
					<br>
			<!-- Preview area start -->
			<b>��������������� ��������</b>
			<div id="preview_area">�</div>
			<!-- Preview area end -->

			<?

	elseif ($_GET['action']=='delete' && (abs(AccessLevel) & AccessArticles)):

		$id = (int)$_GET['Id'];
//		$query = mysql_query("SELECT `id`, `id_sec`, `title`, `text`, `post_date`, `poster_id`, `allow_comments`, `markup`, `subsection`, `extra` FROM `site_data` WHERE `id` = '$id LIMIT 1");
//		$content = mysql_fetch_array($query);
		mysql_query("DELETE FROM `site_data` WHERE `id` = '$id' LIMIT 1") or die(mysql_error());
		?>
		<script language="javascript">self.location.href = "<?=$_SERVER['PHP_SELF'].'?act='.$_GET['act'].'&type='.$_GET['type']?>";</script>
		<?

	endif;
endif;

?>