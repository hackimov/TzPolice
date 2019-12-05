use strict;
no strict 'refs';
our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

our @bots = (

	{ id => "prison", login => "Big Brother", pass => "L1ZacWU4", x => 168, y => -171, z => 0, r => 0, mod => "pl_fab", owners => ["Stealth", "deadbeef", "ZenitSPb", "FANTASTISH"] },

);

our %conf = (

	proto_ver	=> 107,
	flash_ver	=> "0.1 (TZPD)",

	sql_host	=> "localhost",
	sql_login	=> "tzpolice_test",
	sql_pass	=> "StxoE1Yv",
	sql_dbase	=> "tzpolice_test",

	#sql_host	=> "localhost",
	#sql_login	=> "site",
	#sql_pass	=> "",
	#sql_dbase	=> "site",

	owners		=> ["deadbeef", "FANTASTISH"],

	pl_fab_rescan	=> 6000000000,
	pl_fab_hangs	=> 300,

	pl_fab_rescan_2 => 3000000000,
	pl_fab_hangs_2  => 60,
	
	server => 2,

);

1;
