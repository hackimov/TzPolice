<?
$megaboss = 0;
if (AuthUserGroup>1)
	{
    	$megaboss = 1;
    }
else
	{
    	$megaboss = 0;
    }
if (AuthUserGroup == '100') {
if($_REQUEST['f'] && $_REQUEST['th']) {
$f=$_REQUEST['f'];
$th=$_REQUEST['th'];
//    	if ($freelance["$f"]['restr'] == AuthUserRestrAccess || AuthUserRestrAccess == 100)
if ($freelance["$f"]['restr'] < 1 || substr_count(AuthUserRestrAccess, $freelance["$f"]["nm"]) > 0 || AuthUserGroup==100)
       	{
				if ($freelance["$f"]['restr'] > 0)
                	{
                    	$megaboss = 1;
                    }
    $SQL="SELECT th.*, u.id AS user_id, u.user_name AS user_name, u.clan
    	FROM freelance_threads th LEFT JOIN site_users u ON th.poster_id=u.id
        WHERE th.id_sec='$f' AND th.id='$th'
    ";
    $r=mysql_query($SQL);
   if(mysql_num_rows($r)>0) {
    $thread=mysql_fetch_array($r);
    mysql_query("UPDATE freelance_threads SET cnt_views=cnt_views+1 WHERE id='$th'");
	echo "<h1>{$thread['title']}</h1>";
  	if($_REQUEST['p']>0) $p=$_REQUEST['p'];
	else $p=1;
#
# admin
#
        if(@$_REQUEST['do']=="close" && $megaboss>0) {
//определяем что писать в причине закрытия темы
            $userinfo = GetUserInfo(AuthUserName);
            if (!$userinfo["error"])
                {
                    if ($userinfo['man'] == 0)
                        {
	                        $pro = $userinfo['pro']."w";
	                    }
	                else
	                    {
	                        $pro = $userinfo['pro'];
	                    }
		            $_RESULT = array("res" => "OK");
                    if ($userinfo['level'] > 0)
                    	{
		                    if (strlen($userinfo['clan']) > 2)
	    		                {
	            		        	$usr_full = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
			                else
	    		            	{
	            		        	$usr_full = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
                        }
                    else
                    	{
                        	$usr_full = "[b]".AuthUserName."[/b]";
                        }
                }
            else
            	{
                	$usr_full = "[b]".AuthUserName."[/b]";
                }
                $DelText="[color=red]Обсуждение закрыто модератором[/color] ".$usr_full;
//определили

			$SQL="INSERT INTO freelance_replies (id_thread,poster_id,post_date,text) values('$th','".AuthUserId."','".time()."','".addslashes($DelText)."')";
			mysql_query($SQL);
			$SQL="UPDATE freelance_threads SET allow_comments='0' WHERE id='$th'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_threads&f={$f}'</script>";
        } elseif(@$_REQUEST['do']=="open" && $megaboss>0) {
//определяем что писать в причине восстановления (Трю)
            $userinfo = GetUserInfo(AuthUserName);
            if (!$userinfo["error"])
	            {
	                if ($userinfo['man'] == 0)
	                    {
	                        $pro = $userinfo['pro']."w";
	                    }
	                else
	                    {
	                        $pro = $userinfo['pro'];
	                    }
		            $_RESULT = array("res" => "OK");
                    if ($userinfo['level'] > 0)
                    	{
		                    if (strlen($userinfo['clan']) > 2)
	    		                {
	            		        	$usr_full = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
			                else
	    		            	{
	            		        	$usr_full = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
                        }
                    else
                    	{
                        	$usr_full = "[b]".AuthUserName."[/b]";
                        }
                }
            else
            	{
                	$usr_full = "[b]".AuthUserName."[/b]";
                }
                $DelText="[color=green]Обсуждение продолжено модератором[/color] ".$usr_full;
//определили

			$SQL="INSERT INTO freelance_replies (id_thread,poster_id,post_date,text) values('$th','".AuthUserId."','".time()."','".addslashes($DelText)."')";
			mysql_query($SQL);
			$SQL="UPDATE freelance_threads SET allow_comments='1' WHERE id='$th'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_threads&f={$f}'</script>";
        } elseif(@$_REQUEST['do']=="del" && @$_REQUEST['postid'] && $megaboss>0) {

//определяем что писать в причине удаления поста
            $userinfo = GetUserInfo(AuthUserName);
            if (!$userinfo["error"])
                {
                    if ($userinfo['man'] == 0)
                        {
	                        $pro = $userinfo['pro']."w";
	                    }
	                else
	                    {
	                        $pro = $userinfo['pro'];
	                    }
		            $_RESULT = array("res" => "OK");
                    if ($userinfo['level'] > 0)
                    	{
		                    if (strlen($userinfo['clan']) > 2)
	    		                {
	            		        	$usr_full = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
			                else
	    		            	{
	            		        	$usr_full = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    		}
                        }
                    else
                    	{
                        	$usr_full = "[b]".AuthUserName."[/b]";
                        }
                }
            else
            	{
                	$usr_full = "[b]".AuthUserName."[/b]";
                }
                $DelText="[color=red]Удалено модератором[/color] ".$usr_full;
//определили

	        $SQL="UPDATE freelance_replies SET text='".addslashes($DelText)."' WHERE id='".$_REQUEST['postid']."'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_replies&f={$f}&th={$th}&p={$p}'</script>";
        } elseif(@$_REQUEST['do']=="attach" && $megaboss>0) {
			$SQL="UPDATE freelance_threads SET is_attached='1' WHERE id='$th'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_threads&f={$f}'</script>";
        } elseif(@$_REQUEST['do']=="unattach" && $megaboss>0) {
			$SQL="UPDATE freelance_threads SET is_attached='0' WHERE id='$th'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_threads&f={$f}'</script>";
        } elseif(@$_REQUEST['do']=="delall" && $megaboss>0) {
			$SQL="DELETE FROM freelance_threads WHERE id='$th'";
			mysql_query($SQL);
			$SQL="DELETE FROM freelance_replies WHERE id_thread='$th'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=freelance_threads&f={$f}'</script>";
        }
#
# add
#
if(strlen($_REQUEST['NewsText'])>4) {
   	$SQL="SELECT banned, post_time FROM site_users WHERE id='".AuthUserId."'";
	$d=mysql_fetch_array(mysql_query($SQL));
	if($d['banned']>time()) {
		$MolDiff=$d['banned']-time();
		echo "<h2>Вы не можете писать на форуме еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2>";
	} elseif($d['post_time']+30>time()) {
		echo "<h2>Вы не можете писать ответы в топиках чаще, чем раз в 30 секунд</h2>";
	} elseif ($thread['allow_comments']==0) {
		echo "<h2>Обсуждение уже закрыто! Не фиг хачить!</h2>";
	} else {
        $SQL="INSERT INTO freelance_replies(id_thread,text,post_date,poster_id)
            values('$th','".addslashes($_REQUEST['NewsText'])."',
        	'".time()."','".AuthUserId."')
        ";
       mysql_query($SQL);
        $SQL="UPDATE site_users SET post_time='".time()."' WHERE id='".AuthUserId."'";
		mysql_query($SQL);
        $SQL="UPDATE freelance_threads SET last_date='".time()."' WHERE id='$th'";
		mysql_query($SQL);
		echo "<script>top.location.href='?act=freelance_threads&f=".$_REQUEST['f']."'</script>";
    }
}
#
# // add
#
?>
    <p>
    <a href='?act=freelance_threads'>Форумы</a> &raquo;
    <a href='?act=freelance_threads&f=<?=$f?>'><?=$freelance["$f"]['name']?></a> &raquo;
	<a href='?act=freelance_replies&f=<?=$f?>&th=<?=$th?>'><?=$thread['title']?></a>
    </p>
<?
    $SQL="SELECT re.*, u.id AS user_id, u.user_name AS user_name, u.clan
    	FROM freelance_replies re LEFT JOIN site_users u ON re.poster_id=u.id
        WHERE re.id_thread='$th'
        ORDER BY post_date";
    $r=mysql_query($SQL);
        $pages=ceil(mysql_num_rows($r)/$RepliesPP);
        if(!$pages) $pages=1;
        $LimitParam=$p*$RepliesPP-$RepliesPP;
        $SQL.=" LIMIT $LimitParam, $RepliesPP";
        $r=mysql_query($SQL);
        echo "<br><div align=right class='v10'>Страницы: <b>";
        ShowPages($p,$pages,5,"act=freelance_replies&f={$f}&th={$th}");
        echo "</b></div>";
		echo "<table width=100% cellpadding=6 cellspacing=1>";
        if($p==1) {
    	    echo "
	        	<tr>
			    <td valign=top width=150 nowrap background='i/bgr-grid-sand.gif' class='v9' align=center>
            ";
			if(AuthStatus==1 && $thread['allow_comments']!=0) echo "<a href=\"JavaScript:insertAtCaret(top.News.NewsText, '[b]".$thread['user_name'].",[/b] ')\"><img border=0 src='/i/to.gif'></a>";
            echo "
				".GetClan($thread['clan']).GetUser($thread['user_id'],$thread['user_name'],AuthUserGroup)."<br>
				".date("d.m.y H:i",$thread['post_date']);
            echo "
				</td><td width=100% valign=top  background='i/bgr-grid-sand.gif'>
        ";
            ParseNews2($thread['text'],0);
    	    echo "
				</td>
            	</tr>
			";
        }
		for($i=0;$i<mysql_num_rows($r);$i++) {
        $d=mysql_fetch_array($r);
		echo "
			<tr>
			<td valign=top width=150 nowrap background='i/bgr-grid-sand1.gif' class='v9' align=center>
        ";
		if(AuthStatus==1 && $thread['allow_comments']!=0) echo "<a href=\"JavaScript:insertAtCaret(top.News.NewsText, '[b]".$d['user_name'].",[/b] ')\"><img border=0 src='/i/to.gif'></a>";
        echo "
	        ".GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup)."<br>
			".date("d.m.y H:i",$d['post_date']);
		if($megaboss>0) echo "<br><a href='#;return false' onclick=\"if(confirm('Удалить пост?')) top.location.href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=del&postid=".$d['id']."'\">удалить</a> ";
        echo "
			</td><td width=100% valign=top  background='i/bgr-grid-sand.gif'>
        ";
		ParseNews2($d['text'],0);
		echo "</td></tr>";
		}?>
   </table>
<?
#
# ADD
#
echo "<br><div align=right class='v10'>Страницы: <b>";
ShowPages($p,$pages,5,"act=freelance_replies&f={$f}&th={$th}");
echo "</b></div>";
echo "<div class=vd9>";
if($megaboss>0 && $thread['allow_comments']!=0) echo " <a href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=close'>Закрыть обсуждение</a> ";
if($megaboss>0 && $thread['allow_comments']==0) echo " <a href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=open'>Продолжить обсуждение</a> ";
if($megaboss>0 && $thread['is_attached']!=0) echo " / <a href='#;return false' onclick=\"if(confirm('открепить топик с верхушки форума?')) top.location.href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=unattach'\">открепить</a> ";
if($megaboss>0 && $thread['is_attached']==0) echo " / <a href='#;return false' onclick=\"if(confirm('Закрепить наверху форума?')) top.location.href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=attach'\">закрепить</a> ";
if($megaboss>0) echo " / <a href='#;return false' onclick=\"if(confirm('Удалить весь топик?')) top.location.href='?act=freelance_replies&th={$th}&f={$f}&p={$p}&do=delall'\">удалить топик</a> ";
echo "</div>";
if(AuthStatus!=1) echo $mess['CantAddReply'].$mess['WantRegister'];
else {
        $SQL="SELECT banned FROM site_users WHERE id='".AuthUserId."'";
        $d=mysql_fetch_array(mysql_query($SQL));
        if($d['banned']>time()) {
                $MolDiff=$d['banned']-time();
            echo "<h2>Вы не можете оставлять публикации на форуме еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
        } else {
            if($thread['allow_comments']==0) echo "<h2>Обсуждение закрыто</h2>";
        else {
?>
<SCRIPT src='_modules/xhr_js.js'></SCRIPT>
<SCRIPT src='_modules/news_js.js'></SCRIPT>
<script language="JavaScript1.2">
<!--
function loadNick()
	{
		var query = '' + document.getElementById('ins_nick').value;
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
						if (req.responseJS)
                        	{
                                if (req.responseJS.res == 'OK')
                                	{
	                                    insertAtCaret(document.getElementById('NewsText'), req.responseText);
	                                    document.getElementById('plzwait').innerHTML = '';
                                    }
                                else
                                	{
	                                    document.getElementById('plzwait').innerHTML = req.responseText;

                                   }
							}
					}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/charinfo.php', true);
		document.getElementById("plzwait").innerHTML = '<center><b>Пожалуйста, подождите...</b></center>';
		req.send({ n: query });
	}
function preview_news()
	{
        var q_text = '' + document.getElementById('NewsText').value;
		var req = new Subsys_JsHttpRequest_Js();
		req.onreadystatechange = function()
        	{
				if (req.readyState == 4)
                	{
						if (req.responseJS)
                        	{
                                document.getElementById('preview_area').innerHTML = req.responseText;
							}

				}
            }
		req.caching = false;
		req.open('POST', '_modules/backends/news_preview.php', true);
		document.getElementById("preview_area").innerHTML = '<center><br>Пожалуйста, подождите...<br><br></center>';
		req.send({te: q_text, ta: "<?=AuthUserName?>" });
	}
var smileWin = 0;
function smileWindow(obj)
	{
	    if(smileWin)

		{
				if(!smileWin.closed) smileWin.close();
			}
	    win_left = obj.clientX + 15;
	    win_top = obj.clientY + 15;
        smileWin = open('_modules/icons/smiles2.php', 'smiles', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=both,resizable=no,width=350,height=250,scrollbars=1,left='+win_left+', top='+win_top+',screenX='+win_left+',screenY='+win_top);
	}
function AddRemark(cid) {
	if (rem = window.prompt("Введите ответ 'от модератора'", ""))
    	{
			if (!rem) alert("Пустой ответ недопустим!")
		    else
    			{
		            document.add_remark.txt.value = rem;
		            document.add_remark.c_id.value = cid;
					document.add_remark.submit();
			    }
        }
	}
function quotate(nick)
	{
		var txt='';
		if (document.getSelection) {txt=document.getSelection()}
		else if (document.selection) {txt=document.selection.createRange().text;}
        if (txt == '')
        	{
				txt=nick;
            }
        else
        	{
		        txt=nick+" писал(а): \n[quote]"+txt+'[/quote]\n';
            }
        insertAtCaret(document.getElementById('NewsText'), txt)
	}
//-->
</script>
<table width='100%' border='0' cellspacing='1' cellpadding='6'>
<td height=20 background='i/bgr-grid-sand1.gif'>
<strong>Оставить ответ:</strong>
</td></tr></table>
<table width=100% cellpadding=5>
<tr>
<td valign=top>
        <form method="POST" name="News">
        <input name="act" type="hidden" value="freelance_replies">
       <input name="f" type="hidden" value="<?=$f?>">
       <input name="th" type="hidden" value="<?=$th?>">
        <input name="p" type="hidden" value="<?=$p?>">

              цвет:
				<select name="fc" onChange="fontstyle('[color=' + this.form.fc.options[this.form.fc.selectedIndex].value + ']', '[/color]','NewsText');this.selectedIndex=0;">
					  <option value="none">По умолчанию</option>
					  <option style="color:darkred;" value="darkred">Тёмно-красный</option>
					  <option style="color:red;" value="red">Красный</option>
					  <option style="color:orange;" value="orange">Оранжевый</option>
					  <option style="color:brown;" value="brown">Коричневый</option>
					  <option style="color:green;" value="green">Зелёный</option>
					  <option style="color:olive;" value="olive">Оливковый</option>
					  <option style="color:blue;" value="blue">Синий</option>
					  <option style="color:darkblue;" value="darkblue">Тёмно-синий</option>
					  <option style="color:indigo;" value="indigo">Индиго</option>
					  <option style="color:violet;" value="violet">Фиолетовый</option>
					  <option style="color:white;" value="white">Белый</option>
				  <option style="color:black;" value="black">Чёрный</option>
				</select>
				&nbsp;
              <select name="ins_clan" id="ins_clan" size="1" onChange="AddClan(this.form.ins_clan.options[this.form.ins_clan.selectedIndex].value, 'NewsText');this.selectedIndex=0;">
			<option value="none" selected>Кланы</option>
<?
$smiles_dir="_imgs/clans/";
$d=opendir($smiles_dir);
$counter = 0;
while(($CurFile=readdir($d))!==false) if(is_file("$smiles_dir/$CurFile")) {
	$FileType=GetImageSize("$smiles_dir/$CurFile");
	$CurFile=explode(".",$CurFile);
    if($FileType[2]==1)
    	{
			$clans[$counter] = $CurFile[0];
            $counter++;
        }
}

closedir($d);
natcasesort($clans);
reset($clans);
while (list($key, $val) = each($clans))
	{
         if ($val !== "0")
         	{
			echo ("<option value='".$val."'>".$val."</option>");
            	}
	}
?>
              </select>
              &nbsp;
			  ник:
              <input name="ins_nick" id="ins_nick" type="text" size="16"><input name="in_subm" type="button" class="ed_dark" value="ок" onClick="loadNick()">
              <br>
				<div id="plzwait"><br></div>
              <table width="100%" border="0">
              <tr><td width="*">
              <textarea ONSELECT="storeCaret(this);" ONCLICK="storeCaret(this);" ONKEYUP="storeCaret(this);" name="NewsText" id="NewsText" rows="15" wrap="VIRTUAL" style="width:95%"></textarea>
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
              <a href="javascript:AddTag('[b]','[/b]','NewsText')"><img border="0" src="_imgs/editor/bold.gif" alt="полужирный" width="24" height="24"></a><a href="javascript:AddTag('[i]','[/i]','NewsText')"><img border="0" src="_imgs/editor/italic.gif" alt="курсив" width="24" height="24"></a><a href="javascript:AddTag('[u]','[/u]','NewsText')"><img border="0" src="_imgs/editor/underline.gif" alt="подчеркнутый" width="24" height="24"></a>&nbsp;<a href="javascript:AddUrl('NewsText')"><img border="0" src="_imgs/editor/hyperlink.gif" alt="ссылка" width="24" height="24"></a><a href="#; return false;" onClick="smileWindow(event);"><img border="0" src="_imgs/editor/smile.gif" alt="смайлики" width="24" height="24"></a>&nbsp;<a href="javascript:AddTag('[quote]','[/quote]','NewsText')"><img border="0" src="_imgs/editor/quote.gif" alt="цитата" width="75" height="24"></a><a href="javascript:AddHidden('NewsText')"><img border="0" src="_imgs/editor/hidden.gif" alt="скрытый текст" width="75" height="24"></a>&nbsp;
              <br>
              <a href="javascript:AddPro('0', 'NewsText');"><img border="0" src="_imgs/pro/i0.gif" width="15" height="15"></a><a href="javascript:AddPro('1', 'NewsText');"><img border="0" src="_imgs/pro/i1.gif" alt="корсар" width="15" height="15"></a><a href="javascript:AddPro('2', 'NewsText');"><img border="0" src="_imgs/pro/i2.gif" alt="сталкер" width="15" height="15"></a><a href="javascript:AddPro('3', 'NewsText');"><img border="0" src="_imgs/pro/i3.gif" alt="шахтер" width="15" height="15"></a><a href="javascript:AddPro('4', 'NewsText');"><img border="0" src="_imgs/pro/i4.gif" alt="инженер" width="15" height="15"></a><a href="javascript:AddPro('5', 'NewsText');"><img border="0" src="_imgs/pro/i5.gif" alt="наемник" width="15" height="15"></a><a href="javascript:AddPro('6', 'NewsText');"><img border="0" src="_imgs/pro/i6.gif" alt="торговец" width="15" height="15"></a><a href="javascript:AddPro('7', 'NewsText');"><img border="0" src="_imgs/pro/i7.gif" alt="патрульный" width="15" height="15"></a><a href="javascript:AddPro('8', 'NewsText');"><img border="0" src="_imgs/pro/i8.gif" alt="штурмовик" width="15" height="15"></a><a href="javascript:AddPro('9', 'NewsText');"><img border="0" src="_imgs/pro/i9.gif" alt="специалист" width="15" height="15"></a><a href="javascript:AddPro('10', 'NewsText');"><img border="0" src="_imgs/pro/i10.gif" alt="журналист" width="15" height="15"></a><a href="javascript:AddPro('11', 'NewsText');"><img border="0" src="_imgs/pro/i11.gif" alt="чиновник" width="15" height="15"></a><a href="javascript:AddPro('12', 'NewsText');"><img border="0" src="_imgs/pro/i12.gif" alt="псионик" width="15" height="15"></a><a href="javascript:AddPro('16', 'NewsText');"><img border="0" src="_imgs/pro/i16.gif" alt="пси-лидер" width="15" height="15"></a><a href="javascript:AddPro('14', 'NewsText');"><img border="0" src="_imgs/pro/i14.gif" alt="пси-кинетик" width="15" height="15"></a><a href="javascript:AddPro('15', 'NewsText');"><img border="0" src="_imgs/pro/i15.gif" alt="пси-медиум" width="15" height="15"></a><a href="javascript:AddPro('13', 'NewsText');"><img border="0" src="_imgs/pro/i13.gif" alt="каторжник" width="15" height="15"></a><a href="javascript:AddPro('30', 'NewsText');"><img border="0" src="_imgs/pro/i30.gif" alt="дилер" width="15" height="15"></a><a href="javascript:AddPro('26', 'NewsText');"><img border="0" src="_imgs/pro/i26.gif" alt="ропат" width="15" height="15"></a><a href="javascript:AddPro('27', 'NewsText');"><img border="0" src="_imgs/pro/i27.gif" alt="ропат" width="15" height="15"></a><a href="javascript:AddPro('28', 'NewsText');"><img border="0" src="_imgs/pro/i28.gif" alt="ропат" width="15" height="15"></a>
                <br>
			  <a href="javascript:AddPro('0w', 'NewsText');"><img border="0" src="_imgs/pro/i0w.gif" width="15" height="15"></a><a href="javascript:AddPro('1w', 'NewsText');"><img border="0" src="_imgs/pro/i1w.gif" alt="корсар" width="15" height="15"></a><a href="javascript:AddPro('2w', 'NewsText');"><img border="0" src="_imgs/pro/i2w.gif" alt="сталкер" width="15" height="15"></a><a href="javascript:AddPro('3w', 'NewsText');"><img border="0" src="_imgs/pro/i3w.gif" alt="шахтер" width="15" height="15"></a><a href="javascript:AddPro('4w', 'NewsText');"><img border="0" src="_imgs/pro/i4w.gif" alt="инженер" width="15" height="15"></a><a href="javascript:AddPro('5w', 'NewsText');"><img border="0" src="_imgs/pro/i5w.gif" alt="наемник" width="15" height="15"></a><a href="javascript:AddPro('6w', 'NewsText');"><img border="0" src="_imgs/pro/i6w.gif" alt="торговец" width="15" height="15"></a><a href="javascript:AddPro('7w', 'NewsText');"><img border="0" src="_imgs/pro/i7w.gif" alt="патрульный" width="15" height="15"></a><a href="javascript:AddPro('8w', 'NewsText');"><img border="0" src="_imgs/pro/i8w.gif" alt="штурмовик" width="15" height="15"></a><a href="javascript:AddPro('9w', 'NewsText');"><img border="0" src="_imgs/pro/i9w.gif" alt="специалист" width="15" height="15"></a><a href="javascript:AddPro('10w', 'NewsText');"><img border="0" src="_imgs/pro/i10w.gif" alt="журналист" width="15" height="15"></a><a href="javascript:AddPro('11w', 'NewsText');"><img border="0" src="_imgs/pro/i11w.gif" alt="чиновник" width="15" height="15"></a><a href="javascript:AddPro('12w', 'NewsText');"><img border="0" src="_imgs/pro/i12w.gif" alt="псионик" width="15" height="15"></a><a href="javascript:AddPro('16w', 'NewsText');"><img border="0" src="_imgs/pro/i16w.gif" alt="пси-лидер" width="15" height="15"></a><a href="javascript:AddPro('14w', 'NewsText');"><img border="0" src="_imgs/pro/i14w.gif" alt="пси-кинетик" width="15" height="15"></a><a href="javascript:AddPro('15w', 'NewsText');"><img border="0" src="_imgs/pro/i15w.gif" alt="пси-медиум" width="15" height="15"></a><a href="javascript:AddPro('13w', 'NewsText');"><img border="0" src="_imgs/pro/i13w.gif" alt="каторжник" width="15" height="15"></a><a href="javascript:AddPro('30w', 'NewsText');"><img border="0" src="_imgs/pro/i30w.gif" alt="дилер" width="15" height="15"></a>
              	<br>
                <input name="prv_subm" type="button" value="Предварительный просмотр" onClick="preview_news();">
                <input name="add_subm" type="submit" value="Добавить">
	</form>
    <b>Предварительный просмотр</b>
<div id="preview_area">...</div>

</td></tr>
</table>
<?}}}
} else echo "<h2>Указаная ветка не найдена</h2>";
}
}
} else echo $mess['AccessDenied'];
?>