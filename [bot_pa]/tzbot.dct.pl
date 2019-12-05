use strict;
use Digest::SHA1 qw/sha1_hex/;

my @xdl = ("t=\"2\"/></USER><USER login=\"", "\x01^", "<TURN><USER login=\"", "\x01r", "></USER><USER login=\"", "\x01W", "\"/><MAP v=\"", "\x01f", "\" t=\"2\"/><a sf=\"", "\x01M", "\" t=\"1\" direct=\"", "\x01N", "><a sf=\"0\" t=\"", "\x01`", "\" t=\"5\" xy=\"", "\x01c", ".1\" slot=\"", "\x01j", "\" quality=\"", "\x01l", "\" massa=\"1", "\x01m", "\" maxquality=\"", "\x01n", "><a sf=\"6\" t=\"2\"/><a ", "\x01B", "/><a sf=\"6\" t=\"", "\x01o", "\" damage=\"S", "\x01p", "\" made=\"AR\$\" ", "\x01q", "\" nskill=\"", "\x01s", "\" st=\"G,H\" ", "\x01t", "\" type=\"1\"", "\x01u", "section=\"0\" damage=\"", "\x01~", "\" section=\"", "\x01", "=\"1\" type=\"", "\x01A", "protect=\"S", "\x01C", " ODratio=\"1\" loc_time=\"", "\x01V", "\"/>\n</O>\n<O id=\"", "\x01D", "\"/>\n<O id=\"", "\x01E", "level=", "\x01F", " min=\"", "\x01H", " txt=\"ammo ", "\x01I", " txt=\"BankCell Key (copy) #", "\x01J", "\" txt=\"Coins\" massa=\"1\" ", "\x01K", " cost=\"0\" ", "\x01L", ".1\" name=\"b1-g2\" txt=\"Boulder\" massa=\"5\" st=\"G,H\" made=\"AR\$\" section=\"0\" damage=\"S2-5\" shot=\"7-1\" nskill=\"4\" OD=\"1\" type=\"9.1\"/>", "\x01S", " psy=\"0\" man=\"1\" maxHP=\"", "\x01T", " freeexchange=\"1\" ", "\x01U", "\" virus=\"0\" login=\"", "\x01Y", "\" ne=\",,,,,\" ne2=\",,,,,\" nark=\"0\" gluk=\"0\" ", "\x01Z", "\" max_count=\"", "\x01[", "\" calibre=\"", "\x01\\", "\" count=\"", "\x01]", "\" build_in=\"", "\x01O", "\" shot=\"", "\x01_", "\" range=\"", "\x01a", ".1\" slot=\"A\" name=\"b", "\x01b", ".1\" slot=\"B\" name=\"b", "\x01d", ".1\" slot=\"C\" name=\"b", "\x01h", ".1\" slot=\"D\" name=\"b", "\x01e", ".1\" slot=\"E\" name=\"b", "\x01g", ".1\" slot=\"F\" name=\"b", "\x01{", ".1\" slot=\"GH\" name=\"b", "\x01v", "\" slot=\"", "\x01X", " psy=\"0\" man=\"3\" maxPsy=\"0\" ODratio=\"1\" img=\"rat\" group=\"2\" battleid=\"", "\x01i", ".1\" name=\"b2-s5\" txt=\"Silicon\" massa=\"50\" ", "\x01k", ".1\" name=\"b2-s8\" txt=\"Venom\" massa=\"70\" ", "\x01Q", ".1\" name=\"b2-s4\" txt=\"Organic\" massa=\"30\" ", "\x01w", ".1\" name=\"b2-s2\" txt=\"Precious metals\" massa=\"500\" ", "\x01x", ".1\" name=\"b2-s7\" txt=\"Gems\" massa=\"80\" ", "\x01y", ".1\" name=\"b2-s6\" txt=\"Radioactive materials\" massa=\"800\" ", "\x01z", ".1\" name=\"b2-s3\" txt=\"Polymers\" massa=\"30\" ", "\x01|", "<BATTLE t=\"45\" t2=\"45\" turn=\"1\" cl=\"0\" ", "\x01}", "\" ODratio=\"1\" ", "\x01P", "\" p=\"\"/></L><L X=\"", "\x01R", "zzzzzz", "\x01G", "\"/><a sf=\"", "\x02", ">\n<O id=\"", "\x03", "><O id=\"", "\x04", "      ", "\x05", "00\" ", "\x06", " txt=\"", "\x07", " name=\"b", "\b");
my @xdl_chat = ("\"/>", "\x01", "<A t=\"", "\x02", "<R t=\"", "\x03", "<D t=\"", "\x04", "<S t=\"", "\x05", "<Z t=\"", "\x06");

