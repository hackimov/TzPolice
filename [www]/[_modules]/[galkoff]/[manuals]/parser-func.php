<?php
	function ParseNews($buf, $AllowTags, $replaceBR=1) {
		$outttvar = '';
		if($AllowTags==0) $buf = strip_tags($buf, '<b><i><u><div><wbr><strike>');
		$buf=stripslashes($buf);
		if($replaceBR==1) $buf = str_replace("\n", "<br>\n", $buf);
		$FontColors1 = array('[red]', '[/red]', '[green]', '[/green]', '[blue]', '[/blue]');
		$FontColors2 = array('<font color=red>', '</font>', '<font color=green>', '</font>', '<font color=blue>', '</font>');
		$buf=str_replace($FontColors1,$FontColors2,$buf);
		$buf=str_replace('{MP_LIST}',mp_list(),$buf);
		$text = $buf;
		$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
		$text = preg_replace("/\[pers clan=([0-9A-Za-z_ ]+) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
		$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
		$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
		$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
		$buf = $text;
		$buf=preg_replace("/\[prof\](.*?)\[\/prof\]/si","<img border=0 style='vertical-align:text-bottom' src='/_imgs/pro/\\1.gif'>",$buf);
		$buf=preg_replace("/\[clan\](.*?)\[\/clan\]/si","<img border=0 src='/_imgs/clans/\\1.gif'>",$buf);
		$buf=eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]","<img border=0 src='/user_data/\\1'>",$buf);
		$buf=eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$buf);
		$buf = preg_replace("/\[URL\](.*?);(.*?)\[\/url\]/si", "<a href='\\1' target=_blank>\\2</a>",$buf);
		$buf=explode(' ',$buf);
		for($i=0;$i<count($buf);$i++) {
			if(eregi(':([a-z0-9_]+):',$buf[$i],$ok)) {
				if(is_file('/home/sites/police/www/_imgs/smiles/'.$ok[1].'.gif'))
					$buf[$i]=str_replace(':'.$ok[1].':', '<img border=0 src="/_imgs/smiles/'.$ok[1].'.gif"> ', $buf[$i]);
				$outttvar .= $buf[$i].' ';
			} else
				$outttvar .= $buf[$i].' ';
		}
		$outttvar = mywordwrap($outttvar);
		echo ($outttvar);
	}

	function ParseNews2a($text) {
		$text = stripslashes($text);
		$text = strip_tags($text, '<a><div><table><tr><td><strike><script><strong><em><img><br>');
//GALKOFF 30.09.2009
	if (preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t))
		{
			for($i = 0; $i < count($t[0]); $i++)
				{
					$tmp = '';
					preg_match_all('#\[dialog="(.*)"\]#si', $t[0][$i], $t1);    
					$tmp .= $t[1][$i];
					$tmp .= '<div class="quote"><img src="http://www.timezero.ru/i/avatar/'.$t1[1][0].'" class="dialog">';
//!!!!!!!!!!!!!!!!! В этой строке делаем шаблон жадным, ставя вот этот вопросик     \/        таким образом ищется только первая закрывающая квадратная скобка, а не последняя. иначе если в строке была еще одна квадратная скобка - типа номер локации - подсвечивалось все до этой скобки.
					$tmp .= preg_replace('#\[(.*)\] private \[(.*?)\]#', '<b>[${1}] <font color="red">private [${2}]</font></b>', $t[2][$i]);
					$tmp .= '</div>';
					$pos1=strpos($text,$t[0][$i]);
					$pos2=strlen($t[0][$i]);
					$text=substr($text,0,$pos1).$tmp.substr($text,$pos1+$pos2);
				}
		}
