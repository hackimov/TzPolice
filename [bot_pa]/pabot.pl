#!/usr/bin/perl -w
#use CGI::Carp "fatalsToBrowser"; # DimoN
use strict;
use sigtrap qw(stack-trace old-interface-signals ALRM);

our @at_exit;

END { &{$_}() foreach reverse @at_exit; }

use IO::Socket;
use IO::Select;
use Encode qw(from_to);
use POSIX;
use List::Util qw(min max);

setlocale LC_CTYPE, "ru_RU.CP1251";
use locale;

our $home = ".";
our $logs = ".";

our $debug = 0;
our $shutup = 0;
our $meeting_log = "no";

#=begin

$home = "/home/sites/police/bot_pa";
$logs = "$home/logs";

$debug = 0;

open STDERR, ">>$home/tzbot-err.log";
#open STDOUT, ">&STDERR";
#open(STDERR,">/dev/null") or open(STDERR,">nul") or die "Can't find null device!";
open STDOUT, ">>$home/tzbot.log";

select STDERR; $| = 1;
select STDOUT; $| = 1;

#=cut

exit if -e "$home/tzbot.lock";

#unless ($home eq ".") {
#	exit if fork();
#
#	#$SIG{HUP} = $SIG{TERM} = $SIG{INT} = sub {};
#	setsid();
#}

unless ($home eq ".") {
	exit if fork();
	
	if (open my $file, "$home/pabot.pid") {
		my $pid = <$file>;
		chomp $pid;
		close $file;
		exit if -d "/proc/$pid";
	}
	
	if (open my $file, ">$home/pabot.pid") {
		print $file $$;
		close $file;
	}
	
	#$SIG{HUP} = $SIG{TERM} = $SIG{INT} = sub {};
	setsid();
}


require "$home/tzbot.cfg.pl";
require "$home/tzbot.lib.pl";
require "$home/tzbot.dct.pl";

#require "$home/mod.shop.pl";
#require "$home/mod.hospital.pl";
#require "$home/mod.portal.pl";
#require "$home/mod.tztime.pl";
#require "$home/mod.rshop.pl";
#require "$home/mod.purchase.pl";
require "$home/mod.pa.pl";

our (%conf, @bots, %mods);

msg("License bot started");

our $blocking = IO::Socket::INET->new(Blocking => 1)?1:0;
msg("Non blocking socket failed") unless $blocking;

my @root_cmds = ("help", "status", "sleep [n]", "kill", "all_sleep [n]", "crash", "restart");
my @boss_cmds = ("help", "status", "sleep [n]");

our (@repl_keys, %repl_phrases);

