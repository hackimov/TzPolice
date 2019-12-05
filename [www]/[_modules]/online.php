<br>
<p class="menu1th"><img src="i/bullet-menu01.gif" width="15" height="11" hspace="0" vspace="0" align="absmiddle"><b> Сейчас в он-лайн:</b></p>

<div>
<?php
	$OnlineTime = time()-300;
	$SQL = 'SELECT * FROM `site_users` WHERE `last_visit`>\''.$OnlineTime.'\'';
	$r = mysql_query($SQL);
	$i = 0;
	while($d = mysql_fetch_array($r)) {
		if ($d['user_name'] != 'FANTASTISH') {
			$i++;
			//GetClan($d['clan'])
			if(AuthStatus==1 && (abs(AccessLevel) & AccessUsers)) {
				echo GetUser($d['id'], $d['user_name'], '100');
			} else {
				echo GetUser($d['id'], $d['user_name'], '1');
			}
			if($i!=mysql_num_rows($r)) echo ', ';
		}
	}
?>
</div>