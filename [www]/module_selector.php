<?
$modulesDir = "_modules";
#вписывать только если есть разница между гет запросом и именем модуля!
#и вообще переделать нафиг. Навц модули по папкам раскидывать?
$otherModules = Array(
'post'=>'_modules/inviz/posts/post.php',
'post33'=>'_modules/inviz/posts/post33.php',
'post2'=>'_modules/inviz/posts/post33.php',
'post_vault'=>'_modules/inviz/posts/post_vault.php',
'post_forum'=>'_modules/inviz/posts/post_forum.php',
'post_nma'=>'_modules/inviz/posts/post_nma.php',
'post_forum2'=>'_modules/inviz/posts/post_forum2.php',
'post_prison'=>'_modules/inviz/posts/post_prison.php',
'xfiles'=>'_modules/inviz/ld.php',
'om_stats'=>'_modules/inviz/om_stats.php',
'police_join'=>'_modules/inviz/police_join.php',

'mp_join'=>'_modules/inviz/mp_join.php',
'mpblack'=>'_modules/mp/mpblack.php',
'mp_black'=>'_modules/mpblack.php',
'mp_headq'=>'_modules/inviz/mp_headquarters.php',

'articles_add'=>'_modules/data_manager.php',
'encycl'=>'_modules/encyclopedia.php',
'fotos_add'=>'_modules/fotos_send2.php',
'fotos'=>'_modules/fotos2.php',
'major'=>'_modules/city.php',
'mp_special'=>'mp/mp_sl.php',
'pda'=>'_modules/pdascan.php',
'kors'=>'_modules/corsairs.php',
'warez_pe'=>'_modules/warez_police.php',
'sl_search'=>'_modules/silent_log_search.php',
'prisoned'=>'_modules/prison_stats_pers.php',
'comm_ed'=>'_modules/comm_editor.php',
'rsd'=>'_modules/resdinam.php',
'prison_alarm'=>'_modules/prison_alarms.php',

'tzrating'=>'_modules/rating_arh/tz_rating_public.php',
'cops_stats'=>'_modules/rating_arh/cops_stats_public.php',
'pa_stats'=>'_modules/rating_arh/pa_stats_public.php',
'fees'=>'_modules/rating_arh/fees_public.php',
'4amaz'=>'_modules/rating_arh/4amaz_public.php',
'mp_stats'=>'_modules/rating_arh/mp_stats_public.php',
'silents'=>'_modules/rating_arh/silents_log.php',
'oko_search'=>'_modules/rating_arh/oko_search.php',
'personal_silents'=>'_modules/rating_arh/personal_silents_log.php',
'battles_archive'=>'_modules/rating_arh/battles_public.php',
'mp_inform'=>'_modules/rating_arh/mp_inform.php',
'solemnizers'=>'_modules/rating_arh/solemnizers.php',

'siterating'=>'_modules/siteRating/index.php',
'siterating_admin'=>'_modules/siteRating/admin.php',

'meetings'=>'_modules/yarkij/vstrechi.php',
'meeting_add'=>'_modules/yarkij/vstrechi_add.php',
'meeting_moder'=>'_modules/yarkij/vstrechi_moder.php',
'vzlom'=>'_modules/yarkij/vzlom.php',

'data2'=>'_modules/galkoff/manuals/data.php',
'data_manager2'=>'_modules/galkoff/manuals/data_manager.php',
'ipscanner'=>'_modules/galkoff/ipscaner/scaner.php',

'news_search' => Array('file'=>'_modules/news.php','action'=>'search'),
'longlogs'=>'otherhistory/parse_history.php',

'history'=>'_modules/history/history.php',
'hist'=>'_modules/history/temp.php',

'color'=>'_modules/colorifier/index.php'

);

#если запрошен модуль, есть такой файл в папке модулей или есть линк на файл в массиве
if($module && (file_exists($modulesDir."/".$module.".php") || isset($otherModules[$module]))) {
	#есть линк на файл в массиве
	if($otherModules[$module]) {
		#дополнительные переменные в модуль
		if(is_array($otherModules[$module])) {
			foreach($otherModules[$module] as $k => $v) {
				if($k == 'file') continue;
				$$k = $v;
			}
			include($otherModules[$module]['file']);
		} else {
			include($otherModules[$module]);
		}
	} else {
		include($modulesDir."/".$module.".php");
	}
} else {
	include($modulesDir."/news.php");
}

?>