sub load_replies
{
	my $fname = shift;

	open my ($file), $fname;

	my $key = "";
	foreach (<$file>) {
		s/\s*\r?\n$//;

		if (/^\t@(.*)/) {
			my ($cmd, $arg) = split /\s+/, $1, 2;
			push @{$repl_phrases{$key}{c}}, { c => $cmd, a => $arg };

		} elsif (/^\s+%(.*)/) {
			push @{$repl_phrases{$key}{0}}, $1;

		} elsif (/^\s+#(.*)/) {
			push @{$repl_phrases{$key}{1}}, $1;

		} elsif (/^\s+(.*)/) {
			push @{$repl_phrases{$key}{0}}, $1;
			push @{$repl_phrases{$key}{1}}, $1;

		} elsif (/^#.+/) {
			$key = $_;
			$repl_phrases{$key} = {};

		} elsif (/.+/) {
			$key = $_;
			$repl_phrases{$key} = {};
			push @repl_keys, $key;
		}
	}

	close $file;
}
load_replies("$home/replies.txt");
our %clients = ();
sub load_clients
	{
		touch("$home/clients.txt");
		my $q = 0666;
		my $w = "$home/clients.txt";
		chmod($q, $w);
		open (BASE, "<$home/clients.txt");
		my @base = <BASE>;
		close (BASE);
		$#::clients = -1;
		for (my $i=$#base; $i>=0; $i--)
			{
				(my $nick, my $msg)=split (/ \|\| /, $base [$i]);
				$::clients{$nick} = $msg;
			}
	}

my @loc_cache;
my $loc_saved = time;

sub save_loc
{
	@loc_cache = ();
	$loc_saved = time;
	return;

	if (@loc_cache) {
		my $fname = "$logs/loc-".strftime("%Y%m%d%H%M%S", localtime);

		if (open my ($file), ">$fname.tmp") {
			print $file join "\n", @loc_cache;
			close $file;

			rename "$fname.tmp", "$fname.xml";

			@loc_cache = ();
			$loc_saved = time;
		}
	}
}

push @at_exit, \&save_loc;

sub clone_item
{
	my $xml = xml_parse_s(xml_to_str(shift));
	return $xml->[0];
}

sub pack_inventory
{
	my $bot = shift;

	my (%res_hash);

	foreach my $id (keys %{$bot->{inventory}}) {
		my $item = $bot->{inventory}{$id};

		if ($item->{data}{name} =~ /^b2-s([1-8])$/ || $item->{data}{name} eq "b1-y1") {
			my $id_2 = $res_hash{$item->{data}{name}};
			if ($id_2) {
				client_out($bot, qq~<JOIN id1="$id" id2="$id_2"/>~);

				$bot->{inventory}{$id_2}{data}{count} += $item->{data}{count};
				delete $bot->{inventory}{$id};
			} else {
				$res_hash{$item->{data}{name}} = $id;
			}
		}
	}
}

sub get_money_id
{
	my $bot = shift;

	my $money_id;

	foreach my $id (keys %{$bot->{inventory}}) {
		my $item = $bot->{inventory}{$id};

		if ($item->{data}{name} eq "b1-y1") {
			if ($money_id) {
				client_out($bot, qq~<JOIN id1="$id" id2="$money_id"/>~);

				$bot->{inventory}{$money_id}{data}{count} += $item->{data}{count};
				delete $bot->{inventory}{$id};
			} else {
				$money_id = $id;
			}
		}
	}

	return $money_id;
}

sub client_in
{
	my ($bot, $data) = @_;

	my $xml = xml_parse_s($data);

	foreach my $key (@{$xml}) {
		if ($key->{name} eq "KEY") {
#			client_out($bot, qq~<LOGIN l="$bot->{login}" p="~.encrypt($key->{data}{s}, $bot->{pass}).qq~" v="$bot->{proto_ver}" v2="$conf{flash_ver}"/>~);
#			client_out($bot, qq~<LOGIN l="$bot->{login}" p="$bot->{pass}" v="$bot->{proto_ver}" v2="$conf{flash_ver}"/>~);
			client_out($bot, qq~<LOGIN v2="$conf{flash_ver}" v="$conf{proto_ver}" p="~.encrypt($key->{data}{s}, $bot->{pass}).qq~" l="$bot->{login}" />~); 
		} elsif ($key->{name} eq "OK") {
			$bot->{ses} = $key->{data}{ses};
			client_out($bot, "<GETME/>");
			$bot->{client}{getme} = 1;

		} elsif ($key->{name} eq "ERROR") {
			msg("$bot->{login}  Client ERROR: code=".(defined $key->{data}{code}?$key->{data}{code}:"undef"));

			if ($key->{data}{code} eq "5") {
				$bot->{proto_ver}++;

			} elsif ($key->{data}{code} eq "3" || $key->{data}{code} eq "9" || $key->{data}{code} eq "10") {
				$bot->{sleep} = time+300;

			} else {
				touch("$home/tzbot.$bot->{id}.lock");
			}
			
		} elsif ($key->{name} eq "MYPARAM") {
			if ($bot->{client}{getme}) {
				$bot->{client}{in_game} = 1;
				
				$bot->{params} = {};
				$bot->{inventory} = {};
				
				client_out($bot, "<CHAT />");
			}
			
			$bot->{params}{$_} = $key->{data}{$_} foreach keys %{$key->{data}};

			foreach my $item (@{$key->{child}}) {
				if ($item->{name} eq "O") {
					if ($item->{data}{name} eq "b1-y1") {
						if ($bot->{client}{money_id}) {
							client_out($bot, qq~<JOIN id1="$item->{data}{id}" id2="$bot->{client}{money_id}"/>~);
							$bot->{client}{money} += $item->{data}{count};

						} else {
							$bot->{client}{money_id} = $item->{data}{id};
							$bot->{client}{money} = $item->{data}{count};
						}
					}

					$bot->{inventory}{$item->{data}{id}} = $item;
				}
			}

			pack_inventory($bot);

			if ($bot->{client}{getme}) {
				delete $bot->{client}{getme};
				&{$mods{$bot->{mod}}{on_connect}}($bot) if $bot->{mod};
			}
		
		} elsif ($key->{name} eq "CHAT") {
			$bot->{client}{chat_on} = 1;

		} elsif ($key->{name} eq "ADD_ONE") {
			foreach my $item (@{$key->{child}}) {
				if ($item->{name} eq "O") {
					if ($item->{data}{name} eq "b1-y1") {
						if ($bot->{client}{money_id}) {
							client_out($bot, qq~<JOIN id1="$item->{data}{id}" id2="$bot->{client}{money_id}"/>~);
							$bot->{client}{money} += $item->{data}{count};
						} else {
							$bot->{client}{money_id} = $item->{data}{id};
							$bot->{client}{money} = $item->{data}{count};
						}
					}

					$bot->{inventory}{$item->{data}{id}} = $item;
				}
			}

			pack_inventory($bot);

		} elsif ($bot->{mod} && &{$mods{$bot->{mod}}{on_data}}($bot, $key, $data)) {
		}
	}
}

sub client_out
{
	my ($bot, $text, $tm) = @_;
	push @{$bot->{client}{outdata}}, { m => $text, t => $tm||0 };
}

sub chat_in
{
	my ($bot, $data) = @_;

	return $bot->{chat}{skip_cmd}-- if $bot->{chat}{skip_cmd};

	my $xml = xml_parse_s($data);
	
	foreach my $key (@{$xml}) {
		if ($key->{name} eq "CHAT") {
			chat_out($bot, qq~<CHAT l="$bot->{login}" ses="$bot->{ses}"/>~);
			
		} elsif ($key->{name} eq "KEY") {
			chat_out($bot, "<CHAT/>");

		}elsif ($key->{name} eq "R") {
			my ($loc, $list) = split "\t", $key->{data}{t};

			if (defined $bot->{chat}{loc_name}) {
				my $clear_old = 1;
				foreach my $temp (@bots) {
					if ($temp != $bot && $temp->{chat}{loc_name} eq $bot->{chat}{loc_name}) {
						$clear_old = 0;
						last;
					}
				}
				push @loc_cache, qq~<R loc="$bot->{chat}{loc_name}"/>~ if $clear_old;
			}

			$bot->{chat}{loc_name} = $loc;
			push @loc_cache, qq~<R loc="$bot->{chat}{loc_name}"/>~;

			foreach (split ",", $list) {
				if ($_) {
					my ($bid, $grp, $flag, $clan, $login, $level) = split "/", $_;
					my ($pro, $woman) = (($flag>>5)&63, ($flag&8192)?1:0);

					push @loc_cache, qq~<A loc="$bot->{chat}{loc_name}" login="$login" clan="$clan" level="$level" pro="$pro" woman="$woman"/>~;
				}
			}

			$bot->{chat}{skip_cmd} = 2;

		} elsif ($key->{name} eq "A") {
			my ($bid, $grp, $flag, $clan, $login, $level) = split "/", $key->{data}{t};
			my ($pro, $woman) = (($flag>>5)&63, ($flag&8192)?1:0);

			push @loc_cache, qq~<A loc="$bot->{chat}{loc_name}" login="$login" clan="$clan" level="$level" pro="$pro" woman="$woman"/>~;

		} elsif ($key->{name} eq "D") {
			push @loc_cache, qq~<D loc="$bot->{chat}{loc_name}" login="$key->{data}{t}"/>~;

		} elsif ($key->{name} eq "S") {
			if ($key->{data}{t} =~ /^.{9}((\d\d:\d\d) \[(.+?)\] (.*))\t(.+)$/) {
				chat_private($bot, html_unescape($1), html_unescape($3), html_unescape($4));
			}
		}
	}
}

sub chat_out
{
	my ($bot, $text, $tm) = @_;
	push @{$bot->{chat}{outdata}}, { m => $text, t => $tm||0 };
}

sub chat_msg
{
	my ($bot, $text, $tm) = @_;
	chat_out($bot, qq~<POST t="~.html_escape($text).(" "x(int rand 10)).qq~"/>~, $tm||0) if $bot->{chat};
}

sub chat_private
{
	my ($bot, $full, $from, $text) = @_;
	
	my $clan = "clan";
	my $tome = 0;
	my $to_clan = 0;
	my $to_alliance = 0;
	if ($from ne $bot->{login}) {
		while ($text =~ /\s*private \[(.+?)\]\s*/g) {
			if (lc $1 eq lc $bot->{login}) { $tome = 1; }
			elsif (lc $1 eq "clan") { $to_clan = 1; }
			elsif(lc $1 eq "alliance") {
				$clan = "alliance";
				$to_alliance = 1;
			}
		}
		while ($text =~ /\s*to \[(.+?)\]\s*/g) {
			if (lc $1 eq lc $bot->{login}) { $tome = 1; }
		}
	}
	
	if ($meeting_log ne "no") {
		if (open my ($file), ">>/home/sites/police/www/academy/log-".$meeting_log.".txt") {
			print $file "$full\n";
			close $file;
		}		
	}

	if ($tome || $from eq $bot->{login}) {
		if (open my ($file), ">>$logs/chat-".$bot->{id}."-".strftime("%Y%m%d", localtime).".txt") {
			print $file "$full\n";
			close $file;
		}
	}

#start clan flood
	$text =~ (/^\s*private \[(.+?)\]\s*(.*)$/);
#	if (!($to_alliance) && $from ne "Terminal PA" && $2 ne "" && $shutup < time) {	
	if ($from ne "Terminal PA" && $2 ne "" && $shutup < time) {
		my $text_msg = $2;
		if (($from eq "FANTASTISH" || $from eq "Ell" || $from eq "deadbeef" || $from eq "Natsha") && index(lc($2),'пиши') > -1 && $to_clan == 0 && $to_alliance == 0)
			{
				if ($meeting_log eq "no") {
					$meeting_log = strftime("%Y-%m-%d_%H-%M", localtime);
					chat_msg($bot, "private [$from] Пишу :yessir: И даже в чате флудить не буду :shuffle: А как закончите - скажите мне 'отдыхай', я вам грамотку-то и передам!");
				} else {
					chat_msg($bot, "private [$from] Так это, ваше высокоблагородие, уже пишу! А ежели зря стараюсь - так вы скажите 'отдыхай', я только того и жду :sad:");
				}
			}
		elsif (($from eq "FANTASTISH" || $from eq "Ell" || $from eq "deadbeef" || $from eq "Natsha") && index(lc($2),'отдыхай') > -1 && $meeting_log ne "no" && $to_clan == 0 && $to_alliance == 0)
			{
				chat_msg($bot, "private [$from] Благодарю, ваше высокоблагородие :yessir: Бумагу положил на полочку в архиве - http://www.tzpolice.ru/academy/log-".$meeting_log.".txt Разрешите чарочку за здравие государя? :tost:");
				chat_msg($bot, "private [$from] Да, просили доложить, что по адресу http://www.tzpolice.ru/academy/parser/ лежит расшифровщик логов для выкладывания на форуме :idea:", time+2);
				$meeting_log = "no";
			}

#			my $poruchik = "";
#			if (int(rand(99)) > 80)
#				{
#					$poruchik = " И встать когда с тобой разговаривает подпоручик!  :ban:";
#				}
			if ($meeting_log eq "no")
			{
				if (($from eq "Natsha" || $from eq "deadbeef" || $from eq "Ваулт_Любимая") && index(lc($2),':kulich:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :sad: Молчу, дурак...", time + 3);
					$shutup = time + 1800;
				}
				elsif ($from eq "Natsha" && index(lc($2),':hug:') > -1)
				{
					if (int(rand(2)) eq 1) {
						chat_msg($bot, "private [$clan] private [$from] :red:", time + 3);
						chat_msg($bot, "private [$clan] private [$from] :flowers:", time + 2);
						
					} else {
						chat_msg($bot, "private [$clan] private [$from] Любить - это не значит смотреть друг на друга, любить - значит вместе смотреть в одном направлении :old:", time + 3);
					
					}
					chat_msg($bot, "private [$clan] private [$from] :bayan: :serenade: ", time + 6);
				}
				elsif (($from eq "FANTASTISH" || $from eq "deadbeef" || $from eq "6Labs" || $from eq "Ell" || $from eq "AVE") && index(lc($2),':stupid:') > -1)
				{
					chat_msg($bot, "private [alliance] private [$from] :stupid2:", time + 3);
					chat_msg($bot, "private [alliance] private [$from] Пойду лучше кляузу накатаю :cop:", time + 5);
					$shutup = time + 1800;
				}
				elsif ($from eq "Juez" && index(lc($2),':rambo:') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] Ты ещё не маршал? :obm: А ну бегом фраги рубить, салага :m60:", time + 3);
#					chat_msg($bot, "private [alliance] private [$from] Пойду лучше кляузу накатаю :cop:", time + 5);
#					$shutup = time + 1800;
				}
				elsif ($from eq "Тесто" && index(lc($2),'привет') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] О, тесто подошло :crazy:", time + 3);
#					chat_msg($bot, "private [alliance] private [$from] Пойду лучше кляузу накатаю :cop:", time + 5);
#					$shutup = time + 1800;
				}
				elsif ($from eq "usver" && index(lc($2),':toothpick:') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] никто не любит мой сосед нурсултан телякбаев", time + 3);
				}
				elsif ($from eq "Adamastis" && index(lc($2),':ahtung:') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] Прювет, печенько! :crazylol:", time + 3);

				}

				elsif ($from eq "kaiii" && index(lc($2),':king:') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] Привет, Хозяин! :mol: Чай, кофе? Свежая газета у Вас на столе :yessir:", time + 3);

				}

				elsif ($from eq "GANIBAL" && index(lc($2),':naem:') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] :boogi: Мы - это целый мир.", time + 3);

				}
				elsif ($from eq "YURAN" && index(lc($2),'всем привет') > -1)
				{
					chat_msg($bot, "private [alliance] to [$from] Привед мой Сладкей! :crazy:", time + 3);
				}

				elsif ($to_clan == 0 && $to_alliance == 0 && ($from eq "FANTASTISH" || $from eq "deadbeef" || $from eq "Ell" || $from eq "Natsha" || $from eq "Atenais" || $from eq "Stealth" || $from eq "AVE") && $text =~ /^\s*private \[Terminal PA\] {(.+?)}(.*?)$/) {
					if (index(lc($1),'::') > -1){
						my ($clan, $text_msg2) = split(/::/, $1);
						chat_msg($bot, "private [$clan] private [$text_msg2] $2", time);
					} else {
						if (length $1 > 2 && length $1 < 17) {
							if (lc $1 eq 'clan') { chat_msg($bot, "private [clan] $2", time +0); }
							elsif (lc $1 eq 'alliance') { chat_msg($bot, "private [alliance] $2", time +0); }
							else { chat_msg($bot, "private [clan] private [$1] $2", time + 0); }
						}
					}
				}
		#		elsif (($from eq "FANTASTISH" || $from eq "6Labs" || $from eq "deadbeef" || $from eq "Любимая Дениски" || $from eq "Atenais" || $from eq "Stealth") && $text =~ /^\s*private \[Terminal PA\] {(.+?)}(.*?)$/) {
	#				if (length $1 > 2 && length $1 < 17) {
