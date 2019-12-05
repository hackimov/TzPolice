use strict;
our $home2 = "/home/sites/police/bot_fees";
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
our %alerts = ();
our %alerts_sender = ();
sub load_alerts
	{
		open (BASE, "<$home2/alerts.txt");
		my @base = <BASE>;
		close (BASE);
		unlink("$home2/alerts.txt");
		touch("$home2/alerts.txt");
		my $q = 0666;
		my $w = "$home2/alerts.txt";
		chmod($q, $w);
		for (my $i=$#base; $i>=0; $i--)
			{
				(my $sender, my $nick, my $msg)=split (/ \|\| /, $base [$i]);
				$::alerts{$nick}=$msg;
				$::alerts_sender{$nick}=$sender;
			}
	}

sub pl_fab_on_connect
{
	my $bot = shift;
	$bot->{pl_fab} = {} unless $bot->{pl_fab};
	$bot->{pl_fab_ses} = {};
}

sub pl_fab_on_disconnect
{
	my $bot = shift;
	delete $bot->{pl_fab_ses}
}

sub pl_fab_on_idle
{
	my $bot = shift;
	my $wait = time-($bot->{pl_fab}{lastscan}||0);
	#msg("$bot->{login}  : $wait");
    	my $wait2 = time-($bot->{pl_fab}{lastscan2}||0);
	if ($bot->{pl_fab_ses}{busy})
		{
			client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_fab_hangs};
			return;
		}
	if ($wait > $conf{pl_fab_rescan})
		{
                $bot->{pl_fab}{lastscan} = time;
				$bot->{pl_fab_ses}{busy} = 1;
				#msg("$bot->{login}  : Begin scan");
				#my $time = time-86400;
				my $time = time-7200;
				#my $time = time - 3600*24*7;
				#msg("$bot->{login}  : History ".strftime("%d.%m.%y", localtime $time));
				client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" />~);
				$bot->{pl_fab_ses}{history} = [];
				client_out($bot, qq~<SCANHIS/>~);
				return;
		}
	if ($wait2 > $conf{pl_fab_rescan_2})
		{
                		$bot->{pl_fab}{lastscan2} = time;
				$bot->{pl_fab_ses}{busy} = 1;
			#	msg("$bot->{login}  : Begin telegrams $wait");
#				%alerts = -1;
				%alerts = ();
				#%alerts_sender = -1;
				load_alerts();
				#my $time = time-259200;
				#my $time = time-7200;
				#msg("$bot->{login}  : History ".strftime("%d.%m.%y", localtime $time));
				foreach my $person (keys %alerts)
					{
					#	msg("$bot->{login}  : telegrams to $person - $alerts{$person}");
						if($person ne ""){
							client_out($bot, qq~<PDA id="1653782.1" p1="$person" p2="$alerts{$person}" />~);
				            	    	chat_msg($bot, qq~private [$alerts_sender{$person}] Телеграмма персонажу [$person] отправлена :joy:~);
						}
						#print "I know the age of $person is $ages{$person}\n";
					}
				$bot->{pl_fab_ses}{busy} = 0;
				return;
		}
}

sub pl_fab_on_data
{
	my ($bot, $key, $data) = @_;
	if ($key->{name} eq "HISTORY")
		{
#			msg("Incoming history");
			if ($data =~ /<HISTORY.*?>(.*)<\/HISTORY>/s)
				{
					my $history = $1;
					foreach (split /\r?\n/, $history)
						{
							push @{$bot->{pl_fab_ses}{history}}, $_;
						}
				}
    		if ($key->{data}{date} ne strftime "%d.%m.%y", localtime)
				{
					my ($day, $mon, $year) = split /\./, $key->{data}{date};
					my $time = 86400 + mktime 0, 0, 0, $day, $mon-1, $year+100;
     				msg("$bot->{login} : History ".strftime("%d.%m.%y", localtime $time));
					client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" />~);
     			}
			else
				{
					my $fname = "$logs/hist-$bot->{id}-".strftime("%Y%m%d%H%M%S", localtime);

					if (open my ($file), ">$fname.tmp")
						{
							print $file join "\n", @{$bot->{pl_fab_ses}{history}};
							close $file;
							rename "$fname.tmp", "$fname.txt";
						}
					delete $bot->{pl_fab_ses}{busy};
					#msg("$bot->{login} : End scan");
					return 1;
				}
		}
	return 0;
}

sub pl_fab_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

sub pl_fab_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

1;