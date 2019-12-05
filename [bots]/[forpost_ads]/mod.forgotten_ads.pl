use strict;
our $home2 = "/home/sites/police/bots/forpost_ads";
our (%mods, %conf, $logs, @res_names);
$mods{pl_forg} = {
	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_forg_on_connect,
	on_disconnect	=> \&pl_forg_on_disconnect,
	on_idle		=> \&pl_forg_on_idle,
	on_data		=> \&pl_forg_on_data,
	on_root_cmd	=> \&pl_forg_on_root_cmd,
	on_boss_cmd	=> \&pl_forg_on_boss_cmd,

};
our @alerts2;
our $alertnum2 = 2;
our $maxalert2 = 3;
sub load_alerts2
	{
		open (BASE2, "<$home2/alerts_forg.txt");
		my @base2 = <BASE2>;
		close (BASE2);
		$maxalert2=$#base2;
		for (my $i=$maxalert2; $i>=0; $i--)
			{
				(my $num, my $msg)=split (/\|\|/, $base2[$i]);
				$alerts2[$num]=$msg;
				msg("LOAD MESSAGE FORGOTTEN");
			}
	}

load_alerts2();
sub pl_forg_on_connect
{
	my $bot2 = shift;
	$bot2->{pl_forg} = {} unless $bot2->{pl_forg};
	$bot2->{pl_forg_ses} = {};
}

sub pl_forg_on_disconnect
{
	my $bot2 = shift;
	delete $bot2->{pl_forg_ses}
}

sub pl_forg_on_idle
{
	my $bot2 = shift;
	my $wait = time-($bot2->{pl_forg}{lastscan}||0);
	if ($wait > $conf{pl_forg_rescan})
		{
			msg("$bot2->{login}: SEND MESSAGE TO CHAT $alertnum2");
			$bot2->{pl_forg}{lastscan} = time;
			$bot2->{pl_forg_ses}{busy} = 1;
			chat_msg($bot2, $alerts2[$alertnum2]);
			$alertnum2++;
			if ($alertnum2 > $maxalert2) {$alertnum2 = 0;}
			$bot2->{pl_forg_ses}{busy} = 0;
			return;
		}
}
sub pl_forg_on_data
{
	my ($bot2, $key, $data) = @_;
	return 0;
}

sub pl_forg_on_root_cmd
{
	my ($bot2, $cmd, $arg) = @_;
	return 0;
}

sub pl_forg_on_boss_cmd
{
	my ($bot2, $cmd, $arg) = @_;
	return 0;
}

1;