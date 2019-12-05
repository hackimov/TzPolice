use strict;
our $home2 = "/home/sites/police/bots/smarts_monitor";
our (%mods, %conf, $logs, @res_names);
$mods{pl_monitor} = {
	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_monitor_on_connect,
	on_disconnect	=> \&pl_monitor_on_disconnect,
	on_idle		=> \&pl_monitor_on_idle,
	on_data		=> \&pl_monitor_on_data,
	on_root_cmd	=> \&pl_monitor_on_root_cmd,
	on_boss_cmd	=> \&pl_monitor_on_boss_cmd,

};

sub pl_monitor_on_connect
{
	my $bot = shift;
	$bot->{pl_monitor} = {} unless $bot->{pl_monitor};
	$bot->{pl_monitor_ses} = {};
}

sub pl_monitor_on_disconnect
{
	my $bot = shift;
	delete $bot->{pl_monitor_ses}
}

sub pl_monitor_on_idle
{
	my $bot = shift;
	my $wait = time-($bot->{pl_monitor}{lastscan}||0);
	my $wait2 = time-($bot->{pl_monitor}{lastscan2}||0);
	my $wait3 = ($bot->{pl_monitor}{lastscan3}||0);
	my $wait5 = time-($bot->{pl_monitor}{lastscan5}||0);
	
	if ($bot->{pl_monitor_ses}{busy})
		{
			client_out($bot, "<LOGOUT/>") if $wait5 > $conf{pl_fab_hangs};
			return;
		}
	if ($wait5 > $conf{pl_online_rescan}) {
		$bot->{pl_monitor}{lastscan5} = time;
		$bot->{pl_monitor_ses}{busy} = 1;

		msg("$bot->{login}: Clan-list scan");
		client_out($bot, qq~<STATUS clan="1" />~);
		return;
	}
#	else {msg("$bot->{login}: Timeout is not out $wait5");}

}

sub pl_monitor_on_data
{
	my ($bot, $key, $data) = @_;
	if ($key->{name} eq "STATUS")
		{
		if (defined $key->{data}{clan}) {
			my $fname = "$logs/smart-".strftime("%Y%m%d%H%M%S", localtime);
			if (open my ($file), ">$fname.tmp") {
				print $file $key->{data}{clan};
				close $file;

				rename "$fname.tmp", "$fname.txt";
			}

			delete $bot->{pl_monitor_ses}{busy};
			#msg("$bot->{login}  Police: End scan");
			}
		}
	return 0;
}

sub pl_monitor_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

sub pl_monitor_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

1;