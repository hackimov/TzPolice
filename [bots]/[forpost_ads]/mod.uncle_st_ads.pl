use strict;

our $home3 = "/home/sites/police/bots/forpost_ads";
our (%mods, %conf, $logs, @res_names);

$mods{pl_uncle_st} = {
	
	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_uncle_st_connect,
	on_disconnect	=> \&pl_uncle_st_disconnect,
	on_idle		=> \&pl_uncle_st_idle,
	on_data		=> \&pl_uncle_st_data,
	on_root_cmd	=> \&pl_uncle_st_root_cmd,
	on_boss_cmd	=> \&pl_uncle_st_boss_cmd,

};

our @alerts3;
our $alertnum3 = 1;
our $maxalert3 = 83;

sub load_alerts3
	{
		open (BASE3, "<$home3/alerts_uncle_st.txt");
		my @base3 = <BASE3>;
		close (BASE3);
		$maxalert3=$#base3;
		for (my $i=$maxalert3; $i>=0; $i--)
			{
				(my $num, my $msg)=split (/\|\|/, $base3[$i]);
				$alerts3[$num]=$msg;
				msg("LOAD MESSAGE UNCLE STEPA");
			}
	}

load_alerts3();

sub pl_uncle_st_connect
{
	my $bot3 = shift;
	$bot3->{pl_uncle_st} = {} unless $bot3->{pl_uncle_st};
	$bot3->{pl_uncle_st_ses} = {};
}

sub pl_uncle_st_disconnect
{
	my $bot3 = shift;
	delete $bot3->{pl_uncle_st_ses}
}

sub pl_uncle_st_idle
{
	my $bot3 = shift;
	my $wait = time-($bot3->{pl_uncle_st}{lastscan}||0);
	if ($wait > $conf{pl_uncle_st_rescan})
		{
			msg("$bot3->{login}: SEND MESSAGE TO CHAT $alertnum3");
			$bot3->{pl_uncle_st}{lastscan} = time;
			$bot3->{pl_uncle_st_ses}{busy} = 1;
			chat_msg($bot3, $alerts3[$alertnum3]);
			$alertnum3++;
			if ($alertnum3 > $maxalert3) {$alertnum3 = 0;}
			$bot3->{pl_uncle_st_ses}{busy} = 0;
			return;
		}
}
sub pl_uncle_st_data
{
	my ($bot3, $key, $data) = @_;
	return 0;
}

sub pl_uncle_st_root_cmd
{
	my ($bot3, $cmd, $arg) = @_;
	return 0;
}

sub pl_uncle_st_boss_cmd
{
	my ($bot3, $cmd, $arg) = @_;
	return 0;
}

1;