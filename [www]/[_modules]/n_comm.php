<h1>Комментарии к новости</h1>
<?php
	$vstr[0] = '';
	$vstr[1] = '<strong style="color: green">[внутренняя police] </strong>';
	$vstr[2] = '<strong style="color: blue">[внутренняя] </strong>';			
	if(abs(AccessLevel) & AccesInnerNews) {	
		$v = '';
		$v2 = '';
	} elseif (AuthUserClan=='Military Police') {
		$v = ' AND (our_news=\'0\' OR our_news=\'2\')';
		$v2 = ' AND (our_news=\'0\' OR our_news=\'2\')';
	} else {
		$v = ' AND our_news=\'0\'';
		$v2 = ' AND our_news=\'0\'';
	}
if(@$_REQUEST['added']==1) echo "<div class=green>Комментарий добавлен</div><br>";
if(@$_REQUEST['IdNews']) {
		if(@$_REQUEST['add_rem']=="1" && (AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))) {
            $SQL="UPDATE comments SET moder_answer='".addslashes($_REQUEST['txt'])."' WHERE id='".$_REQUEST['c_id']."'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
        }
        if(@$_REQUEST['do']=="close" && (AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))) {
            $userinfo = GetUserInfo(AuthUserName);
            if (!$userinfo["error"] && $userinfo['level'] > 0)
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
                    if (strlen($userinfo['clan']) > 2)
	                    {
	                    	$usr_full = "[pers clan={$userinfo['clan']} nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    }
	                else
	                	{
	                    	$usr_full = "[pers clan=0 nick={$userinfo['login']} level={$userinfo['level']} pro={$pro}]";
	                    }
                }
                $DelText="[color=red]Обсуждение закрыто модератором[/color] ".$usr_full;
                $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text,ver) values('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($DelText)."','2')";
                mysql_query($SQL);
                $SQL="UPDATE news SET allow_comments='0' WHERE id='".$_REQUEST['IdNews']."'";
                mysql_query($SQL) or die(mysql_error());
                echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
        } elseif(@$_REQUEST['do']=="open" && (AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))) {
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
                $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text,ver) values('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($DelText)."','2')";
                mysql_query($SQL);
            $SQL="UPDATE news SET allow_comments='1' WHERE id='".$_REQUEST['IdNews']."'";
            mysql_query($SQL);
            echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
        }
        if(@$_REQUEST['do']=="delete" && @$_REQUEST['IdComment'] && (AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))) {
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
                $SQL="UPDATE comments SET deleted_by='".$usr_full."' WHERE id='".$_REQUEST['IdComment']."'";
		        mysql_query($SQL);
        echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
    	}
        if(@$_REQUEST['do']=="return" && @$_REQUEST['IdComment'] && (AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))) {
                $SQL="UPDATE comments SET deleted_by='' WHERE id='".$_REQUEST['IdComment']."'";
		        mysql_query($SQL);
        echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
    	}
        if(@$_REQUEST['do']=="add" && @$_REQUEST['NewsText'] && AuthStatus==1) {
                $SQL="SELECT allow_comments, our_news FROM news WHERE id='".$_REQUEST['IdNews']."'";
                $d=mysql_fetch_array(mysql_query($SQL));
        $AllowComments=$d['allow_comments'];
//		if ($d['our_news'] && !(abs(AccessLevel) & AccesInnerNews)) $AllowComments=0;
                $SQL="SELECT banned, post_time FROM site_users WHERE id='".AuthUserId."'";
                $d=mysql_fetch_array(mysql_query($SQL));
                if($d['banned']>time()) {
                        $MolDiff=$d['banned']-time();
                    echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
                } elseif($d['post_time']+30>time()) {
                $oldcomm=stripslashes($_REQUEST['NewsText']);
                    echo "<h2>Вы не можете оставлять комментарии чаще, чем раз в 30 секунд</h2><br><br>";
                } elseif ($AllowComments==0) {
                echo "<h2>Обсуждение закрыто</h2><br><br>";
                } else {
            $userip = $_SERVER['REMOTE_ADDR'];
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
            $SQL="INSERT INTO comments (id_news,id_user,comment_date,comment_text,ver,user_full,ip)
            values
            ('".$_REQUEST['IdNews']."','".AuthUserId."','".time()."','".addslashes($_REQUEST['NewsText'])."','2','".$usr_full."','".$userip."')";
            mysql_query($SQL) or die(mysql_error());
			$SQL="UPDATE site_users SET post_time='".time()."' WHERE id='".AuthUserId."'";
	        mysql_query($SQL);
            echo "<script>top.location.href='?act=n_comm&IdNews=".$_REQUEST['IdNews']."&p=".$_REQUEST['p']."'</script>";
            }
        }

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
<form name="add_remark" method="post" action="?act=n_comm">
  <input type="hidden" name="txt" value="">
  <input type="hidden" name="IdNews" value="<?=$_REQUEST['IdNews']?>">
  <input type="hidden" name="p" value="<?=$_REQUEST['p']?>">
  <input type="hidden" name="c_id" value="0">
  <input type="hidden" name="add_rem" value="1">
