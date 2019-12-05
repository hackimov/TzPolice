use strict;

our @res_names = ("", "met", "gol", "pol", "org", "sil", "rad", "gem", "ven");
our %res_id    = (met => 1, gol => 2, pol => 3, org => 4, sil => 5, rad => 6, gem => 7, ven => 8);

our @bots = (

	{ id => "coprock", login => "Полицейский Рок",  pass => "echelonminigun", private => "G7ZZAPYZD3KECUA4PT4K", x => 0, y => -1, z => 7, r => 0, mod => "hlp", server => 2 },
	{ id => "copwind", login => "Полицейский Винд", pass => "echelonhood",    private => "SD344UZBZJS5776HS7FF", x => 0, y => 0, z => 0, r => 0,  mod => "hlp", server => 2 },

);

our %conf = (

	proto_ver	=> 108,
	flash_ver	=> "0.1 (TZPD)",

	owners		=> ["Ксакеп"],

	pl_help	=> 1800

);

1;