#						if (lc $1 eq 'clan') { chat_msg($bot, "private [clan] $2", time +0); }
#						elsif (lc $1 eq 'alliance') { chat_msg($bot, "private [alliance] $2", time +0); }
#						else { chat_msg($bot, "private [clan] private [$1] $2", time + 0); }
#					}
#				}
				
			elsif ($tome>0 && (index(lc($2),' баян') > -1 || index(lc($2),' боян') > -1 || index(lc($2),' :bayan: ') > -1))
			{
				if (int(rand(2)) eq 1) {
					chat_msg($bot, "private [$clan] private [$from] Вы становитесь предсказуемым :smoke: ", time + 3);
				}else{
					chat_msg($bot, "private [$clan] private [$from] Повторить? :csotona: ", time + 3);
				}
			}
			elsif (index(lc($2),' хз ') > -1)
			{
			#	chat_msg($bot, "private [$clan] private [$from] :mad: Еще раз вырвешь из контекста фразу - собственноручно сдам. Все ясно? (c) Любимая Дениски", time + 3);
				chat_msg($bot, "private [$clan] private [$from] :mad: Еще раз вырвешь из контекста фразу - собственноручно сдам. Все ясно?", time + 3);
			}
			elsif (index(lc($2),' фз ') > -1 || index(lc($2),' пнх ') > -1 || index(lc($2),' фак ') > -1 || index(lc($2),' хрен ') > -1 || index(lc($2),' хер ') > -1 || index(lc($2),' мля ') > -1 || index(lc($2),' пипец ') > -1 || index(lc($2),' ппц ') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :nunu: смотри допрыгаешься у меня...", time + 3);
				}
			elsif (index(lc($2),'пост сдал') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :umn: Опять власть переменилась...", time + 3);
				}
			elsif (index(lc($2),'прикол') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :crazy:", time + 3);
				}
			elsif (index(lc($2),':bulldog:') > -1)
				{
				#	chat_msg($bot, "private [$clan] private [$from] ты не человек, ты животное! ЖИВОТНОЕ :mad: :rocket: (c) Clever_Xabokar", time + 3);
					chat_msg($bot, "private [$clan] private [$from] ты не человек, ты животное! ЖИВОТНОЕ :mad: :rocket:", time + 3);
				}
			elsif (index(lc($2),'тест ') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :umn: Семь раз отмерь - один раз отрежь!", time + 3);
				}
			elsif (index(lc($2),':agree:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Согласие есть продукт при полном непротивлении сторон :umn:", time + 3);
				}
			elsif (index(lc($2),':crzswans:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Малинки, малинки :swans:", time + 3);
				}
			elsif (index(lc($2),' нуп') > -1 || index(lc($2),' нуб') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :rtfm:", time + 3);
				}
			elsif (index(lc($2),' пока ') > -1)
				{
					if (int(rand(2)) eq 1)
						{
							chat_msg($bot, "private [$clan] private [$from] :farewell:", time + 3);
						}
				}
			elsif (index(lc($2),':kluv:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :bayan:", time + 3);
				}
			elsif (index(lc($2),'медвед') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] полики! :joy:", time + 3);
				}
			elsif (index(lc($2),'психов') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :admins:", time + 3);
				}
			elsif (index(lc($2),'инжей') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :help:", time + 3);
				}
			elsif (index(lc($2),':wall:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :wall: :grenade:", time + 3);
				}
			elsif (index(lc($2),':congr:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :friday:", time + 3);
				}
			elsif (index(lc($2),'привет') > -1)
				{
					if (int(rand(2)) eq 1)
						{
							chat_msg($bot, "private [$clan] private [$from] :hi:", time + 3);
						}
				}
			elsif (index(lc($2),'здрасте') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :priva:", time + 3);
				}
			elsif (index(lc($2),'спать') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :nobody:", time + 3);
				}
			elsif (index(lc($2),'молодец') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :proud:", time + 3);
				}
			elsif (index(lc($2),':swans:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Павлины, говоришь?", time + 3);
				}
			elsif (index(lc($2),'забыл') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :popcorn:", time + 3);
				}
#			elsif (index(lc($2),'копоф') > -1 || index(lc($2),'копов') > -1 || index(lc($2),'модерам') > -1 || index(lc($2),'модератор') > -1)
			elsif (index(lc($2),'копоф') > -1 || index(lc($2),'копов') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :cop:", time + 3);
				}
			elsif (index(lc($2),':bayan:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Сыграй что-нить такое, чтобы душа развернулась, а потом обратно завернулась!", time + 3);
				}
			elsif (index(lc($2),':rocket:') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Трубка 15, прицел 120, бац, бац - и мимо!", time + 3);
				}
			elsif (index(lc($2),'папуас') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] :loser:", time + 3);
				}
			elsif (index(lc($2),'банке') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] дай пару монет. на лечение!", time + 3);
				}
			elsif (index(lc($2),'штраф') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Я мзду не беру. Мне за державу обидно...", time + 3);
				}
			elsif (index(lc($2),'молчанк') > -1)
				{
					if (int(rand(2)) eq 1)
						{
							chat_msg($bot, "private [$clan] private [$from] Характеризует человека его словарь, т. е. сколько слов он знает - мало или много.", time + 3);
						}
					else
						{
							chat_msg($bot, "private [$clan] private [$from] Чем больше человек знает слов, тем меньше он в них нуждается.", time + 3);
						}
				}
			elsif (index(lc($2),'хрен') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] :row:", time + 3);
	                }
			elsif (index(lc($2),'основа') > -1 || index(lc($2),'основу') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] :smoke2:", time + 3);
	                }
			elsif (index(lc($2),'стреля') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Вот что ребята... Пулемёта я вам не дам.", time + 3);
	                }
			elsif (index(lc($2),'убью') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Джавдет мой... Встретишь - не трогай его...", time + 3);
	                }
	                elsif (index(lc($2),' каторг') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Вот ты думаешь, это мне дали пятнадцать суток? Это нам дали пятнадцать суток.", time + 3);
	                }
			elsif (index(lc($2),' мп ') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Кто такие? Сколько лет? Почему не в армии?", time + 3);
	                }
			elsif (index(lc($2),'скоро') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Скоро только кошки родятся!", time + 3);
	                }
			elsif (index(lc($2),'кушать') > -1 || index(lc($2),'поем') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] Месье! Же нэ манж па сис жур!", time + 3);
	                }
			elsif (index(lc($2),'рапорт') > -1)
	            	{
        	        	chat_msg($bot, "private [$clan] private [$from] ...докладчик сделает доклад, коротенько так, минут на сорок!", time + 3);
	                }
#			elsif (index(lc($2),'флуд') > -1)
#	            	{
#       	        	chat_msg($bot, "private [$clan] private [$from] Минуточку! Будьте добры, помедленнее!.. Я записываю...", time + 3);
#	                }
			elsif (index(lc($2),'выговор') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Да, плохо мы ещё воспитываем нашу молодёжь!", time + 3);
				}
			elsif (index(lc($2),'туплю') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Тьфу на вас! Тьфу на вас ещё раз.", time + 3);
				}
			elsif (index(lc($2),'водк') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Ключница водку делала?", time + 3);
				}
			elsif (index(lc($2),'кредит') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Скажите... А двести монет могут спасти гиганта мысли?", time + 3);
				}
			elsif (index(lc($2),'одолжи') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Может быть тебе дать ещё ключ от квартиры, где деньги лежат?", time + 3);
				}
			elsif (index(lc($2),'заткнись') > -1)
				{
					chat_msg($bot, "private [$clan] private [$from] Мой папа был турецкоподданный!", time + 3);
					if (int(rand(99)) > 80)
						{
							chat_msg($bot, "private [$clan] private [$from] И встать когда с тобой разговаривает подпоручик! :ban:", time + 5);
					}
				}
		}
	}