sub kt
{
	my $t = shift;
	return ($t<20)?1518500249:($t<40)?1859775393:($t<60)?-1894007588:-899497514;
}

sub ft
{
	my ($t, $b, $c, $d) = @_;
	return ($t<20)?(($b&$c)|(($b^4294967295)&$d)):($t<40)?($b^$c^$d):($t<60)?(($b&$c)|($b&$d)|($c&$d)):($b^$c^$d);
}

sub rol
{
	my ($n, $c) = @_;
	return ($n<<$c)|($n>>(32-$c));
}

sub add
{
	my ($x, $y) = @_;

	my $l = ($x&65535)+($y&65535);
	my $m = ($x>>16)+($y>>16)+($l>>16);

	return ($m<<16)|($l&65535);
}

sub s2b
{
	my $s = shift;
	my $n = ((length($s)+8)>>6)+1;
	my (@b, $i);
	for ($i=0; $i<$n*16; $i++) {
		$b[$i] = 0;
	}
	for ($i=0; $i<length $s; $i++) {
		$b[$i>>2] |= ord(substr $s, $i, 1)<<(24-($i%4)*8);
	}
	$b[$i>>2] |= 128<<(24-($i%4)*8);
	$b[$n*16-1] = length($s)*8;
	return \@b;
}

sub n2h
{
	my $n = shift;
	my @h = qw(0 1 2 3 4 5 6 7 8 9 a b c d e f);
	my $s = "";
	for (my $i=7; $i>=0; $i--) {
		$s .= $h[$n>>($i*4)&15];
	}
	return $s;
}

sub sha1_hex_bad
{
	my $x = s2b(shift);
	my ($a,$b,$c,$d,$e) = (1732584193, -271733879, -1732584194, 271733878, -1009589776);
	my @w;
	for (my $i=0; $i<=$#$x; $i+=16) {
		my ($oa, $ob, $oc, $od, $oe) = ($a, $b, $c, $d, $e);
		for (my $j=0; $j<80; $j++) {
			if ($j<16) {
				$w[$j] = $x->[$i+$j];
			} else {
				$w[$j] = rol($w[$j-3]^$w[$j-8]^$w[$j-14]^$w[$j-16], 1);
			}
			my $t = add(add(rol($a, 5), ft($j, $b, $c, $d)), add(add($e, $w[$j]), kt($j)));
			($e, $d, $c, $b, $a) = ($d, $c, rol($b, 30), $a, $t);
		}
		$a = add($a, $oa);
		$b = add($b, $ob);
		$c = add($c, $oc);
		$d = add($d, $od);
		$e = add($e, $oe);
	}
	return n2h($a).n2h($b).n2h($c).n2h($d).n2h($e);
}

sub encrypt
{
	my ($key, $pass) = @_;

	my $str = sha1_hex(substr($pass, 0, 1).substr($key, 0, 10).substr($pass, 1).substr($key, 10));

	my @mix = (35, 6, 4, 25, 7, 8, 36, 16, 20, 37, 12, 31, 39, 38, 21, 5, 33, 15, 9, 13, 29, 23, 32, 22, 2, 27, 1, 10, 30, 24, 0, 19, 26, 14, 18, 34, 17, 28, 11, 3);
	my @out;

	for (my $i=0; $i<40; $i++) {
		$out[$mix[$i]] = substr($str, $i, 1);
	}

	return uc join "", @out;
}

sub decomp
{
	my $s = shift;
	for (my $i=0; $i<=$#xdl; $i+=2) {
		$s =~ s/\Q$xdl[$i+1]\E/$xdl[$i]/sg;
	}
	return $s;
}

sub chat_decomp
{
	my $s = shift;
	for (my $i=0; $i<=$#xdl_chat; $i+=2) {
		$s =~ s/\Q$xdl_chat[$i+1]\E/$xdl_chat[$i]/sg;
	}
	return $s;
}

sub get_new_id
{
	my ($bot) = @_;

	my $id = ($bot->{params}{id1}||0)+($bot->{params}{i1}||0);

	if (++$bot->{params}{i1} >= 100) {
		$bot->{params}{id1} = $bot->{params}{id2};
		$bot->{params}{i1} = 0;
	}

	client_out($bot, "<NEWID/>");

	return $id;
}

sub calc_h5_2
{
	my ($list) = @_;
	my $out = "";

	foreach my $id (sort keys %{$list}) {
		my $item = $list->{$id};
		$out .= $id.(defined $item->{data}{count}?$item->{data}{count}:"");
	}

	return $out;
}

1;
