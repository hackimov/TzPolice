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
	my $timemsg .= "� $d.$m.$y $h:$i:$s";
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

#�����
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

							#������� ����� �����
			            	if($utmp ne $login) {

								# �� ��������� ���������� ������ ��������� �������
							
			                    # 1. ������� ��������. 
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

								
								# 2. ����������� �� ������ �������� (���������):
								if(1 == 2) {
								
									$sth = $db2->prepare("SELECT * FROM `prison_chars` WHERE `nick`='$login'");
									$sth->execute;
									my $z = $sth->fetchrow_hashref;
								
									if($$z{nick}) {		# ��������� � �������.. ��������� �������� �� �� ��� ��� ����� �� ��������� ��� 
									
										if($bot->{login} ne "terminal 20") {
											
											# ������ � ������� ������� ��������
											my $collected = $$z{collected};
											$que = qq~INSERT INTO prison_manualdelete SET nick = '$login', res = '$collected', date = '$uut'~;
											$sth = $db2->do($que);
											
											#
											$que = qq~DELETE FROM prison_chars WHERE nick = '$login' LIMIT 1~;
											$sth = $db2->do($que);
											
											#
											$que = qq~INSERT INTO prison_actions_log SET date='$uut', cop='<font color=red>-AUTO-</font>', `prisoner`='$login', `operation`='5'~;
											$sth = $db2->do($que);
											
											my $message = qq~��������! �� ������� ��������� �������� [$login], ������������ � ������� �����������! �������� ������ �� �������. ����������� � ����� ������� �� �����.~;
											$que = qq~INSERT bots_messages SET bot_id='wars', recp='��������� ������', message='$message', send_after='0'~;
											$db->do($que);
										
										}			
									}
								}
								
								# 3. ������ � �������� ������� ����������.
								
								#  ��������� ������ ������� ���������. ���, ������������ ���������, ���� �� ����� �������� ���������� �� ������� `locator` ���������.
								# �������
								# 1 - ������� � ����
								# 2 - ����� �������
								# 3 - ����� ������
								# 4 - ����� ���������
								# 5 - ����� ����
								# 6 - ����� �������
								# 7 - ����� ��� ������ (���� ����������)
								# 8 - ����� ����
								#
								#
								
								my $events = "";
								$sth = $db->prepare("SELECT * FROM `history` WHERE login='$login' ORDER BY addtime DESC LIMIT 1");
								$sth->execute;
								my $h = $sth->fetchrow_hashref;

								# ���� ���������� ��������� ��������� - �������� ����� ������. ���� ������ ������� - �������� ��������� ������.
								if($$h{login}) {
									# ���������� �������
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
								
								# 4. �������������� �������� ������� �� ��
								
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
									
									if($$old{login} && $$new{clan} && $$old{old_clan} ne $klan) { # ���� � ��, ����� ���� � ��. ��������� ����� � �������� �� 
									
										$que = qq~UPDATE `mp_black_list_persons` SET `clan`='$klan' WHERE login='$login'~;
										$sth = $db->do($que);
											
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
																					VALUES ($uut, '37038', '$login', '5', '', '$$new{clan}')~;	
										$sth = $db->do($que);
												
									} elsif ($$old{login} && !(defined $$new{clan}) && $$old{old_clan} ne "") { # ���� � ��, ����� ���� �� � ��. ��������� ����� � ����������� ��.
										
										# ���� � ��������� ��� ����� �����, �� ��� ������ ���� � �� - ����� ����� �����
										if ($$old{pers_summ} eq "" && $$old{old_clan_summ}) {
													
											$$old{pers_summ} = $$old{old_clan_summ};
												
										}
										
										# � ����� �� ������� �����.
										if ($$old{pers_summ} eq "") {
											$$old{pers_summ} = "10�";
										}
												
										my $updatestring = qq~, `summ`='$$old{pers_summ}'~;
										$updatestring .= qq~, `comment`='$$old{old_comment}, (�) �������� � ����� $$old{old_clan}'~;
													
										$que = qq~UPDATE `mp_black_list_persons` SET `clan`='' $updatestring WHERE login='$login'~;
										$sth = $db->do($que);
											
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
																					VALUES ($uut, '37038', '$login', '10', '$$old{pers_summ}', '$$old{old_clan}')~;
										$sth = $db->do($que);
									
									
									} elsif (!(defined $$old{login}) && $$new{clan}) { # ����� � �� ���, ����� ���� � ��. ��������� ����� � �������� ��.
									
										$que = qq~INSERT INTO mp_black_list_persons(`login`, `summ`, `comment`, `logs`, `clan`, `special`)
														  VALUES ('$login', '$$new{summ}', '(�) ���� � ��������: $klan', '', '$klan', '0')~;

										$sth = $db->do($que);
												
										$que = qq~INSERT INTO mp_black_list_journal(`date`, `user_id`, `bl_nick`, `act`, `before_val`, `after_val`)
														  VALUES ($uut, '37038', '$login', '7', '', '$$new{clan}')~;	

										$sth = $db->do($que);
									
									} else {
										# ������ �� ������
									}

								}		
							}
							#������� ����� �����
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