#end clan flood


	if ($text =~ /^\s*private \[(.+?)\]\s*(.*)$/) {

		if (lc $1 eq lc $bot->{login} && $2 ne "") {
			my $text_msg = $2;
			my ($cmd, $arg) = split /\s+/, $text_msg, 2;

			my ($root, $boss, $iscmd);

			foreach (@{$conf{owners}}) {
				if (lc $_ eq lc $from) {
					$root = $boss = 1;
					last;
				}
			}

			foreach (@{$bot->{owners}}) {
				if (lc $_ eq lc $from) {
					$boss = 1;
					last;
				}
			}

			if ($root) {
				if ($cmd eq "help") {
					my @cmds = (@root_cmds);
					push @cmds, @{$mods{$bot->{mod}}{root_cmds}} if $bot->{mod};

					chat_msg($bot, "private [$from] cmds: ".join(", ", @cmds));
					$iscmd = 1;

				} elsif ($cmd eq "kill") {
					touch("$home/tzbot.$bot->{id}.lock");
					$iscmd = 1;

				} elsif ($cmd eq "all_sleep") {
					$_->{sleep} = time + 60*(defined $arg?$arg:5) foreach @bots;
					$iscmd = 1;

				} elsif ($cmd eq "crash") {
					touch("$home/tzbot.lock");
					exit;

				} elsif ($cmd eq "restart") {
					exit;

				} elsif ($bot->{mod}) {
					$iscmd = &{$mods{$bot->{mod}}{on_root_cmd}}($bot, $cmd, $arg);
				}
			}

			if ($boss) {
				if ($cmd eq "help") {
					unless ($root) {
						my @cmds = (@boss_cmds);
						push @cmds, @{$mods{$bot->{mod}}{boss_cmds}} if $bot->{mod};

						chat_msg($bot, "private [$from] cmds: ".join(", ", @cmds));
						$iscmd = 1;
					}
				} elsif ($cmd eq "sleep") {
					$bot->{sleep} = time + 60*(defined $arg?$arg:5);
					$iscmd = 1;

				} elsif ($cmd eq "status") {
					my ($online, $sleep, $locked, $offline) = (0, 0, 0, 0);
					foreach my $temp (@bots) {
						if ($temp->{client} && $temp->{client}{in_game}) {
							$online++;
						} elsif ($temp->{sleep} && $temp->{sleep}>time) {
							$sleep++;
						} elsif (-e "$home/tzbot.$temp->{id}.lock") {
							$locked++;
						} else {
							$offline++;
						}
					}
					chat_msg($bot, "private [$from] online: $online, sleep: $sleep, locked: $locked, offline: $offline");
					$iscmd = 1;

				} elsif ($bot->{mod}) {
					$iscmd = &{$mods{$bot->{mod}}{on_boss_cmd}}($bot, $cmd, $arg);
				}
			}
			my $reloadclients = time-($bot->{pl_fab}{lastcliload}||0);
			if ($reloadclients > 60)
				{
					load_clients();
					$bot->{pl_fab}{lastcliload} = time;
				}
			chat_reply($bot, $from, $text_msg) unless $iscmd;
		}
	}
}

