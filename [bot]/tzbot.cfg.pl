use strict;

our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

open F, "/home/sites/police/botowners.txt";
chomp(my @botowners = <F>);
close F;

our @bots = (

	{ id => "fabzon", login => "HONY", pass => "fhcnl2tl1", x => 168, y => -171, z => 10, r => 0, mod => "pl_fab", owners => ["deadbeef", "FANTASTISH", "Big Brother"], nochat => 0, server => 2 },
	{ id => "labzon", login => "maHONEY", pass => "qlpi1shs2", x => 168, y => -171, z => 10, r => 0, mod => "pl_fab", owners => ["Текс", "Ксакеп", "Big Brother"], nochat => 0, server => 2 },
	{ id => "police", login => "Terminal POLICE", pass => "h196y0qr", x => -33, y => -55, z => 6, r => 0, mod => "pl_online", owners => \@botowners, nochat => 1, server => 1 },

	{ login => "terminal 07", pass => "6k4wpwvt", id => "atlanta", x => -174, y => -100, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 08", pass => "xkys3ien", id => "syn", x => 174, y => 175, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 09", pass => "e4w9s293", id => "neva", x => -33, y => -56, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 10", pass => "oy7fm4ui", id => "oj", x => -25, y => 151, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 11", pass => "kk8453fk", id => "kitezh", x => 61, y => -160, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 12", pass => "byteb7is", id => "kabul", x => 141, y => 33, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 13", pass => "m3w6zjv7", id => "oasis", x => 56, y => 34, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 14", pass => "g2j6z9ev", id => "isla", x => -110, y => 130, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 15", pass => "m7xpxv5s", id => "moskow", x => 0, y => 0, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 16", pass => "bfxge10b", id => "shelter", x => 76, y => -65, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },

	{ login => "terminal 17", pass => "gteshpmm", id => "babylon", x => 100, y => 140, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 18", pass => "w9adxuqm", id => "outland", x => -42, y => -96, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 19", pass => "c8a7gx4o", id => "southwest", x => -120, y => 66, z => 0, r => 0, mod => "locator", server => 1, nochat => 1 },
	{ login => "terminal 20", pass => "ngo92ias9f.", id => "prison", x => 168, y => -171, z => 0, r => 0, mod => "locator", server => 2, nochat => 1 },
	
	{ login => "Рыжие в форпосте", pass => "Zj8b3Gcd:dID", id => "forpost2", x => 135, y => 40, z => 0, r => 0, mod => "locator", server => 2, nochat => 1 },
	{ login => "terminal 21", pass => "t0k8yo5q", id => "rapanui", x => 19, y => -46, z => 0, r => 0, mod => "locator", server => 2, nochat => 1 },
	{ login => "terminal 23", pass => "5wo3abq2", id => "coralbay", x => 43, y => -24, z => 0, r => 0, mod => "locator", server => 2, nochat => 1 },
	{ login => "terminal 24", pass => "8ydz6swu", id => "zona", x => 76, y => -65, z => 0, r => 0, mod => "locator", server => 2, nochat => 1 },
	{ login => "Военный Эксперт", pass => "glm29bcu1", id => "wars", x => 1, y => -1, z => 10, r => 0, mod => "wars", owners => \@botowners, nochat => 1, server => 1 }

);

our %conf = (

	proto_ver	=> 108,
	flash_ver	=> "0.1 (TZPD)",

	sql_host	=> "192.168.253.6",
	sql_login	=> "tzpolice_test",
	sql_pass	=> "oXNrFiUGWCoHEp9Z",
	sql_dbase	=> "tzpolice_test",

	sql_dbase_hist	=> "tzpolice",
	sql_login_hist	=> "tzpolice",
	sql_pass_hist	=> "uDKs8NthHdsMtBUW",

	owners		=> ["deadbeef", "FANTASTISH", "Big Brother"],

	history_allowed => ["AVE","FANTASTISH","deadbeef", "Big Brother"],

	pl_fab_rescan	=> 600,
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
	pl_online_otherhistoryscan => 30,
# скан наличия каторжников без статьи и готовых к освобождению/ раз в минуту
	pl_online_prisonescan => 600,
# скан незанятых постов
	pl_online_postsscan => 1800,
# скан текущих оффваров
	pl_wars_scan => 841,
 	server => 1,
# private key
	private_key => "NF5NYXDPHPRZAZ8864EX",
#Terminal POLICE
#Ваша любимая книга?
#Прощай, оружие.

);

1;
