use strict;

our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

our @bots = (

 	{ id => "smart_logger", login => "умный терминал", pass => "k55z57", x => 135, y => 40, z => 10, r => 0, mod => "pl_monitor", owners => ["Текс", "help 7"], nochat => 0, server => 1 },
	{ id => "pdaprima", login => "Terminal 06", pass => "n713u8wg", x => 0, y => -1, z => 7, r => 0, mod => "pda", server => 1 },
	{ id => "pdarapa", login => "terminal 22", pass => "n713u8wg", x => 18, y => -45, z => 7, r => 0, mod => "pda", server => 2 },

);

our %conf = (

	proto_ver	=> 108,
	flash_ver	=> "0.1 (TZPD)",

	#sql_host	=> "localhost",
	sql_host	=> "127.0.0.6",
	sql_login	=> "tzpolice_test",
	sql_pass	=> "StxoE1Yv",
	sql_dbase	=> "tzpolice_test",

	sql_dbase_hist	=> "tzpolice",
	sql_login_hist	=> "tzpolice",
	sql_pass_hist	=> "CkO3Z4mDM9",


	owners		=> ["deadbeef", "Dissident"],

	history_allowed => ["Dissident","deadbeef"],

	pl_fab_rescan	=> 900,
	pl_fab_hangs	=> 300,

	pl_fab_rescan_2 => 300,
	pl_fab_hangs_2  => 60,

	pl_cmpl_rescan => 720,
# скан списка клана
	pl_online_rescan => 300,
	pl_online_hangs  => 60,
# отправка телеги
	pl_online_send => 30,
	pl_online_sendhangs  => 15,
# скан своей истории каждые 4 часа
	pl_online_historyscan => 14400,
# скан чужой истории каждые 30 секунд
	pl_online_otherhistoryscan => 5,
	
 	server => 1,
#Terminal POLICE
#Ваша любимая книга?
#Прощай, оружие.


#SMArt: Ромео, help 7

#Forpost: help 7, Кочка, Кеара
);

1;
