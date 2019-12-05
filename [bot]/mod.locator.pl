use strict;
use Data::Dumper;



our (%mods, %conf, $logs, $db, $db2, %twistsended);
my $tmp = 'nodata';
my $utmp = "";
my %antiflood = ();
my $antifloodt = 10;
my %evtlist = ();
my $runtime = time();


$SIG{ALRM} = sub {msg("timeout!");};

$mods{locator} = {

	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&locator_on_connect,
	on_disconnect	=> \&locator_on_disconnect,
	on_idle		=> \&locator_on_idle,
	on_data		=> \&locator_on_data,
	on_root_cmd	=> \&locator_on_root_cmd,
	on_boss_cmd	=> \&locator_on_boss_cmd,

};

sub getTimeString {
	my $utime = shift;
	my $rtime = time()-$utime;
	my ($s,$i,$h,$d,$m,$y,$w)=localtime($utime);
	$m++;
	$y+=1900;
	$d = "0$d" if($d<10);
	$m = "0$m" if($m<10);
	$i = "0$i" if($i<10);
	$h = "0$h" if($h<10);
	$s = "0$s" if($s<10);
	my $timemsg .= "в $d.$m.$y $h:$i:$s";
return $timemsg;
}

sub locator_on_connect {
	my $bot = shift;

	client_out($bot, qq~<GOBLD n="$bot->{z}" />~, time);
	#msg("$bot->{login} locator_on_connect");
	$bot->{locator_ses} = {};
}

sub locator_on_disconnect {
	my $bot = shift;

	#msg("$bot->{login}  locator_on_disconnect");

	#delete $bot->{locator_ses};
}

#Ранге
sub getUserRank {
	my $uranks = shift;
	$uranks = $uranks ? $uranks : 0;
	my $i=0;
	my @ranks = ("0", "20", "60", "120", "250", "600", "1100", "1800", "2500", "3200", "4000", "5000", "6000", "7200", "10000", "15000", "30000", "1000000");

	foreach (@ranks) {
 		$i++;
		last if($uranks >= $ranks[$i-1] && $uranks < $ranks[$i]);
 	}
	return $i;
}

