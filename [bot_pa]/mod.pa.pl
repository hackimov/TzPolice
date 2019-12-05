use strict;
our $home2 = "/home/sites/police/bot_pa";
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
sub load_alerts	{
		open (BASE, "<$home2/alerts.txt");
		my @base = <BASE>;
		close (BASE);
		unlink("$home2/alerts.txt");
		touch("$home2/alerts.txt");
		my $q = 0666;
		my $w = "$home2/alerts.txt";
		chmod($q, $w);
		for (my $i=$#base; $i>=0; $i--)	{			(my $nick, my $msg)=split (/ \|\| /, $base [$i]);
			msg(qq~ADD message^ $nick => $msg~);
			$alerts{$nick}=$msg;
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
	my $wait2 = time-($bot->{pl_fab}{lastscan2}||0);
	my $wait3 = ($bot->{pl_fab}{lastscan3}||0);

	if ($bot->{pl_fab_ses}{busy})
		{
			client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_fab_hangs};
			return;
		}
	if ($wait > $conf{pl_fab_rescan})
		{
#				%alerts = ();
#				load_alerts();
#				foreach my $person (keys %alerts)
#					{
#						client_out($bot, qq~<PDA p2="$alerts{$person}" p1="$person" id="" />~);
#					}
				$bot->{pl_fab}{lastscan} = time;
				$bot->{pl_fab_ses}{busy} = 1;
				my $time = time-7200;
				client_out($bot, qq~<STATUS clan="1" />~);
				$bot->{pl_fab_ses}{history} = [];
				return 0;
		}
	if ($wait2 > $conf{pl_fab_rescan_2})
		{
				$bot->{pl_fab}{lastscan2} = time;
				$bot->{pl_fab_ses}{busy} = 1;
				%alerts = ();
				load_alerts();
				foreach my $person (keys %alerts) {
						if($person != "-1"){							msg(qq~message from $person => $alerts{$person} | sended~);
							client_out($bot, qq~<PDA p2="$alerts{$person}" p1="$person" id="9033393199.1" />~);
						}
				}
				$bot->{pl_fab_ses}{busy} = 0;
				return 0;
		}

		my $current_hour = strftime("%H", localtime);
		if ($wait3 != $current_hour)
			{
				my $cur_time = time;

				$bot->{pl_fab}{lastscan3} = $current_hour;
				$bot->{pl_fab_ses}{busy} = 1;
				open (BASE, "<".$home2."/bash/bash".$current_hour.".txt");
				my @base = <BASE>;
				close (BASE);
			#	unlink($home2."/bash/bash".$current_hour.".txt");

				for (my $i=0; $i<=$#base; $i++) {
					if($base[$i] ne "" && $base[$i] ne " " && $base[$i] ne "\n"){
						#chat_msg($bot, "private [alliance] ".$base[$i]."", $cur_time+2);
						$cur_time = $cur_time+2;
					}
				}

				$bot->{pl_fab_ses}{busy} = 0;
				return;
			}
}

sub pl_fab_on_data
{
	my ($bot, $key, $data) = @_;
	if ($key->{name} eq "STATUS")
		{
		if (defined $key->{data}{clan}) {
			my $fname = "$logs/pa-".strftime("%Y%m%d%H%M%S", localtime);
			if (open my ($file), ">$fname.tmp") {
				print $file $key->{data}{clan};
				close $file;

				rename "$fname.tmp", "$fname.txt";
			}

			delete $bot->{pl_fab_ses}{busy};
			msg("$bot->{login}  Police: End scan");
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