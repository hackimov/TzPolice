#!/usr/bin/perl -w
use CGI::Carp "fatalsToBrowser"; # DimoN
use strict;
use sigtrap qw(stack-trace old-interface-signals ALRM);

our @at_exit;

END { &{$_}() foreach reverse @at_exit; }

use IO::Socket;
use IO::Select;
use Encode qw(from_to);
use POSIX;
use Tie::File;
use Time::Local;
use Fcntl;
use List::Util qw(min max);

setlocale LC_CTYPE, "ru_RU.CP1251";
use locale;

our $home = ".";
our $logs = ".";

our $debug = 0;

#=begin




$home = "/home/sites/police/bots/forpost_ads";
$logs = "$home/logs";

$debug = 0;

#open STDERR, ">>$home/tzbot.log";
#open STDOUT, ">&STDERR";
open(STDERR,">/dev/null") or open(STDERR,">nul") or die "Can't find null device!";
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
	if (open my $file, "$home/tzbot.pid") {
		my $pid = <$file>;
		chomp $pid;
		close $file;
		exit if -d "/proc/$pid";
	}
	
	if (open my $file, ">$home/tzbot.pid") {
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
#require "$home/mod.pl_fab.pl";
require "$home/mod.forpost_ads.pl";
require "$home/mod.forgotten_ads.pl";
require "$home/mod.uncle_st_ads.pl";
require "$home/mod.smart.pl";

our (%conf, @bots, %mods);

our @otherhistoryqueue;

msg("TZBOT started");

our $blocking = IO::Socket::INET->new(Blocking => 1)?1:0;
msg("Non blocking socket failed") unless $blocking;

my @root_cmds = ("help", "status", "sleep [n]", "kill", "all_sleep [n]", "crash", "restart");
my @boss_cmds = ("help", "status", "sleep [n]");

our (@repl_keys, %repl_phrases);

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
			client_out($bot, qq~<LOGIN v2="$conf{flash_ver}" v="$conf{proto_ver}" p="~.encrypt($key->{data}{s}, $bot->{pass}).qq~" pp="~.encrypt($key->{data}{s}, $conf{private_key}).qq~" l="$bot->{login}" />~); 
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
			
			} elsif ($key->{data}{code} eq "1" && $bot->{server} < 3) {
				msg($bot->{login}." SERVER ERROR: ".$bot->{server});
				$bot->{server}++;


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
		if ($key->{name} eq "KEY") {
			chat_out($bot, "<CHAT/>");

		} elsif ($key->{name} eq "CHAT") {
			chat_out($bot, qq~<CHAT l="$bot->{login}" ses="$bot->{ses}"/>~);

		} elsif ($key->{name} eq "R") {
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

	my $tome = 0;
	if ($from ne $bot->{login}) {
		while ($text =~ /private \[(.+?)\]/g) {
			if (lc $1 eq lc $bot->{login}) {
				$tome = 1;
				last;
			}
		}
	}

	if ($tome || $from eq $bot->{login}) {
		if (open my ($file), ">>$logs/chat-".$bot->{id}."-".strftime("%Y%m%d", localtime).".txt") {
			print $file "$full\n";
			close $file;
		}
	}

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
			my $allow_his=0;
			foreach (@{$conf{history_allowed}}){
				if (lc $_ eq lc $from){
					$allow_his = 1;
					last;
				}
			}

			if (($cmd eq "getinf")||($cmd eq "getbtl"))
			{
                if ($allow_his){
	                $iscmd=1;
					if ($text_msg=~ /get(inf|btl)\s+(.+?)\s+(\d\d\.\d\d\.\d\d)\s+(\d\d\.\d\d\.\d\d)$/){
					    my $startdate=$3;my $enddate=$4;my $victim=$2;my $getcmd=$1;
					    my $correct=1;my $startsecs=0;my $endsecs=0;
					    $startdate=~/(\d+)\.(\d+)\.(\d+)/;
					    eval{$startsecs=timelocal(0,0,0,$1,$2-1,$3+100);};
					    if ($@){
	            	    	chat_msg($bot,"private [$from] ���-�� � ��� �� ��� � ��������� ���... � ��� ������������ �� ����... :dkn:");
	            	    	$correct=0;
					    }else{
							$enddate=~/(\d+)\.(\d+)\.(\d+)/;
						    eval{$endsecs=timelocal(0,0,0,$1,$2-1,$3+100);};
					    }
					    if ($@){
	            	    	chat_msg($bot,"private [$from] ���-�� � ��� �� ��� � ��������� ���... � ��� ������������ �� ����... :dkn:");
	            	    	$correct=0;
					    }
					    else{
						    my $currtime=time;
						    if ($startsecs>$endsecs){
        	    	    	chat_msg($bot,"private [$from] ���� ������ ������������ ������ ���� ����� ������������! :mad:");
            		    	$correct=0;
						    }
						    if ($startsecs>$currtime){
	            	    		chat_msg($bot,"private [$from] ���� ������ ������������ ������ ������� ����! :mad:");
    	        	    	$correct=0;
						    }
						    if ($endsecs>$currtime){
            		    	chat_msg($bot,"private [$from] ���� ����� ������������ ������ ������� ����! :mad:");
            	    		$correct=0;
					    	}
					    }
					    if ($correct){
	        	        	push(@otherhistoryqueue,$getcmd."|".$from."|".$victim."|".$startdate."|".$enddate."|".time);
    	        	    	chat_msg($bot,"private [$from] ��� ������ ��������� � �������. ��� ����� � ������� - ".scalar @otherhistoryqueue.". ����� ������.");
            	    	}
                	}else{
	                	chat_msg($bot,"private [$from] Syntax error.");
    	            }
    	        }
    	        else{
					chat_msg($bot, "private [$from] Access denied to ".(lc $from));
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

			chat_reply($bot, $from, $text_msg) unless $iscmd;
		}
	}
}

sub chat_reply
{
	my ($bot, $from, $text) = @_;

	return if $bot->{ignore}{$from} && $bot->{ignore}{$from} > time;
	return if $bot->{nochat};

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
				$sock = IO::Socket::INET->new(PeerAddr => "city".$bot->{server}.".timezero.ru", PeerPort => 5190, Blocking => 1);
#				$sock = IO::Socket::INET->new(PeerAddr => "city".$bot->{server}.".timezero.ru", PeerPort => 5191, Blocking => 1);
#				$sock = IO::Socket::INET->new(PeerAddr => "213.79.68.168", PeerPort => 5190, Blocking => 1);
			} else {
#				$sock = IO::Socket::INET->new(PeerAddr => "city".$bot->{server}.".timezero.ru", PeerPort => 5191);
				$sock = IO::Socket::INET->new(PeerAddr => "city".$bot->{server}.".timezero.ru", PeerPort => 5190);
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

		if($debug){
			my $flager = "";
			if (defined $flag) { $flager = "true"; }
			else { $flager = "false"; }
			
			my $sockv = "";
			if($sock == $bot->{client}{sock}){
				$sockv = "Client";
			}else{
				$sockv = "Chat";
			}
			msg("$bot->{login}  $sockv SOCKET-READ $sock : Flag[ $flager ] Data[ ".length($data)." ]");
		}
		
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