sub chat_reply
{
	my ($bot, $from, $text) = @_;

	return if $bot->{ignore}{$from} && $bot->{ignore}{$from} > time;

	my $man = $bot->{params}{man}?1:0;

	foreach (@repl_keys) {
		if ($text =~ /$_/i && ($repl_phrases{$_}{$man} || $repl_phrases{$_}{c})) {
			#if (rand > 0.1) {
				if ($repl_phrases{$_}{$man}) {
					my $reply = $repl_phrases{$_}{$man}[int rand($#{$repl_phrases{$_}{$man}}+1)];
					chat_msg($bot, "private [$from] $reply", time + 3);
				}
				if ($repl_phrases{$_}{c}) {
					foreach my $temp (@{$repl_phrases{$_}{c}}) {
						if ($temp->{c} eq "ignore") {
							$bot->{ignore} = {} unless $bot->{ignore};
							$bot->{ignore}{$from} = time + 60*(defined $temp->{a}?$temp->{a}:60);
						}
					}
				}
			#}
			last;
		}
	}
}

sub get_phrase
{
	my ($bot, $mask) = @_;

	my $man = $bot->{params}{man} ? 1 : 0;

	return $repl_phrases{$mask."_".$bot->{id}}{$man}[int rand($#{$repl_phrases{$mask."_".$bot->{id}}{$man}}+1)] if $repl_phrases{$mask."_".$bot->{id}}{$man};
	return $repl_phrases{$mask}{$man}[int rand($#{$repl_phrases{$mask}{$man}}+1)] if $repl_phrases{$mask}{$man};

	return "";
}

foreach my $bot (@bots) {
	$bot->{proto_ver} = $conf{proto_ver};
}

my $select = IO::Select->new();
my %sock_hash;

while (1) {
	alarm 60;

	exit if -e "$home/tzbot.lock";

	foreach my $bot (@bots) {
		unless ($bot->{client}) {
			next if (-e "$home/tzbot.$bot->{id}.lock") || ($bot->{sleep} && $bot->{sleep} > time);

			msg("$bot->{login}  Client: Connecting");

			my $sock;

			if ($blocking) {
				$sock = IO::Socket::INET->new(PeerAddr => "city1.timezero.ru", PeerPort => 5190, Blocking => 1);
#				$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.168", PeerPort => 5190, Blocking => 1);
#				$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.168", PeerPort => 5191, Blocking => 1);
			} else {
				$sock = IO::Socket::INET->new(PeerAddr => "city1.timezero.ru", PeerPort => 5190);
#				$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.168", PeerPort => 5191);
#				$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.168", PeerPort => 5190);

			}

			if ($sock) {
				msg("$bot->{login}  Client: Connected");

				$sock->blocking(0);

				$sock_hash{$sock} = $bot;
				$select->add($sock);

				$bot->{client} = { sock => $sock, last_out => time };
			} else {
				msg("$bot->{login}  Client: Failed");
				$bot->{sleep} = time + 60;
			}

		} elsif ((-e "$home/tzbot.$bot->{id}.lock") || ($bot->{sleep} && $bot->{sleep} > time)) {
			client_out($bot, "<LOGOUT/>");

		} elsif ($bot->{client}{in_game} && $bot->{client}{chat_on}) {
			
			&{$mods{$bot->{mod}}{on_idle}}($bot) if $bot->{mod};

			unless ($bot->{chat}) {
				msg("$bot->{login}  Chat: Connecting");

				my $sock;
				
				if ($blocking) {
					$sock = IO::Socket::INET->new(PeerAddr => "chat.timezero.ru", PeerPort => 5190, Blocking => 1);
#					$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.180", PeerPort => 5190, Blocking => 1);
#					$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.180", PeerPort => 5191, Blocking => 1);
				} else {
					$sock = IO::Socket::INET->new(PeerAddr => "chat.timezero.ru", PeerPort => 5190);
#					$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.180", PeerPort => 5190);
#					$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.180", PeerPort => 5191);
				}

				if ($sock) {
					msg("$bot->{login}  Chat: Connected");

					$sock->blocking(0);

					$sock_hash{$sock} = $bot;
					$select->add($sock);
					
					$bot->{chat} = { sock => $sock, last_out => time };
					
					chat_out($bot, qq~<CHAT l="$bot->{login}" ses="$bot->{ses}"/>~);
					
				} else {
					msg("$bot->{login}  Chat: Failed");
				}
			}
		}
	}

	foreach my $sock ($select->can_read(0.2)) {
		my $bot = $sock_hash{$sock};
		next unless $bot;

		my $data = "";
		my $flag = $sock->recv($data, POSIX::BUFSIZ);

		my $flager = "";
		if (defined $flag) { $flager = "true"; }
		else { $flager = "false"; }
		
		my $sockv = "";
		if($sock == $bot->{client}{sock}){
			$sockv = "Client";
		}else{
			$sockv = "Chat";
		}
		msg("$bot->{login}  $sockv SOCKET-READ $sock : Flag[ $flager ] Data[ ".length($data)." ]") if $debug;
		
		unless(defined $flag && length $data) {
			if ($sock == $bot->{client}{sock}) {
				msg("$bot->{login}  Client: Disconnected");
				
				&{$mods{$bot->{mod}}{on_disconnect}}($bot) if $bot->{mod};
				
				if ($bot->{chat}) {
					$select->remove($bot->{chat}{sock});
					delete $sock_hash{$bot->{chat}{sock}};

					delete $bot->{chat};
				}

				$select->remove($sock);
				delete $sock_hash{$sock};

				delete $bot->{client};
			} else {
				msg("$bot->{login}  Chat: Disconnected1");

				$select->remove($sock);
				delete $sock_hash{$sock};

				delete $bot->{chat};
			}
		} else {
			if ($sock == $bot->{client}{sock}) {
				$bot->{client}{indata} .= $data;

				while ($bot->{client}{indata} =~ s/^(.*?)\0//s) {
					my $temp = from_utf(decomp($1));
					msg("$bot->{login}  Client RECV: $temp") if $debug;
					client_in($bot, $temp);
				}
			} else {
				$bot->{chat}{indata} .= $data;
				while ($bot->{chat}{indata} =~ s/^(.*?)\0//s) {
					my $temp = from_utf(chat_decomp($1));
					msg("$bot->{login}  Chat RECV: $temp") if $debug;
					chat_in($bot, $temp);
				}
			}
		}
	}

	foreach my $sock ($select->can_write(0.2)) {
		my $bot = $sock_hash{$sock};
		next unless $bot;
		if ($sock == $bot->{client}{sock}) {
			my @temp;
			while (my $data = shift @{$bot->{client}{outdata}}) {
				if ($data->{t} > time) {
					push @temp, $data;
				} else {
					msg("$bot->{login}  Client SEND: $data->{m}") if $debug;

					my $data = to_utf("$data->{m}\0");
					my $flag = $sock->send($data);

					unless (defined $flag && length $data == $flag) {
						msg("$bot->{login}  Client: Disconnected");

						&{$mods{$bot->{mod}}{on_disconnect}}($bot) if $bot->{mod};

						if ($bot->{chat}) {
							$select->remove($bot->{chat}{sock});
							delete $sock_hash{$bot->{chat}{sock}};
							delete $bot->{chat};
						}

						$select->remove($sock);
						delete $sock_hash{$sock};

						delete $bot->{client};
					} else {
						$bot->{client}{last_out} = time;
					}
				}
			}
			$bot->{client}{outdata} = \@temp;
			client_out($bot, qq~<N s="$bot->{ses}"/>~)
			if $bot->{client}{in_game} && time-$bot->{client}{last_out} >= 60;
			
		} else {
			my @temp;
			while (my $data = shift @{$bot->{chat}{outdata}}) {
				if ($data->{t} > time) {
					push @temp, $data;
				} else {
					msg("$bot->{login}  Chat SEND: $data->{m}") if $debug;

					my $data = to_utf("$data->{m}\0");
					my $flag = $sock->send($data);

					unless (defined $flag && length $data == $flag) {
						msg("$bot->{login}  Chat: Disconnected2");

						$select->remove($sock);
						delete $sock_hash{$sock};

						delete $bot->{chat};
					} else {
						$bot->{chat}{last_out} = time;
					}
				}
			}
			$bot->{chat}{outdata} = \@temp;
			chat_out($bot, "<N/>") if time-$bot->{chat}{last_out} >= 30;
		}
	}

	save_loc() if time-$loc_saved >= 60;
	
	select(undef, undef, undef, 0.3);
}