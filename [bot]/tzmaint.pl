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

# �� ����� ��������� ���� ���� ����� ����� ���������... ��� ����.
foreach (sort <$logs/chat-*-????????.txt>) {
	unlink $_ if (-M $_) > 5;
}

# ������������ ���� ������ � ���� �� ��������, �������� ������ �� ������� ��������.
foreach (sort <$logs/fab-?abzon-??????????????.txt>) {
	m#^\Q$logs\E/(.*\.txt)$#;
	my $fname = $1;
	msg(qq~get file: $fname ~);
	
	if (open my $file, "$logs/$fname") {
		my ($p_day, $line_no) = (0, 0);
		my $bid = "nobody";
		# ���������� �������� id ���� �� �������� �����
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
				
				# �������� ���. ������.
				if (index($data,"�����������|") == 0) {
					next;
				}
				# -------------------------------------

				# ��� ������� ������ ��� ���� ������ �������������� ����� ������
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
									# �������� �� �������� � ��������� ���-�� ������ �������� ��, ��� ������ � �����.
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
									$dbh->do(qq~INSERT prison_chars SET nick="$login", term=-1, collected="$count", last_pay="$^T", reason="-1", remark="�������� �������������", dept=-1, add_date="~.strftime("%Y-%m-%d", localtime $^T).qq~", add_by="�������"~);
									msg(qq~INSERT prison_chars SET nick="$login", term=-1, collected="$count", last_pay="$^T", reason="-1", remark="�������� �������������", dept=-1, add_date="~.strftime("%Y-%m-%d", localtime $^T).qq~", add_by="�������", bot_id="$bid"~);
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

#  �������� �� ���� id ������ �� �������, ���������, � ����������� �� ������ ��������� ���������.
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
	
	# ��������� "������" �����/�������
	
	my $cl = join('\', \'', split(/,/, $mainrow->{members1}));
	
	$sth = $dbh->prepare(qq~SELECT s.user_name, w.m_declaration, w.m_start, w.m_finish FROM wars_subscribe AS w INNER JOIN site_users AS s ON(s.id = w.user_id) INNER JOIN locator AS l ON(s.user_name = l.login) WHERE l.clan IN ('$cl')~);
	$sth->execute;
	
	my $text1 = '';
	my $text2 = '';
	
	while (my $row = $sth->fetchrow_hashref) {
		
		if ('*'.$mainrow->{members1} eq $mainrow->{all1}) {
			$text1 = '';
		} else {
			$text1 = qq~� ������� ������� $mainrow->{all1},~;
		}

		if ('*'.$mainrow->{members2} eq $mainrow->{all2}) {
			$text2 = qq~ ������ $mainrow->{members2}~;
		} else {
			$text2 = qq~ �������� $mainrow->{all2} ($mainrow->{members2})~;
		}
		
		if ($event eq 'start') {
			
			if ($row->{m_declaration} eq '1') {
				
				my $message = qq~��������! ���������������� ���������� ����� ����� ����� ������ $text1 � $text2 . ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
			if ($row->{m_start} eq '1') {

				my $message = qq~��������! ����� � $text2 �������� � �������� ����! � ����� ������� � ���� �� ��������� � ������� ��������� � �������! ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))+86100;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
		}
		
		if ($event eq 'finish') {
		
			if ($row->{m_finish} eq '1') {
			
				my $message = qq~��������! ���������������� ��������� ����� � $text2 . ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
		}
	}
	
	$sth->finish;
	
	# ��������� "�������" �����/�������
	
	$cl = join('\', \'', split(/,/, $mainrow->{members2}));
	
	$sth = $dbh->prepare(qq~SELECT s.user_name, w.m_declaration, w.m_start, w.m_finish FROM wars_subscribe AS w INNER JOIN site_users AS s ON(s.id = w.user_id) INNER JOIN locator AS l ON(s.user_name = l.login) WHERE l.clan IN ('$cl')~);
	$sth->execute;
	
	$text1 = '';
	$text2 = '';
	
	while (my $row = $sth->fetchrow_hashref) {
		
		if ('*'.$mainrow->{members2} eq $mainrow->{all2}) {
			$text1 = '';
		} else {
			$text1 = qq~� ������� ������� $mainrow->{all2},~;
		}

		if ('*'.$mainrow->{members1} eq $mainrow->{all1}) {
			$text2 = qq~ ������ $mainrow->{members1}~;
		} else {
			$text2 = qq~ �������� $mainrow->{all1} ($mainrow->{members1})~;
		}
		
		if ($event eq 'start') {
			
			if ($row->{m_declaration} eq '1') {
				
				my $message = qq~��������! ���������������� ���������� ����� ����� ����� ������ $text1 � $text2 . ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
			if ($row->{m_start} eq '1') {

				my $message = qq~��������! ����� � $text2 �������� � �������� ����! � ����� ������� � ���� �� ��������� � ������� ��������� � �������! ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))+86100;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
		}
		
		if ($event eq 'finish') {
		
			if ($row->{m_finish} eq '1') {
			
				my $message = qq~��������! ���������������� ��������� ����� � $text2 . ���������� �������� ��� ������ �������� ���� �� ������ http://tzpolice.ru/?act=offwars, �������� ������ ������ ��� ��� � ������ ����� ��������� ������������ ����� ��! ~;
				my $send_after = mktime(localtime(time))-300;
				$dbh->do(qq~INSERT bots_messages SET bot_id="wars", recp="$row->{user_name}", message="$message", send_after="$send_after" ~);
			}
			
		}
	}
	
	$sth->finish;
	
}

#reg_messages('233');

our %clans = ();			# ��� ������� ������, ��� �������� �������� ������� � ������ ������������ ������ 
our @reset_clans = ();		# ������ ������, � ������� ����� ���������� ����� ��� ������ �������� ������
# ������������ ������������ �����, �������� ������ �� ������� � ������������� ����-�����.

foreach (sort <$logs/wars-??????????????.txt>) {
	
	m#^\Q$logs\E/(.*\.txt)$#;
	my $fname = $1;
	msg(qq~get file: $fname ~);
	
	if (open my $file, "$logs/$fname") {

		# ���������� ���� �� ����� �����
		$fname =~ /^wars-(\d\d\d\d)(\d\d)(\d\d)(\d\d)(\d\d)(\d\d).txt$/;
		my ($day, $mon, $year, $hour, $min, $sec) = ($3, $2, $1, $4, $5, $6);
		$year = sprintf("%02d", $year % 100);
		my $date = mktime $sec, $min, $hour, $day, $mon-1, $year+100;
		
		my $clan_list = <$file>;
		my $all_list = <$file>;

		# ��������� �������� ������ �� ��������
		
		chomp($clan_list);
		chomp($all_list);

		my @cl = quotewords(",", 0, $clan_list);
		my @al = quotewords(",", 0, $all_list);

		my %all_table = ();		

		my @info = ();			# ������ ������ � �����. ��� �����, ID �������, ������ �����.
		my @wars = ();			# ������ ������-�����������

		my $record = "";		# ������ � �����

		foreach $record (@cl) {
			@info = quotewords(":", 0, $record);
			
			
			if (defined $info[5]) {
				
				my $all_name = ($info[1] ne "" ? $al[$info[1]] : "*$info[0]");
				$clans{$info[0]} = $all_name; 

				# �� ���� �������� ���������� ����� � �������.
				
				my @members = ();

				if(exists($all_table{$all_name})) {
					@members = @{$all_table{$all_name}->{members}};
				}

				push @members, $info[0];

				@wars = quotewords("/", 0, $info[5]);
				$all_table{$all_name} = { name => $all_name, members => [@members], wars => [@wars] };
			}
		}

		# ����� ��� � ����������� �����������. 
		foreach my $value(values %all_table) {
			
			# ���������� ������ ������-����������� ������� �����
			@wars = @{$value->{wars}};
			
			my $wr = 0;

			while ( $wr <= $#wars ) {
				# ��������� ������������� ������-����������� � ���� ������� ������
				if(!exists($clans{$wars[$wr]})) {
					@reset_clans = ();
					my $result = kaboom($wars[$wr]);
					splice (@wars, $wr, 1, @reset_clans);
				}
				$wr++;
			}

			$value->{wars} = [@wars];
		}
		
		# ��������� �����-���������� �� �������
		foreach my $value(values %all_table) {
			
			# ���������� ������ ������-����������� ������� �����
			@wars = @{$value->{wars}};
			my %new_wars = (); # ��� � �������, ��������� �� �������.
			
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

		# ��������� �� ������������ ����.
		
		my $sth = $dbh->prepare(qq~SELECT * FROM wars_history WHERE finish="0"~);
		$sth->execute;

		while (my $row = $sth->fetchrow_hashref) {
			
			my $all1 = $row->{all1};
			my $all2 = $row->{all2};
			
			# ��������� ������������� ������� 1.
			if(exists($all_table{$all1})) {
				# ���� ���������� - ��������� ������������� ������� 2.
				# ��� ������ ���� ������.. ���� �� 1 ��� ��������� ���, ���������� ����� ������ �������, � ��� ����� ��������..
				my %wars = %{$all_table{$all1}->{wars}};
				unless (exists($wars{$all2})) {
					# ������ ���� � ������� ���. ���������.
					$dbh->do(qq~UPDATE wars_history SET finish="$date" WHERE id="$row->{id}"~);
					reg_messages($row->{id});
				}
				
			} else {
				# ������ ���� � ������� ���. ���������.
				$dbh->do(qq~UPDATE wars_history SET finish="$date" WHERE id="$row->{id}"~);
				reg_messages($row->{id});
			}
		}

		$sth->finish;

		# ��������� �� ����������, �� ��� �� ������������������ ����
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
					
					# ������� ��������� ID, �������������� ��������.
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
	
	my  $input = $_[0];		# �������� ������.

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



# ���������� ����� ������ ������� 
my $sth = $dbh->prepare(qq~SELECT name FROM sd_cops WHERE dept in (12,13,14,15,57,71,27,83,105);~);
$sth->execute;
my $row;
my $fname = "$home/accessnames";
open my ($file), ">$fname.tmp";
print $file "����\n";
print $file "������\n";
while ($row = $sth->fetchrow_hashref) {
	print $file $row->{name}."\n";
}
close $file;
rename "$fname.tmp", "$fname.txt";