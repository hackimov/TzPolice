use strict;

our (%mods, %conf, $logs, @res_names);

$mods{pl_fab} = {

	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_fab_on_connect,
	on_disconnect	=> \&pl_fab_on_disconnect,
	on_idle		=> \&pl_fab_on_idle,
	on_data		=> \&pl_fab_on_data,
	on_root_cmd	=> \&pl_fab_on_root_cmd,
	on_boss_cmd	=> \&pl_fab_on_boss_cmd,

};

sub pl_fab_on_connect
{
	my $bot = shift;

	#msg("$bot->{login}  pl_fab_on_connect");

	$bot->{pl_fab} = {} unless $bot->{pl_fab};
	$bot->{pl_fab_ses} = {};
	$bot->{pl_fab}{lastsay} = 0;
}

sub pl_fab_on_disconnect
{
	my $bot = shift;

	#msg("$bot->{login}  pl_fab_on_disconnect");

	delete $bot->{pl_fab_ses}
}

sub pl_fab_on_idle
{
	my $bot = shift;

	#msg("$bot->{login}  pl_fab_on_idle");

	my $wait = time-($bot->{pl_fab}{lastscan_2}||0);

	if ($bot->{pl_fab_ses}{busy_2}) {
		client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_fab_hangs_2};
		return;
	}

	$wait = time-($bot->{pl_fab}{lastscan}||0);
	my $wait2 = time-($bot->{pl_fab}{lastsay}||0);

	if ($bot->{pl_fab_ses}{busy}) {
		client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_fab_hangs};
		return;
	}

	if ($wait > $conf{pl_fab_rescan}) {

		$bot->{pl_fab}{lastscan} = time;
		$bot->{pl_fab_ses}{busy} = 1;

		msg("$bot->{login}  Plant: Begin scan");

		client_out($bot, qq~<GOBLD n="9" />~) if $bot->{id} eq "fabzon";
		client_out($bot, qq~<GOBLD n="10" />~) if $bot->{id} eq "labzon";
		
		client_out($bot, qq~<FC/>~) if $bot->{id} eq "fabzon";
		client_out($bot, qq~<LB/>~) if $bot->{id} eq "labzon";

		return;
	}

	if ($wait2 > 600) {

		$bot->{pl_fab}{lastsay} = time;
		#chat_msg($bot, qq~Внимание заключённым! Акция! До понедельника(02.07.2012) включительно у Вас есть уникальная возможность выйти с каторги в ТРИ раза быстрее. На период действия акции выход с каторги возможен при наборе всего 33,3% от срока!~);
		return;
	}
}

sub pl_fab_on_data
{
	my ($bot, $key, $data) = @_;

	#msg("$bot->{login}  pl_fab_on_data");

	if ($key->{name} eq "FC" || $key->{name} eq "LB") {

		if (defined $key->{data}{p3} || defined $key->{data}{chip}) {
		#	my $time = time-7200;		#2 hours
		#	my $time = time-86400;		#24 hours
		#	my $time = time-172800;		#48 hours
			my $time = time-259200;		#72 hours
		#	my $time = time-345600;		#96 hours
		#	my $time = time-604800;		#week
		#	my $time = time - 864000;	#10 days
		#   my $time = time - 1209600;	#14 days


			msg("$bot->{login}  Plant: History ".strftime("%d.%m.%y", localtime $time));
			client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="\$building"/>~);

			$bot->{pl_fab_ses}{history} = [];
		} else {
			delete $bot->{pl_fab_ses}{busy};
			msg("$bot->{login}  Plant: Scan error: Not owner");

			my @to_str;

			push @to_str, "private [$_]" foreach @{$bot->{owners}};

			if (@to_str) {
				my $reply = get_phrase($bot, "#pl_fab_no_key");
				chat_msg($bot, join(" ", @to_str)." ".$reply) if $reply ne "";
			}
		}

		return 1;

	} elsif ($key->{name} eq "HISTORY") {
		if ($data =~ /<HISTORY.*?>(.*)<\/HISTORY>/s) {
			my $history = $1;
			foreach (split /\r?\n/, $history) {
				push @{$bot->{pl_fab_ses}{history}}, $_;
			}
		}

		if ($key->{data}{date} ne strftime "%d.%m.%y", localtime) {
			
			my ($day, $mon, $year) = split /\./, $key->{data}{date};
		
			my $time = 93600 + mktime 0, 0, 0, $day, $mon-1, $year+100;
			
			msg("$bot->{login}  Plant: History ".strftime("%d.%m.%y", localtime $time));
			
			client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="\$building"/>~);

		} else {
			my $fname = "$logs/fab-$bot->{id}-".strftime("%Y%m%d%H%M%S", localtime);

			if (open my ($file), ">$fname.tmp") {
				print $file join "\n", @{$bot->{pl_fab_ses}{history}};
				close $file;

				rename "$fname.tmp", "$fname.txt";
			}

			delete $bot->{pl_fab_ses}{busy};
			msg("$bot->{login}  Plant: End scan");
		}

		return 1;

	}

	return 0;
}

sub pl_fab_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_root_cmd");

	return 0;
}

sub pl_fab_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_boss_cmd");

	return 0;
}

1;