//GALKOFF END
		$text = preg_replace("/\[url=([\w]+?:\/\/.*?)\](.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\2</a>", $text);
		$text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\2</a>", $text);
		$text = preg_replace("/\[url\]([\w]+?:\/\/.*?)\[\/url\]/si", "<a href='\\1' target='_blank'>\\1</a>", $text);
		$text = preg_replace("/\[url\](.*?)\[\/url\]/si", "<a href='http://\\1' target='_blank'>\\1</a>", $text);
		$text = preg_replace('/\[color="(\#[0-9A-F]{6}|[a-z]+)"\]/si', "<font color='\\1'>", $text);
		$text = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<font color='\\1'>", $text);
		$text = str_replace('[/color]', '</font>', $text);
		$text = preg_replace("/\[pers nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<b>\\1</b> [\\2]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\3.gif' border='0'>", $text);
		$text = preg_replace("/\[pers clan=(.*?) nick=(.*?) level=([0-9]+) pro=([0-9w]+)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'><b>\\2</b> [\\3]<img style='vertical-align:text-bottom' src='_imgs/pro/i\\4.gif' border='0'>", $text);
		$text = preg_replace("/\[clan=(.*?)\]/si", "<img src='_imgs/clans/\\1.gif' alt='\\1' border='0'>", $text);
		$text = preg_replace("/\[pro=([0-9w]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
		$text = preg_replace("/\[pro=([0-9w]+) nick=([0-9A-Za-z *\-]+)\]/si", "<img style='vertical-align:text-bottom' src='_imgs/pro/i\\1.gif' border='0'>", $text);
		$text = str_replace('[b]', '<b>', $text);
		$text = str_replace('[/b]', '</b>', $text);
		$text = str_replace('[u]', '<u>', $text);
		$text = str_replace('[/u]', '</u>', $text);
		$text = str_replace('[i]', '<i>', $text);
		$text = str_replace('[/i]', '</i>', $text);
		$text = str_replace('[quote]', '<div class="quote">', $text);
		$text = str_replace('[/quote]', '</div>', $text);
		$text = str_replace('[left]', '<div align="left">', $text);
		$text = str_replace('[/left]', '</div>', $text);
		$text = str_replace('[center]', '<div align="center">', $text);
		$text = str_replace('[/center]', '</div>', $text);
		$text = str_replace('[small]', '<font size="-2">', $text);
		$text = str_replace('[/small]', '</font>', $text);
		$text = str_replace('[right]', '<div align="right">', $text);
		$text = str_replace('[/right]', '</div>', $text);
		$text = str_replace('[image]', '<img border="0" src="', $text);
		$text = str_replace('[imageleft]', '<img border="0" align="left" hspace="0" vspace="15" src="', $text);
		$text = str_replace('[/image]', '">', $text);
		$text = str_replace('[list]', '<ul>', $text);
		$text = str_replace('[list=x]', '<ol>', $text);
		$text = str_replace('[*]', '<li>', $text);
		$text = str_replace('[/list]', '</ul>', $text);
		$text = str_replace('[/list=x]', '</ol>', $text);
		$text = eregi_replace("\\[img\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img border=0 src='/user_data/\\1'>", $text);
		$text = eregi_replace("\\[imgleft\\]([a-z0-9\\./\\-]+)\\[/img\\]", "<img align='left' border='0' hspace='15' vspace='0' src='/user_data/\\1'>", $text);
		$text = eregi_replace("\\[imgprev\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img border=0 src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
		$text = eregi_replace("\\[imgprevleft\\]([A-Za-z0-9/\\-]+);([jpggifpng0-9A-Za-z_\\.]+);([0-9]+);([0-9]+)\\[/imgprev\\]","<a href=\"JavaScript:Enlarge('\\1','\\2',\\3,\\4)\"><img align='left' border='0' hspace='15' vspace='0' src='/user_data/\\1/thumb/\\2' ALT='Нажмите, чтобы увеличить'></a>",$text);
		$text = str_replace("\n", ' <br> ', $text);
		$tmp = explode(' ',$text);
		for($i=0;$i<count($tmp);$i++) {
			if(eregi(':([a-z0-9_]+):', $tmp[$i], $ok)) {
				if(is_file('_imgs/smiles/'.$ok[1].'.gif'))
					$text=str_replace(':'.$ok[1].':', '<img src="_imgs/smiles/'.$ok[1].'.gif"> ', $text);
			}
		}
		
		preg_match_all('#(.*)\[dialog=".*"\](.*)\[/dialog\]#Usi', $text, $t);
		echo ($text);
	}

?>