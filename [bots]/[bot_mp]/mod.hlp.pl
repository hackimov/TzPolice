use strict;
our (%mods, %conf, $logs, @res_names, $home, $db);

$mods{hlp} = {

	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pda_on_connect,
	on_disconnect	=> \&pda_on_disconnect,
	on_idle		=> \&pda_on_idle,
	on_data		=> \&pda_on_data,
	on_root_cmd	=> \&pda_on_root_cmd,
	on_boss_cmd	=> \&pda_on_boss_cmd,

};

sub get_pda_list {

}

sub set_user_stat {
	

}

sub pda_on_connect {

    my $bot = shift;
    
	if ($bot->{id} eq "coprock") {
		$bot->{mp}{lastchat} = time - $conf{pl_help} + 10;
		msg("$bot->{id}  $bot->{mp}{lastchat}");
	}
	
	if ($bot->{id} eq "copwind") {
		$bot->{mp}{lastchat} = time - $conf{pl_help}/2 + 10;
		msg("$bot->{id}  $bot->{mp}{lastchat}");
	}
	
}

sub pda_on_disconnect {

    my $bot = shift;
    $bot->{mp} = {};
}

sub pda_on_idle {

	my $bot = shift;
	my $wait = time-($bot->{mp}{lastchat}||0);
	
	if ($wait > $conf{pl_help}) {
		$bot->{mp}{lastchat} = time;
		chat_msg($bot,"Напали ? :naem:  Пиши мне!  SOS и номер комнаты шахты!  :cop:");
		return;
	}
	
}

#o = другой сервер, n = оффнут, y = онлайн |  y:X/Y/SERV/CHAT
sub pda_on_data  {
	
	return 0;
}

sub pda_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

sub pda_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;
	return 0;
}

1;