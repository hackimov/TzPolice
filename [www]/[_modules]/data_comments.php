<h1>����������� � �������</h1>

<?php

if(@$_REQUEST['added']==1) echo "<div class=green>����������� ��������</div><br>";


if(@$_REQUEST['DataId']) {

        if(@$_REQUEST['do']=="close" && (abs(AccessLevel) & AccessArticles)) {
			$SQL="UPDATE site_data SET allow_comments='0' WHERE id='".$_REQUEST['DataId']."'";
			mysql_query($SQL);
			echo "<script>top.location.href='?act=data_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
        } elseif(@$_REQUEST['do']=="open" && (abs(AccessLevel) & AccessArticles)) {
            $SQL="UPDATE site_data SET allow_comments='1' WHERE id='".$_REQUEST['DataId']."'";
            mysql_query($SQL);
            echo "<script>top.location.href='?act=data_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";
        }


        if(@$_REQUEST['do']=="delete" && @$_REQUEST['IdComment'] && (abs(AccessLevel) & AccessArticles)) {

        $DelText="[red]������� �����������[/red] [clan]police[/clan]<b>".GetUser(AuthUserId,AuthUserName,AuthUserGroup)."</b>";
		$SQL="UPDATE data_comments SET comment_text='".addslashes($DelText)."' WHERE id='".$_REQUEST['IdComment']."'";
        mysql_query($SQL);
        echo "<script>top.location.href='?act=data_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";

    }

        if(@$_REQUEST['do']=="add" && @$_REQUEST['NewsText']) {

                $SQL="SELECT allow_comments FROM site_data WHERE id='".$_REQUEST['DataId']."'";
                $d=mysql_fetch_array(mysql_query($SQL));
		        $AllowComments=$d['allow_comments'];


                $SQL="SELECT banned, post_time FROM site_users WHERE id='".AuthUserId."'";
                $d=mysql_fetch_array(mysql_query($SQL));
//	        echo $d['post_time']."+30 < ".time();
                if($d['banned']>time()) {
                        $MolDiff=$d['banned']-time();
                    echo "<h2>�� �� ������ ��������� ����������� ��� ".gmdate("z��. H� i� s�",$MolDiff)."</h2><br><br>";
                } elseif($d['post_time']+30>time()) {
                $oldcomm=stripslashes($_REQUEST['NewsText']);
                    echo "<h2>�� �� ������ ��������� ����������� ����, ��� ��� � 30 ������</h2><br><br>";
                } elseif ($AllowComments==0) {
                echo "<h2>���������� �������</h2><br><br>";
                } else {

                        $SQL="INSERT INTO data_comments (id_data,id_user,comment_date,comment_text) values('".$_REQUEST['DataId']."','".AuthUserId."','".time()."','".addslashes($_REQUEST['NewsText'])."')";
                        mysql_query($SQL);
            $SQL="UPDATE site_users SET post_time='".time()."' WHERE id='".AuthUserId."'";
            mysql_query($SQL);
                    echo "<script>top.location.href='?act=data_comments&DataId=".$_REQUEST['DataId']."&p=".$_REQUEST['p']."'</script>";

            }
        }

$SQL="SELECT * FROM site_data WHERE id='".$_REQUEST['DataId']."'";
$r=mysql_query($SQL);
if(mysql_num_rows($r)<1) echo $mess['NewsNotFound'];
else {

        $d=mysql_fetch_array($r);
        $AllowComments=$d['allow_comments'];


    echo "
		<table width='100%' border='0' cellspacing='3' cellpadding='5'><tr>
		<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���������� ������: </strong> <span class='top-header'>{$d['title']}</span></p></td>
		</tr><tr><td>
		</tr></table>
	";

    $SQL="SELECT c.*,u.id AS user_id, u.user_name AS user_name, u.clan FROM data_comments c LEFT JOIN site_users u ON c.id_user=u.id WHERE  c.id_data='".$_REQUEST['DataId']."'";
    $r=mysql_query($SQL);
    if(mysql_num_rows($r)<1) echo $mess['NoComments'];
    else {


		$pages=ceil(mysql_num_rows($r)/$CommentsPerPage);
        if($_REQUEST['p']>0) $p=$_REQUEST['p'];
        else $p=1;

        $LimitParam=$p*$CommentsPerPage-$CommentsPerPage;

        $SQL.=" LIMIT $LimitParam, $CommentsPerPage";
        $r=mysql_query($SQL);


        echo "<br><div align=right>��������: <b>";
        ShowPages($p,$pages,4,"act=data_comments&DataId={$_REQUEST['DataId']}");
        echo "</b></div>";


        for($i=0;$i<mysql_num_rows($r);$i++) {

            $d=mysql_fetch_array($r);
		    echo "
				<br><table width='100%' border='0' cellspacing='3' cellpadding='0'><tr>
				<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01.gif' width='18' height='11' hspace='5'>
            ";
            if(abs(AccessLevel) & AccessArticles)  echo "<a href='#;return false' onclick=\"if(confirm('������� �����������?')){top.location='?act=data_comments&DataId={$d[id_data]}&IdComment={$d[id]}&do=delete&p={$p}'}\">�������</a> / ";
            echo " ".date("d.m.Y - H:i",$d['comment_date'])." / ".GetClan($d['clan']).GetUser($d['user_id'],$d['user_name'],AuthUserGroup).":
                </p></td>
				</tr><tr><td>
		    ";
            ParseNews($d['comment_text'],0);
            echo "</td></tr></table>";

        }



    }
?>
<br>

<?
echo "<div align=right>��������: <b>";
ShowPages($p,$pages,4,"act=data_comments&DataId={$_REQUEST['DataId']}");
echo "</b></div>";
if((abs(AccessLevel) & AccessArticles) && $AllowComments!=0) echo "<div>[ <a href='?act=data_comments&do=close&DataId=".$_REQUEST['DataId']."&p=$p'>������� ����������</a> ]</div>";
if((abs(AccessLevel) & AccessArticles) && $AllowComments==0) echo "<div>[ <a href='?act=data_comments&do=open&DataId=".$_REQUEST['DataId']."&p=$p'>���������� ����������</a> ]</div>";


?>




<?
if(AuthStatus!=1) echo $mess['CantAddComment'].$mess['WantRegister'];
else {
        $SQL="SELECT banned FROM site_users WHERE id='".AuthUserId."'";
        $d=mysql_fetch_array(mysql_query($SQL));
        if($d['banned']>time()) {
                $MolDiff=$d['banned']-time();
            echo "<h2>�� �� ������ ��������� ����������� ��� ".gmdate("z��. H� i� s�",$MolDiff)."</h2><br><br>";
        } else {
            if($AllowComments==0) echo "<h2>���������� �������</h2>";
        else {

?>

<table width='100%' border='0' cellspacing='3' cellpadding='5'>
<td height=20 background='i/bgr-grid-sand1.gif'>
<strong>�������� �����������:</strong>
</td></tr></table>

<table width=100% cellpadding=5>
<tr>
<td valign=top>

        <form method="POST" name="News">
        <input name="act" type="hidden" value="data_comments">
        <input name="do" type="hidden" value="add">
           <input name="DataId" type="hidden" value="<?=$_REQUEST['DataId']?>">
    <input name="p" type="hidden" value="<?=$p?>">
    ����� �������: <Br>
        <textarea ONSELECT="storeCaret(this)" ONCLICK="storeCaret(this)" ONKEYUP="storeCaret(this)" name="NewsText" style="width:100%" rows=10><?=@htmlspecialchars(stripslashes($d['news_text']))?><?=$oldcomm?></textarea><br>
        <img src="/_imgs/b.gif" border=0 onclick="decor('b')" style="cursor:hand" ALT="�������� ������� ������ � �������, ����� ������� ��� ����������">
        <img src="/_imgs/i.gif" border=0 onclick="decor('i')" style="cursor:hand" ALT="�������� ������� ������ � �������, ����� �������� ��� ��������">
        <img src="/_imgs/u.gif" border=0 onclick="decor('u')" style="cursor:hand" ALT="�������� ������� ������ � �������, ����� ������� ��� ������������">
        <img src="/_imgs/center.gif" border=0 onclick="decor('div align=center')" style="cursor:hand" ALT="�������� ������� ������ � �������, ����� ��������� ��� �� ������">
        <br><br>
        <input type="submit" value="�����������������">
        </form>

</td><td width=200 valign=top>

        ��������: <br>
    <select size="1" onchange="document.frames['icons'].location='_modules/icons/'+this.options[this.selectedIndex].value+'.php'" style="width:100%">
                <option value="blank" selected>-- �������� ��������� --</option>
        <option value="smiles"> ��������</option>
        <option value="clans"> �����</option>
        <option value="prof"> ���������</option>        
        <option value="login"> ���</option>
        <option value="url"> URL</option>
        </select>

    <iframe BORDER=0 FRAMEBORDER=0 id="icons" src="_modules/icons/blank.php" width=100% HEIGHT=105 style="border:1 solid #000000">


</td></tr>
<table>
<?}}}?>



<?
} // news found
}



?>