#!/usr/bin/perl -w

use strict;
use Text::ParseWords;
use POSIX;
use DBI;

setlocale LC_CTYPE, "ru_RU.CP1251";
use locale;



our $home = ".";
our $logs = ".";

our $data = ".";

#=begin

$home = "/home/sites/police/bot";
$logs = "$home/logs";
$data = "$home/data";

open STDERR, ">>$home/tzmaint.log";
open STDOUT, ">&STDERR";

select STDERR; $| = 1;
select STDOUT; $| = 1;

#=cut

require "$home/tzbot.cfg.pl";
require "$home/tzbot.lib.pl";

our %conf;

#msg("TZBOT maint started");

our $dbh = DBI->connect("DBI:mysql:database=$conf{sql_dbase};host=$conf{sql_host}", $conf{sql_login}, $conf{sql_pass}, { RaiseError => 1 });

$dbh->do(qq~SET NAMES "cp1251"~);

# хз зачем сохранять логи чата чтобы потом поудалять... так было.
foreach (sort <$logs/chat-*-????????.txt>) {
	unlink $_ if (-M $_) > 5;
}

# обрабатываем логи завода и лабы на карторге, заполням данные по сданным ресурсам.
foreach (sort <$logs/fab-?abzon-??????????????.txt>) {
	m#^\Q$logs\E/(.*\.txt)$#;
	my $fname = $1;
	msg(qq~get file: $fname ~);
	
	if (open my $file, "$logs/$fname") {
		my ($p_day, $line_no) = (0, 0);
		my $bid = "nobody";
		# определяем значение id бота из названия файла
		if ($fname =~ /^fab-(.+)-\d+\.txt$/) {
			$bid = $1;
		}

		foreach (<$file>) {
			if (/^(\d+)\.(\d+)\.(\d+) (\d+):(\d+)\t(.+?)\t(.*)(\r?\n)?$/) {
				my ($day, $mon, $year, $hour, $min, $event, $data) = ($1, $2, $3, $4, $5, $6, $7);

				next if $event == 3105;

				my $date = "20$year-$mon-$day";
				my $time = "$hour:$min:00";

				$data =~ s/\t/|/gs;
				
				# отсекаем тех. персов.
				if (index($data,"Конфискация|") == 0) {
					next;
				}
				# -------------------------------------

				# Для каждого нового дня лога заного инициализируем номер строки
				if ($day != $p_day) {
					$line_no = 1;
					$p_day = $day;
				}

				unless ($line_no == 1 && int($hour)>22) {

					my $sth = $dbh->prepare(qq~SELECT * FROM bot_prison_logs WHERE log_date="$date" AND line_no="$line_no" AND bot_id="$bid"~);
					$sth->execute;
					my $row = $sth->fetchrow_hashref;
					$sth->finish;

					my $process;

					unless ($row) {
						$process = 1;

						$dbh->do(qq~INSERT bot_prison_logs SET log_date="$date", log_time="$time", line_no="$line_no", event="$event", data="$data", bot_id="$bid"~);

					} elsif (($row->{log_time} ne $time) || ($row->{event} ne $event) || ($row->{data} ne $data)) {
						$process = 1;

						msg("Warning! Rollback detected: bot_prison_logs $date $line_no");

						$sth = $dbh->prepare(qq~SELECT * FROM bot_prison_logs WHERE id>="$row->{id}"~);
						$sth->execute;

						while ($row = $sth->fetchrow_hashref) {
							if ($row->{event} == 12200 || $row->{event} == 10200) {
								if ($row->{data} =~ /^(.+)\|.*\[(\d+)\]$/) {
									my ($login, $count) = ($1, $2);
									# вычитаем из рейтинга и зачетного кол-ва сданых ресурсов то, что попало в откат.
									$dbh->do(qq~UPDATE prison_chars SET collected=collected-"$count" WHERE nick="$login"~);
									$dbh->do(qq~UPDATE prison_rating SET collected=collected-"$count" WHERE nick="$login" AND date="$row->{log_date}"~);
									msg(qq~UPDATE prison_rating SET collected=collected-"$count" WHERE nick="$login" AND date="$row->{log_date}"~);
								}
							}

							$dbh->do(qq~DELETE FROM bot_prison_logs WHERE id="$row->{id}"~);
						}

						$sth->finish;
					}

					if ($process) {
						if ($event == 12200 || $event == 10200) {
							if ($data =~ /^(.+)\|.*\[(\d+)\]$/) {
								my ($login, $count) = ($1, $2);

								if ($dbh->do(qq~UPDATE prison_chars SET last_pay="$^T", collected=collected+"$count" WHERE nick="$login"~) < 1) {
									$dbh->do(qq~INSERT prison_chars SET nick="$login", term=-1, collected="$count", last_pay="$^T", reason="-1", remark="Добавлен автоматически", dept=-1, add_date="~.strftime("%Y-%m-%d", localtime $^T).qq~", add_by="Автомат"~);
									msg(qq~INSERT prison_chars SET nick="$login", term=-1, collected="$count", last_pay="$^T", reason="-1", remark="Добавлен автоматически", dept=-1, add_date="~.strftime("%Y-%m-%d", localtime $^T).qq~", add_by="Автомат", bot_id="$bid"~);
								} else {
									msg(qq~UPDATE prison_chars SET last_pay="$^T", collected=collected+"$count" WHERE nick="$login", bot_id="$bid"~);
								}

								if ($dbh->do(qq~UPDATE prison_rating SET collected=collected+"$count" WHERE nick="$login" AND date="$date"~) < 1) {
									$dbh->do(qq~INSERT prison_rating SET nick="$login", collected="$count", date="$date"~);
									msg(qq~INSERT prison_rating SET nick="$login", collected="$count", date="$date" bot_id="$bid"~);
								} else {
									msg(qq~UPDATE prison_rating SET collected=collected+"$count" WHERE nick="$login" AND date="$date" , bot_id="$bid"~);
								}

							}
						}
					}

					$line_no++;
				}
			}
		}

		close $file;

		unlink "$logs/$fname";
	}
}


