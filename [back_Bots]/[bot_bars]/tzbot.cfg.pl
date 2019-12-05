use strict;

our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

our @bots = (

	{ id => "term06", login => "Terminal 06", pass => "7wgts96h", mod => "pl_fab", owners => ["Снежный барс", "deadbeef"] },

);

our %conf = (

	proto_ver	=> 108,
	flash_ver	=> "0.1 (TZPD)",

	sql_host	=> "localhost",
	sql_login	=> "tzpolice_test",
	sql_pass	=> "StxoE1Yv",
	sql_dbase	=> "tzpolice_test",

	#sql_host	=> "localhost",
	#sql_login	=> "site",
	#sql_pass	=> "",
	#sql_dbase	=> "site",

	owners		=> ["Снежный барс", "deadbeef"],

	pl_fab_rescan	=> 720,
	pl_fab_hangs	=> 360,

	pl_fab_rescan_2 => 30,
	pl_fab_hangs_2  => 15,

);                          

1;
