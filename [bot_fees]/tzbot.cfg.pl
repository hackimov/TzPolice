use strict;

our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

our @bots = (

	{ id => "term02", login => "Terminal 02", pass => "yef4cdbg", mod => "pl_fab", owners => ["Stealth", "deadbeef", "FANTASTISH"] },

);

our %conf = (

	proto_ver	=> 108,
	flash_ver	=> "0.1 (TZPD)",

	sql_host	=> "192.168.253.6",
	sql_login	=> "tzpolice_test",
	sql_pass	=> "StxoE1Yv",
	sql_dbase	=> "tzpolice_test",

	#sql_host	=> "localhost",
	#sql_login	=> "site",
	#sql_pass	=> "",
	#sql_dbase	=> "site",

	owners		=> ["Stealth", "deadbeef", "FANTASTISH"],

	pl_fab_rescan	=> 900,
	pl_fab_hangs	=> 360,

	pl_fab_rescan_2 => 30,
	pl_fab_hangs_2  => 15,
# private key
	private_key => "NF5NYXDPHPRZAZ8864EX",
);                          

1;