#msg("TZBOT maint done");

$dbh->disconnect();
$dbh = DBI->connect("DBI:mysql:database=$conf{sql_dbase_hist};host=$conf{sql_host}", $conf{sql_login_hist}, $conf{sql_pass_hist}, { RaiseError => 1 });
$dbh->do(qq~SET NAMES "cp1251"~);

#  получаем на вход id записи из истории, извлекаем, в зависимости от данных формируем сообщения.
sub reg_messages {
	
	my $id = shift;
	my $sth = $dbh->prepare(qq~SELECT * FROM wars_history WHERE id="$id"~);
	$sth->execute;
	my $mainrow = $sth->fetchrow_hashref;
	$sth->finish;
	
	my $event = 'finish';
	
	if ($mainrow->{finish} == 0) {
		$event = 'start';
	}
	
	# обработка "левого" клана/альянса
	
	my $cl = join('\', \'', split(/,/, $mainrow->{members1}));
	
	$sth = $dbh->prepare(qq~SELECT s.user_name, w.m_declaration, w.m_start, w.m_finish FROM wars_subscribe AS w INNER JOIN site_users AS s ON(s.id = w.user_id) INNER JOIN locator AS l ON(s.user_name = l.login) WHERE l.clan IN ('$cl')~);
	$sth->execute;
	
	my $text1 = '';
	my $text2 = '';
	
	while (my $row = $sth->fetchrow_hashref) {
		
		if ('*'.$mainrow->{members1} eq $mainrow->{all1}) {
			$text1 = '';
		} else {
			$text1 = qq~в составе альянса $mainrow->{all1},~;
		}

		if ('*'.$mainrow->{members2} eq $mainrow->{all2}) {
			$text2 = qq~ кланом $mainrow->{members2}~;
		} else {
			$text2 = qq~ альянсом $mainrow->{all2} ($mainrow->{members2})~;
		}
		
		if ($event eq 'start') {
			
			if ($row->{m_declaration} eq '1') {
				
				my $message = qq~ВНИМАНИЕ! Зарегистрировано объявление войны между вашим кланом $text1 и $text2 . Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
			if ($row->{m_start} eq '1') {

				my $message = qq~ВНИМАНИЕ! Война с $text2 вступила в активную фазу! С этого момента в боях вы получаете и отдаете варпоинты и рейтинг! Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))+86100;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
		}
		
		if ($event eq 'finish') {
		
			if ($row->{m_finish} eq '1') {
			
				my $message = qq~ВНИМАНИЕ! Зарегистрировано окончание войны с $text2 . Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
		}
	}
	
	$sth->finish;
	
	# обработка "правого" клана/альянса
	
	$cl = join('\', \'', split(/,/, $mainrow->{members2}));
	
	$sth = $dbh->prepare(qq~SELECT s.user_name, w.m_declaration, w.m_start, w.m_finish FROM wars_subscribe AS w INNER JOIN site_users AS s ON(s.id = w.user_id) INNER JOIN locator AS l ON(s.user_name = l.login) WHERE l.clan IN ('$cl')~);
	$sth->execute;
	
	$text1 = '';
	$text2 = '';
	
	while (my $row = $sth->fetchrow_hashref) {
		
		if ('*'.$mainrow->{members2} eq $mainrow->{all2}) {
			$text1 = '';
		} else {
			$text1 = qq~в составе альянса $mainrow->{all2},~;
		}

		if ('*'.$mainrow->{members1} eq $mainrow->{all1}) {
			$text2 = qq~ кланом $mainrow->{members1}~;
		} else {
			$text2 = qq~ альянсом $mainrow->{all1} ($mainrow->{members1})~;
		}
		
		if ($event eq 'start') {
			
			if ($row->{m_declaration} eq '1') {
				
				my $message = qq~ВНИМАНИЕ! Зарегистрировано объявление войны между вашим кланом $text1 и $text2 . Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
			if ($row->{m_start} eq '1') {

				my $message = qq~ВНИМАНИЕ! Война с $text2 вступила в активную фазу! С этого момента в боях вы получаете и отдаете варпоинты и рейтинг! Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))+86100;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
		}
		
		if ($event eq 'finish') {
		
			if ($row->{m_finish} eq '1') {
			
				my $message = qq~ВНИМАНИЕ! Зарегистрировано окончание войны с $text2 . Предлагаем посетить наш сервис КЛАНОВЫХ ВОЙН по адресу http://tzpolice.ru/?act=offwars, обновить списки врагов для КПК и узнать общее состояние политической арены ТО! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
		}
	}
	
	$sth->finish;
	
}

#reg_messages('233');

our %clans = ();			# хеш воюющих кланов, для расклада багнутых записей и поиска существующих кланов 
our @reset_clans = ();		# массив кланов, в котором будут собираться кланы для замены багнутой записи
# обрабатываем политическую карту, заполяем данные по текущим и закончившимся клан-варам.

foreach (sort <$logs/wars-??????????????.txt>) {
	
	m#^\Q$logs\E/(.*\.txt)$#;
	my $fname = $1;
	msg(qq~get file: $fname ~);
	
	if (open my $file, "$logs/$fname") {

		# определяем дату из имени файла
		$fname =~ /^wars-(\d\d\d\d)(\d\d)(\d\d)(\d\d)(\d\d)(\d\d).txt$/;
		my ($day, $mon, $year, $hour, $min, $sec) = ($3, $2, $1, $4, $5, $6);
		$year = sprintf("%02d", $year % 100);
		my $date = mktime $sec, $min, $hour, $day, $mon-1, $year+100;
		
		my $clan_list = <$file>;
		my $all_list = <$file>;

		# разбираем входящие данные на запчасти
		
		chomp($clan_list);
		chomp($all_list);

		my @cl = quotewords(",", 0, $clan_list);
		my @al = quotewords(",", 0, $all_list);

		my %all_table = ();		

		my @info = ();			# массив записи о клане. Имя клана, ID аляьнса, список варов.
		my @wars = ();			# массив кланов-противников

		my $record = "";		# запись о клане

		foreach $record (@cl) {
			@info = quotewords(":", 0, $record);
			
			
			if (defined $info[5]) {
				
				my $all_name = ($info[1] ne "" ? $al[$info[1]] : "*$info[0]");
				$clans{$info[0]} = $all_name; 

				# по ходу перебора группируем кланы в альянсы.
				
				my @members = ();

				if(exists($all_table{$all_name})) {
					@members = @{$all_table{$all_name}->{members}};
				}

				push @members, $info[0];

				@wars = quotewords("/", 0, $info[5]);
				$all_table{$all_name} = { name => $all_name, members => [@members], wars => [@wars] };
			}
		}

		# лечим баг с отсутствием разделителя. 
		foreach my $value(values %all_table) {
			
			# перебираем список кланов-противников каждого клана
			@wars = @{$value->{wars}};
			
			my $wr = 0;

			while ( $wr <= $#wars ) {
				# проверяем существование кланов-противников в хеше воюющих кланов
				if(!exists($clans{$wars[$wr]})) {
					@reset_clans = ();
					my $result = kaboom($wars[$wr]);
					splice (@wars, $wr, 1, @reset_clans);
				}
				$wr++;
			}

			$value->{wars} = [@wars];
		}
		
		# разбиваем кланы-противники на альянсы
		foreach my $value(values %all_table) {
			
			# перебираем список кланов-противников каждого клана
			@wars = @{$value->{wars}};
			my %new_wars = (); # хеш с кланами, разбитыми на альянсы.
			
			foreach my $wclan (@wars) {
				
				my $all_name = $clans{$wclan};
				my @tempwar = ();

				if(exists($new_wars{$all_name})) {
					@tempwar = @{$new_wars{$all_name}};
				}
				
				push @tempwar, $wclan;
				$new_wars{$all_name} = [@tempwar];

			}

			$value->{wars} = {%new_wars};
		}

		# проверяем на окончившиеся вары.
		
		my $sth = $dbh->prepare(qq~SELECT * FROM wars_history WHERE finish="0"~);
		$sth->execute;

		while (my $row = $sth->fetchrow_hashref) {
			
			my $all1 = $row->{all1};
			my $all2 = $row->{all2};
			
			# проверяем существование альянса 1.
			if(exists($all_table{$all1})) {
				# если существует - проверяем существование альянса 2.
				# тут правда есть момент.. если за 1 час окончится вар, произойдет смена кланов альянса, и вар снова начнется..
				my %wars = %{$all_table{$all1}->{wars}};
				unless (exists($wars{$all2})) {
					# такого вара в текущих нет. закрываем.
					$dbh->do(qq~UPDATE wars_history SET finish="$date" WHERE id="$row->{id}"~);
					reg_messages($row->{id});
				}
				
			} else {
				# такого вара в текущих нет. закрываем.
				$dbh->do(qq~UPDATE wars_history SET finish="$date" WHERE id="$row->{id}"~);
				reg_messages($row->{id});
			}
		}

		$sth->finish;

		# проверяем на начавшиеся, но еще не зарегистрированные вары
		foreach my $value(values %all_table) {
			
			my %wars = %{$value->{wars}};

			while ((my $warkey, my $warval) = each(%wars)) {
				
				my $all1 = $value->{name};
				my $all2 = $warkey;
				my $memb1 = join(",", @{$value->{members}});
				my $memb2 = join(",", @{$warval});
				
				$sth = $dbh->prepare(qq~SELECT * FROM wars_history WHERE (all1="$all1" OR all1="$all2") AND (all2="$all1" OR all2="$all2") AND finish="0"~);
				$sth->execute;
				my $row = $sth->fetchrow_hashref;
				$sth->finish;

				unless ($row) {
				
					$dbh->do(qq~INSERT wars_history SET all1="$all1", members1="$memb1", all2="$all2", members2="$memb2", start="$date", finish="0" ~);
					
					# получим последний ID, зарегистрируем системки.
					$sth = $dbh->prepare(qq~SELECT MAX(id) AS id FROM wars_history~);
					$sth->execute;
					my $row = $sth->fetchrow_hashref;
					$sth->finish;
					reg_messages($row->{id});
				}

				

			}
		}

		unlink "$logs/$fname";
	}
}

sub kaboom {
	
	my  $input = $_[0];		# багнутая строка.

	return 1 if ($input eq "");
	
	my $flag = 0;

	foreach my $ccc (keys %clans) {
		
		if  ( $input =~ /^($ccc)(.*$)/ ) {
			
			my $result = kaboom($2);

			if ($result == 1) {
				push @reset_clans, $ccc;
				$flag = 1;
				last;
			}
		}
	}

	return $flag;
}



# заполнение файла данных доступа 
my $sth = $dbh->prepare(qq~SELECT name FROM sd_cops WHERE dept in (12,13,14,15,57,71,27,83,105);~);
$sth->execute;
my $row;
my $fname = "$home/accessnames";
open my ($file), ">$fname.tmp";
print $file "Текс\n";
print $file "Ксакеп\n";
while ($row = $sth->fetchrow_hashref) {
	print $file $row->{name}."\n";
}
close $file;
rename "$fname.tmp", "$fname.txt";