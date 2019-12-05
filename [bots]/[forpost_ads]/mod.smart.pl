use strict;
our $home2 = "/home/sites/police/bots/forpost_ads";
our (%mods, %conf, $logs, @res_names);
$mods{pl_smart} = {
	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_smart_on_connect,
	on_disconnect	=> \&pl_smart_on_disconnect,
	on_idle		=> \&pl_smart_on_idle,
	on_data		=> \&pl_smart_on_data,
	on_root_cmd	=> \&pl_smart_on_root_cmd,
	on_boss_cmd	=> \&pl_smart_on_boss_cmd,

};

sub pl_smart_on_connect
{
	my $bot = shift;
	$bot->{pl_smart} = {} unless $bot->{pl_smart};
	$bot->{pl_smart_ses} = {};
}

sub pl_smart_on_disconnect
{
	my $bot = shift;
	delete $bot->{pl_smart_ses}
}

sub pl_smart_on_idle
{
	my $bot = shift;
	my $wait = time-($bot->{pl_smart}{lastscan}||0);
	my $wait2 = time-($bot->{pl_smart}{lastscan2}||0);
	my $wait3 = ($bot->{pl_smart}{lastscan3}||0);
	my $wait99 = time-($bot->{pl_smart}{lastscan99}||0);
	
	if ($bot->{pl_smart_ses}{busy})
		{
			client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_fab_hangs};
			return;
		}
	if ($wait99 > $conf{pl_onlines_rescan}) {
		$bot->{pl_smart}{lastscan99} = time;
		$bot->{pl_smart_ses}{busy} = 1;

		msg("$bot->{login}: Clan-list scan");
		client_out($bot, qq~<STATUS clan="1" />~);
		return;
	}
#	else {msg("$bot->{login}: Timeout is not out $wait5");}

}

sub pl_smart_on_data
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

			delete $bot->{pl_smart_ses}{busy};
			#msg("$bot->{login}  Police: End scan");
			}
		}
	return 0;
}

sub pl_smart_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

sub pl_smart_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

1;