</form>
<?
$SQL="SELECT * FROM news WHERE is_visible=1".$v." AND id='".$_REQUEST['IdNews']."'";
$r=mysql_query($SQL);
if(mysql_num_rows($r)<1) echo $mess['NewsNotFound'];
else {
        $d=mysql_fetch_array($r);
        $AllowComments=$d['allow_comments'];
        if($d['allow_tags']==0) $allow_tags=0;
        else $allow_tags=1;
    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>[".date("d.m.Y - H:i",$d['news_date'])."] </strong> ".$vstr[$d['our_news']]."<span class='top-header'>".stripslashes(strip_tags($d['news_title']))."</span></p></td>
		</tr><tr><td>
    ";
    if ($d['markup'] == 0)
    	{
		    ParseNews($d['news_text'],$d['allow_tags']);
        }
    elseif ($d['markup'] == 1)
    	{
            ParseNews2($d['news_text']);
        }
	echo "
		</td></tr><tr>
		<td height=20 background='i/bgr-grid-sand1.gif'>
		<strong> Комментарии: </strong>
        </td></tr></table>
	";
        $SQL="SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.id_news='".$_REQUEST['IdNews']."' ORDER BY c.comment_date ";
    $r=mysql_query($SQL);
        if(mysql_num_rows($r)<1) echo $mess['NoComments'];
    else {
         $pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
        if($_REQUEST['p']>0) $p=$_REQUEST['p'];
        else $p=1;
        $LimitParam=$p*$CommentsPerPage-$CommentsPerPage;
        $SQL.=" LIMIT $LimitParam, $CommentsPerPage";
        $r=mysql_query($SQL);
        echo "<br><div align=right>Страницы: <b>";
        ShowPages($p,$pages,4,"act=n_comm&IdNews={$_REQUEST['IdNews']}");
        echo "</b></div>";
        for($i=0;$i<mysql_num_rows($r);$i++) {
            $d=mysql_fetch_array($r);
        if ($d['ver'] == '2')
        	{
	            echo "
	                    <br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>
	                    <td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>
	                ";
	                if(AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))
	                    {
	                        echo ("<a href='#;return false' onclick=\"AddRemark('".$d['id']."');\">ответить</a> / ");
	                        if ($d['deleted_by'] == "")
	                            {
	                                echo "<a href='#;return false' onclick=\"if(confirm('Удалить комментарий?')){top.location='?act=n_comm&IdNews={$d[id_news]}&IdComment={$d[id]}&do=delete&p={$p}'}\">удалить</a> / ";
	                            }
	                        else
	                            {
	                                echo "<a href='#;return false' onclick=\"if(confirm('Вернуть комментарий?')){top.location='?act=n_comm&IdNews={$d[id_news]}&IdComment={$d[id]}&do=return&p={$p}'}\">показывать</a> / ";
	                            }
	                    }
	                echo " ".date("d.m.Y - H:i",$d['comment_date'])." / ";
	                ParseNews2($d['user_full']);
	                if(AuthStatus>0 && strlen($d['user_full']) > 3)
	                    {
	                        echo " / <a href='#;return false' onclick=\"quotate('".$d['user_full']."')\">цитата</a> / ";
	                    }
	                echo"</p></td>
	                    </tr><tr><td>
	                ";
	                if ($d['deleted_by'] == "")
	                    {
	                        ParseNews2($d['comment_text']);
	                    }
	                else
	                    {
	                        echo ("<font color='red'>Удалено модератором</font> ");
	                        ParseNews2($d['deleted_by']);
	                        if(AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor))
	                            {
	                                $str = "\n\n[i]Только для модератора:[/i][quote]".$d['comment_text']."[/quote]";
	                                ParseNews2($str);
	                            }
	                    }
	                if (strlen($d['moder_answer']) > 1)
	                    {
	                        echo ("<br>&nbsp;&nbsp;&nbsp;<font face='Verdana' size='-2'><b>модератор:</b> ");
				ParseNews2(stripslashes($d['moder_answer']));
				echo ("</font>");
	                    }
	                echo "</td></tr></table>";
            }
           else
           	{
	            echo "
	                    <br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>
	                    <td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>
	                ";
	                if(AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor)) echo "<a href='#;return false' onclick=\"if(confirm('Удалить комментарий?')){top.location='?act=n_comm&IdNews={$d[id_news]}&IdComment={$d[id]}&do=delete&p={$p}'}\">удалить</a> / ";
	                echo " ".date("d.m.Y - H:i",$d['comment_date'])." / ";
	                if(AuthStatus==1) echo "<a href=\"JavaScript:insertAtCaret(top.News.NewsText, '<b>".$d['user_name'].",</b> ')\"><img border=0 src='/i/to.gif'></a>";
	                echo GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).":
	                    </p></td>
	                    </tr><tr><td>
	                ";
	                ParseNews($d['comment_text'],0);
	                echo "</td></tr></table>";
            }
        }
    }
