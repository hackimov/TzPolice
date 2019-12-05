use strict;
#use Text::ParseWords;

our (%mods, %conf, $logs, $db);

$mods{wars} = {

	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&wars_on_connect,
	on_disconnect	=> \&wars_on_disconnect,
	on_idle		=> \&wars_on_idle,
	on_data		=> \&wars_on_data,
	on_root_cmd	=> \&wars_on_root_cmd,
	on_boss_cmd	=> \&wars_on_boss_cmd,

};

sub wars_on_connect
{
	my $bot = shift;

	$bot->{wars} = {} unless $bot->{wars};
	$bot->{wars}{lastscan} = 0;
}

sub wars_on_disconnect
{
	my $bot = shift;

}

sub wars_on_idle
{
	my $bot = shift;

	my $wait = time-($bot->{wars}{lastscan});

	if ($wait > $conf{pl_wars_scan}) {
		 
		$bot->{wars}{lastscan} = time;

		# отправляем системки 
		my $bid = $bot->{id};
		my $now = mktime(localtime(time));
		msg(qq~SELECT * FROM bots_messages WHERE bot_id="$bid" AND send_after<$now~);
		my $sth = $db->prepare(qq~SELECT * FROM bots_messages WHERE bot_id="$bid" AND send_after<$now~);
		$sth->execute;

		while (my $row = $sth->fetchrow_hashref) {
			
			my $message_id = $row->{id};
			my $recp = $row->{recp};
			my $mess = $row->{message};
			
			client_out($bot, qq~<PDA id="66233138991.1" p1="$recp" p2="$mess" />~);
			
			$db->do(qq~DELETE FROM bots_messages WHERE id="$message_id"~);
			
		}
		
		#  сканируем вары
		msg("$bot->{login}  Wars: Begin scan");

		client_out($bot, qq~<NOF clan_list="3" />~);

		return;
	}

}

sub wars_on_data
{
	my ($bot, $key, $data) = @_;

	if ($key->{name} eq "NOF") {

		if (defined $key->{data}{clan_list}) {
				
			#my @clans = quotewords(",", 0, $key->{data}{clan_list});

			my $fname = "$logs/wars-".strftime("%Y%m%d%H%M%S", localtime);
			open my ($file), ">$fname.tmp";
			print $file "$key->{data}{clan_list}\n$key->{data}{alliance_list}";
			close $file;
			rename "$fname.tmp", "$fname.txt";

			return 1;
		}
	}

	return 0;
}

sub wars_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_root_cmd");

	return 0;
}

sub wars_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_boss_cmd");

	return 0;
}

1;
