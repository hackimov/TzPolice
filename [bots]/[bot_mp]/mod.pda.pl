use strict;
require "/home/sites/police/dbconn/db.pl";
our (%mods, %conf, $logs, @res_names, $home, $db);
my $pdarescan = 30;
my $prefix = 'deorg_';
$mods{pda} = {
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
	my $bot = shift;
	$bot->{pdalist} = ();
    #фикс
    $db->do(qq~DELETE FROM ~.$prefix.qq~pda_users_stat WHERE instime+1 = lastupdate OR instime = lastupdate~);

	my $query = $db->prepare(qq~SELECT * FROM ~.$prefix.qq~pda_users~);
	$query->execute();
	while(my $u = $query->fetchrow_hashref) {
		push(@{$bot->{pdalist}},$$u{login});
	}
	my $pdalist = join(',',@{$bot->{pdalist}});

	client_out($bot, qq~<CHANGE param="list" txt="$pdalist" />~);
    #msg(qq~$bot->{login}: <CHANGE param="list" txt="$pdalist" />~);
}

sub set_user_stat {
	my ($login,$online,$server,$city,$chat) = @_;

	my $time = time();
	#msg(qq~SELECT * FROM ~.$prefix.qq~pda_users_stat WHERE login = '$login' ORDER BY id DESC LIMIT 1~);
	my $query = $db->prepare(qq~SELECT * FROM ~.$prefix.qq~pda_users_stat WHERE login = '$login' ORDER BY id DESC LIMIT 1~);
	$query->execute();
	my $user = $query->fetchrow_hashref();
    #новая запись?
	my $newStroke = 0;

    #в базе нихрена не найдено
    if(!$$user{login} || $$user{gooff} > 0) {
    	#юзер онлайн? начинаем наблюдение. нет? забиваем
    	if($online > 0) {
    		$newStroke++;
		}
    } else {
    	my $update = qq~lastupdate = '$time'~;
        #статус
        if($online < 1) {
        	$update .= qq~, gooff = '1'~;
        }
        #чат
        if($$user{chat} != $chat) {
        	$newStroke++;
		}
        #купол
		if($$user{city} != $city) {
			$newStroke++;
		}
		#сервер
		if($$user{server} != $server) {
			$newStroke++;
		}
		$db->do(qq~UPDATE ~.$prefix.qq~pda_users_stat SET $update WHERE id = '$$user{id}'~);
    }
	if($newStroke > 0) {
		$db->do(qq~INSERT INTO ~.$prefix.qq~pda_users_stat (login,status,city,chat,server,instime,lastupdate) VALUES('$login','$online','$city','$chat','$server','$time','$time')~);
	}

}

sub pda_on_connect {
    my $bot = shift;
    $bot->{pda}{lastscan} = 0;
    $bot->{pda}{lastgetlist} = time();

    &get_pda_list($bot);
}

sub pda_on_disconnect {
    my $bot = shift;
    $bot->{pda} = {};
}

sub pda_on_idle {
	my $bot = shift;

	my $wait = time-($bot->{pda}{lastscan}||0);
	if ($bot->{pda_ses}{busy})	{
		#msg(qq~busy is $bot->{pda_ses}{busy}~);
		return;
	}
	#апдейт списка кпк
	if ($bot->{pda}{lastgetlist} < time-600)	{
		&get_pda_list($bot);
		$bot->{pda}{lastgetlist} = time();
	}
	if ($wait > $pdarescan) {
		$bot->{pda}{lastscan} = time;
		$bot->{pda_ses}{busy} = 1;
		#msg("$bot->{login}  PDA: Begin scan");
		client_out($bot, qq~<STATUS list="1" />~);
		return;
	}
}
#o = другой сервер, n = оффнут, y = онлайн |  y:X/Y/SERV/CHAT
sub pda_on_data  {
	my ($bot, $key, $data) = @_;
	if ($key->{name} eq "STATUS") {
		if (defined $key->{data}{s}) {
			my $fname = "$home/pda/pda-".strftime("%Y%m%d%H%M%S", localtime)."-".$bot->{server};
			my ($list,$i) = ('',0);
			my @pdascan = split(',',$key->{data}{s});

			foreach my $login (@{$bot->{pdalist}}) {
				my @uinfo = split(':',$pdascan[$i]);
				$i++;
				next if($uinfo[0] ne 'n' && $uinfo[0] ne 'y');

				my($x,$y,$server,$status) = split('/',$uinfo[1]);

				my($online,$city,$chat) = (0,0,0,0);

				if($chat > -1 && $server > 0) {
					$online = 1 if($uinfo[0] eq 'y');
					$city = 1 if($x ne '' && $y ne '');
					$chat = 1 if($status < 2);
                }
                &set_user_stat($login,$online,$server,$city,$chat);
                #<STATUS s="y:135/40/2/0"/> онлайн, купол, чат он, не в бою
                #<STATUS s="y:135/40/2/2"/> онлайн, купол, чат офф, не в бою
                #<STATUS s="y://2/3"/> онлайн, вне купола, чат офф, в бою
                #<STATUS s="y://2/1"/> онлайн, вне купола, чат он, в бою
                #<STATUS s="y:135/40/2/1"/> онлайн, купол, чат он, в бою
                #<STATUS s="y:135/40/2/3"/> онлайн, купол, чат ооф, в бою



				#$list .= qq~$login:$online/$server/$city/$chat [$pdascan[$i]],~;
			}
            #msg(qq~$bot->{login} -> $list~);

			delete $bot->{pda_ses}{busy};
		}

	}

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