?>
<br>
<?
echo "<div align=right>Страницы: <b>";
ShowPages($p,$pages,4,"act=n_comm&IdNews={$_REQUEST['IdNews']}");
echo "</b></div>";
if((AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor)) && $AllowComments!=0) echo "<div>[ <a href='?act=n_comm&do=close&IdNews=".$_REQUEST['IdNews']."&p=$p'>Закрыть обсуждение</a> ]</div>";
if((AuthUserGroup>1 || (abs(AccessLevel) & AccessNewsEditor)) && $AllowComments==0) echo "<div>[ <a href='?act=n_comm&do=open&IdNews=".$_REQUEST['IdNews']."&p=$p'>Продолжить обсуждение</a> ]</div>";
if(AuthStatus<1) echo $mess['CantAddComment'].$mess['WantRegister'];
else {
        $SQL="SELECT banned FROM site_users WHERE id='".AuthUserId."'";
        $d=mysql_fetch_array(mysql_query($SQL));
        if($d['banned']>time()) {
                $MolDiff=$d['banned']-time();
            echo "<h2>Вы не можете оставлять комментарии еще ".gmdate("zдн. Hч iм sс",$MolDiff)."</h2><br><br>";
        } else {
            if($AllowComments==0) echo "<h2>Обсуждение закрыто</h2>";
        else {
?>
<table width='100%' border='0' cellspacing='3' cellpadding='5'>
<td height=20 background='i/bgr-grid-sand1.gif'>
<strong>Оставить комментарий:</strong>
</td></tr></table>
<form name="News" method="post" action="?act=n_comm">
              <input type="hidden" name="do" value="add">
              <input type="hidden" name="IdNews" value="<?=$_REQUEST['IdNews']?>">
              <input type="hidden" name="p" value="<?=$_REQUEST['p']?>">
              <br>
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
              <a href="javascript:AddPro('0', 'NewsText');"><img border="0" src="_imgs/pro/i0.gif" width="15" height="15"></a><a href="javascript:AddPro('1', 'NewsText');"><img border="0" src="_imgs/pro/i1.gif" alt="корсар" width="15" height="15"></a><a href="javascript:AddPro('2', 'NewsText');"><img border="0" src="_imgs/pro/i2.gif" alt="сталкер" width="15" height="15"></a><a href="javascript:AddPro('3', 'NewsText');"><img border="0" src="_imgs/pro/i3.gif" alt="шахтер" width="15" height="15"></a><a href="javascript:AddPro('4', 'NewsText');"><img border="0" src="_imgs/pro/i4.gif" alt="инженер" width="15" height="15"></a><a href="javascript:AddPro('5', 'NewsText');"><img border="0" src="_imgs/pro/i5.gif" alt="наемник" width="15" height="15"></a><a href="javascript:AddPro('6', 'NewsText');"><img border="0" src="_imgs/pro/i6.gif" alt="торговец" width="15" height="15"></a><a href="javascript:AddPro('7', 'NewsText');"><img border="0" src="_imgs/pro/i7.gif" alt="патрульный" width="15" height="15"></a><a href="javascript:AddPro('8', 'NewsText');"><img border="0" src="_imgs/pro/i8.gif" alt="штурмовик" width="15" height="15"></a><a href="javascript:AddPro('9', 'NewsText');"><img border="0" src="_imgs/pro/i9.gif" alt="специалист" width="15" height="15"></a><a href="javascript:AddPro('10', 'NewsText');"><img border="0" src="_imgs/pro/i10.gif" alt="журналист" width="15" height="15"></a><a href="javascript:AddPro('11', 'NewsText');"><img border="0" src="_imgs/pro/i11.gif" alt="чиновник" width="15" height="15"></a><a href="javascript:AddPro('12', 'NewsText');"><img border="0" src="_imgs/pro/i12.gif" alt="псионик" width="15" height="15"></a><a href="javascript:AddPro('13', 'NewsText');"><img border="0" src="_imgs/pro/i13.gif" alt="каторжник" width="15" height="15"></a><a href="javascript:AddPro('30', 'NewsText');"><img border="0" src="_imgs/pro/i30.gif" alt="дилер" width="15" height="15"></a><a href="javascript:AddPro('26', 'NewsText');"><img border="0" src="_imgs/pro/i26.gif" alt="ропат" width="15" height="15"></a><a href="javascript:AddPro('27', 'NewsText');"><img border="0" src="_imgs/pro/i27.gif" alt="ропат" width="15" height="15"></a><a href="javascript:AddPro('28', 'NewsText');"><img border="0" src="_imgs/pro/i28.gif" alt="ропат" width="15" height="15"></a>
                <br>
			  <a href="javascript:AddPro('0w', 'NewsText');"><img border="0" src="_imgs/pro/i0w.gif" width="15" height="15"></a><a href="javascript:AddPro('1w', 'NewsText');"><img border="0" src="_imgs/pro/i1w.gif" alt="корсар" width="15" height="15"></a><a href="javascript:AddPro('2w', 'NewsText');"><img border="0" src="_imgs/pro/i2w.gif" alt="сталкер" width="15" height="15"></a><a href="javascript:AddPro('3w', 'NewsText');"><img border="0" src="_imgs/pro/i3w.gif" alt="шахтер" width="15" height="15"></a><a href="javascript:AddPro('4w', 'NewsText');"><img border="0" src="_imgs/pro/i4w.gif" alt="инженер" width="15" height="15"></a><a href="javascript:AddPro('5w', 'NewsText');"><img border="0" src="_imgs/pro/i5w.gif" alt="наемник" width="15" height="15"></a><a href="javascript:AddPro('6w', 'NewsText');"><img border="0" src="_imgs/pro/i6w.gif" alt="торговец" width="15" height="15"></a><a href="javascript:AddPro('7w', 'NewsText');"><img border="0" src="_imgs/pro/i7w.gif" alt="патрульный" width="15" height="15"></a><a href="javascript:AddPro('8w', 'NewsText');"><img border="0" src="_imgs/pro/i8w.gif" alt="штурмовик" width="15" height="15"></a><a href="javascript:AddPro('9w', 'NewsText');"><img border="0" src="_imgs/pro/i9w.gif" alt="специалист" width="15" height="15"></a><a href="javascript:AddPro('10w', 'NewsText');"><img border="0" src="_imgs/pro/i10w.gif" alt="журналист" width="15" height="15"></a><a href="javascript:AddPro('11w', 'NewsText');"><img border="0" src="_imgs/pro/i11w.gif" alt="чиновник" width="15" height="15"></a><a href="javascript:AddPro('12w', 'NewsText');"><img border="0" src="_imgs/pro/i12w.gif" alt="псионик" width="15" height="15"></a><a href="javascript:AddPro('13w', 'NewsText');"><img border="0" src="_imgs/pro/i13w.gif" alt="каторжник" width="15" height="15"></a><a href="javascript:AddPro('30w', 'NewsText');"><img border="0" src="_imgs/pro/i30w.gif" alt="дилер" width="15" height="15"></a>
              	<br>
                <input name="prv_subm" type="button" value="Предварительный просмотр" onClick="preview_news();">
                <input name="add_subm" type="submit" value="Добавить">
	</form>
    <br>
    <b>Предварительный просмотр</b>
<div id="preview_area">...</div>
<?}}}?>
<?
} // news found
}
?>