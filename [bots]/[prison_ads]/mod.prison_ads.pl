use strict;
our $home2 = "/home/sites/police/bots/prison_ads";
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
our @alerts;
our $alertnum = 23;
our $maxalert = 22;
sub load_alerts
	{
		open (BASE, "<$home2/alerts.txt");
		my @base = <BASE>;
		close (BASE);
		$maxalert=$#base;
#msg($maxalert);
#		unlink("$home2/alerts.txt");
#		touch("$home2/alerts.txt");
#		my $q = 0666;
#		my $w = "$home2/alerts.txt";
#		chmod($q, $w);
		for (my $i=$maxalert; $i>=0; $i--)
			{
				(my $num, my $msg)=split (/\|\|/, $base [$i]);
				$alerts[$num]=$msg;
			}
	}
	
load_alerts();
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
	if ($wait2 > $conf{pl_fab_rescan_2})
		{
				$bot->{pl_fab}{lastscan2} = time;
				$bot->{pl_fab_ses}{busy} = 1;
#				%alerts = -1;
#				load_alerts();
#				foreach my $person (keys %alerts)
#					{
#						if($person != "-1"){
#				client_out($bot, qq~<PDA p2="$alerts{$person}" p1="$person" id="9033393199.1" />~);
#						}
#					}
#				msg($alertnum);
				chat_msg($bot, $alerts[$alertnum]);
#				msg($bot, $alerts[$alertnum]);
				$alertnum++;
				if ($alertnum > $maxalert) {$alertnum = 0;}
#				msg($alertnum);
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
			#msg("$bot->{login}  Police: End scan");
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