sub locator_on_idle {
	
	
	
	my $bot = shift;

	my $que = "";
	if($bot->{chat}) {
        #msg(qq~$bot->{'login'} chat:~);

		foreach my $data ($bot->{chat}{income}) {
			#msg(qq~Income: $data~);
			if ($data) {
			
				if($tmp ne $data) {
					$tmp = $data;
					my $xml = xml_parse_s($data);
					foreach my $key (@{$xml}) {
						if($key->{name} eq "A") {
							my ($id,$group,$status,$klan,$login,$level,$rangs,$hz,$faction)= split(/\//,$key->{data}{t});
							#msg(qq~$bot->{'login'} located: $id,$group,$status,$klan,$login,$level,$rangs \n~);

							#Убиваем копии ников
			            	if($utmp ne $login) {

								# на основании полученных данных заполняем таблицы
							
			                    # 1. Таблица локатора. 
								my $sth = $db->prepare("SELECT * FROM `locator` WHERE login='$login'");
								$sth->execute;
								my $u = $sth->fetchrow_hashref;

				                my $pro = (($status >> 5) & 63) || 0;
				                my $man = ($status & 8192)? 0:1;
				                my $userver = $bot->{server};
				                $userver = ($userver =~ m/(\d+)/i) ? $1 : 1;
				                my $location = "$bot->{x}/$bot->{y}";
				    			my $urank = getUserRank($rangs);
				    			my $uut = time();

								my $updatestring = "";
								$updatestring .= qq~`location`='$location',~ if($$u{location} ne $location);
								$updatestring .= qq~`server`='$userver',~ if($$u{server} ne $userver);
								$updatestring .= qq~`pro`='$pro',~ if($$u{pro} ne $pro);
								$updatestring .= qq~`lvl`='$level',~ if($$u{lvl} ne $level);
								$updatestring .= qq~`pvprank`='$urank',~ if($$u{pvprank} ne $urank);
								$updatestring .= qq~`pvpr`='$rangs',~ if($$u{pvpr} ne $rangs);
								$updatestring .= qq~`clan`='$klan',~ if($$u{clan} ne $klan);

								if($$u{login}) {
									$que = qq~UPDATE `locator` SET $updatestring `utime`='$uut' WHERE login='$login'~;
								} else {
									$que = qq~INSERT INTO locator VALUES
									(NULL, '$uut', '$uut', '$location','$userver', '$klan', '$login', '$level', '$pro', '$rangs', '$urank', '$man')~;
								}
								$sth = $db->do($que);
								#msg($que);

								
								# 2. Автовыписка из списка каторжан (отключена):
								if(1 == 2) {
								
									$sth = $db2->prepare("SELECT * FROM `prison_chars` WHERE `nick`='$login'");
									$sth->execute;
									my $z = $sth->fetchrow_hashref;
								
									if($$z{nick}) {		# красавчик в списках.. следующая проверка на то что его засек не каторжный бот 
									
										if($bot->{login} ne "terminal 20") {
											
											# запись в таблицу ручного удаления
											my $collected = $$z{collected};
											$que = qq~INSERT INTO prison_manualdelete SET nick = '$login', res = '$collected', date = '$uut'~;
											$sth = $db2->do($que);
											
											#
											$que = qq~DELETE FROM prison_chars WHERE nick = '$login' LIMIT 1~;
											$sth = $db2->do($que);
											
											#
											$que = qq~INSERT INTO prison_actions_log SET date='$uut', cop='<font color=red>-AUTO-</font>', `prisoner`='$login', `operation`='5'~;
											$sth = $db2->do($que);
											
											my $message = qq~ВНИМАНИЕ! На свободе обнаружен персонаж [$login], содержащийся в списках заключенных! Персонаж удален из списков. Подробности в логах каторги на сайте.~;
											$que = qq~INSERT bots_messages SET bot_id='wars', recp='Печальный Рыцарь', message='$message', send_after='0'~;
											$db->do($que);
										
										}			
									}
								}
								
								# 3. Работа с таблицей Истории персонажей.
								
								#  Заполняем данные Истории персонажа. Увы, дублирование неизбежно, пока не будет возможно отказаться от таблицы `locator` полностью.
								# События
								# 1 - занесен в базу
								# 2 - новый уровень
								# 3 - новое звание
								# 4 - новая профессия
								# 5 - новый клан
								# 6 - новая фракция
								# 7 - новое пве звание (пока недоступно)
								# 8 - смена пола
								#
								#
								
								my $events = "";
								$sth = $db->prepare("SELECT * FROM `history` WHERE login='$login' ORDER BY addtime DESC LIMIT 1");
								$sth->execute;
								my $h = $sth->fetchrow_hashref;

								# Если изменяются параметры персонажа - инсертим новую строку. Если только локация - апдейтим последнюю запись.
								if($$h{login}) {
									# определяем события
									$events .= "2" if($$h{new_lvl} ne $level);
									$events .= "3" if($$h{new_pvpr} ne $urank);
									$events .= "4" if($$h{new_pro} ne $pro);
									$events .= "5" if($$h{new_clan} ne $klan);
									$events .= "6" if($$h{new_faction} ne $faction);
									$events .= "8" if($$h{new_gender} ne $man);
									
									if (length($events) > 0) {
										$que = qq~INSERT INTO history VALUES
										(NULL, '$login', '$uut', '$uut', '$location','$userver', '$$h{new_clan}', '$klan', '$$h{new_lvl}', '$level', '$$h{new_pro}', '$pro', '$$h{new_pvpr}', '$urank', '$$h{new_faction}', '$faction', '$$h{new_gender}','$man', '$events')~;
									} else {
										my $hupdatestring = "";
										$hupdatestring .= qq~`location`='$location',~ if($$h{location} ne $location);
										$hupdatestring .= qq~`server`='$userver',~ if($$h{server} ne $userver);
										$que = qq~UPDATE `history` SET $hupdatestring `utime`='$uut' WHERE id='$$h{id}'~;
									}							
								} else {
									$que = qq~INSERT INTO history VALUES
									(NULL, '$login', '$uut', '$uut', '$location','$userver', '$klan', '$klan', '$level', '$level', '$pro', '$pro', '$urank', '$urank', '$faction', '$faction', '$man', '$man', '1')~;
								}
								$sth = $db->do($que);
								
								# 4. Автообновление клановых списков ЧС МП
								
								if(1 == 1) {
								
									$sth = $db->prepare("SELECT 
														 p.login AS login, 
														 p.clan AS old_clan,
														 p.comment AS old_comment,
														 p.summ AS pers_summ,
														 o.summ AS old_clan_summ
														 FROM `mp_black_list_persons` AS p 
														 LEFT JOIN `mp_black_list_clanes` AS o ON o.clan = p.clan 
														 WHERE `login`='$login'");
									$sth->execute;
									my $old = $sth->fetchrow_hashref;
									
									$sth = $db->prepare("SELECT clan, summ FROM `mp_black_list_clanes` WHERE `clan`='$klan'");
									$sth->execute;
									my $new = $sth->fetchrow_hashref;
									
									if($$old{login} && $$new{clan} && $$old{old_clan} ne $klan) { # перс в ЧС, новый клан в ЧС. Переводим перса в клановый чс 
									
										$que = qq~UPDATE `mp_black_list_persons` SET `clan`='$klan' WHERE login='$login'~;
										$sth = $db->do($que);
											
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
																					VALUES ($uut, '37038', '$login', '5', '', '$$new{clan}')~;	
										$sth = $db->do($que);
												
									} elsif ($$old{login} && !(defined $$new{clan}) && $$old{old_clan} ne "") { # перс в ЧС, новый клан НЕ в ЧС. Переводим перса в мародерский ЧС.
										
										# если у персонажа нет своей суммы, но его БЫВШИЙ клан в ЧС - берем сумму клана
										if ($$old{pers_summ} eq "" && $$old{old_clan_summ}) {
													
											$$old{pers_summ} = $$old{old_clan_summ};
												
										}
										
										# у клана не указана сумма.
										if ($$old{pers_summ} eq "") {
											$$old{pers_summ} = "10к";
										}
												
										my $updatestring = qq~, `summ`='$$old{pers_summ}'~;
										$updatestring .= qq~, `comment`='$$old{old_comment}, (а) членство в клане $$old{old_clan}'~;
													
										$que = qq~UPDATE `mp_black_list_persons` SET `clan`='' $updatestring WHERE login='$login'~;
										$sth = $db->do($que);
											
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
																					VALUES ($uut, '37038', '$login', '10', '$$old{pers_summ}', '$$old{old_clan}')~;
										$sth = $db->do($que);
									
									
									} elsif (!(defined $$old{login}) && $$new{clan}) { # перса в ЧС нет, новый клан в ЧС. Добавляем перса в клановый ЧС.
									
										$que = qq~INSERT INTO mp_black_list_persons(`login`, `summ`, `comment`, `logs`, `clan`, `special`)
														  VALUES ('$login', '$$new{summ}', '(а) клан к отстрелу: $klan', '', '$klan', '0')~;

										$sth = $db->do($que);
												
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
														  VALUES ($uut, '37038', '$login', '7', '', '$$new{clan}')~;	

										$sth = $db->do($que);
									
									} else {
										# ничего не делаем
									}

								}		
							}
							#Убиваем копии ников
							$utmp = $login;
						}
					}
				}
			}
		}
	}
return 0;
}

sub locator_on_data {
	my ($bot, $key, $data) = @_;

	#msg(qq~$bot->{'login'} $key -> $data~);

	return 0;
}

sub locator_on_root_cmd
{
	my ($bot, $cmd, $arg, $from) = @_;

	#msg("$bot->{login}  locator_on_root_cmd");
	#chat_msg($bot, "private [$from] root $cmd [$arg] on module locator");
	return 0;
}

sub locator_on_boss_cmd {
	my ($bot, $cmd, $arg, $from) = @_;

return 1;
}

1;
