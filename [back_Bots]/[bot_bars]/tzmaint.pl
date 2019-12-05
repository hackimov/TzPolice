#!/usr/bin/perl -w

use strict;

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

msg("TZBOT maint started");

our $dbh = DBI->connect("DBI:mysql:database=$conf{sql_dbase};host=$conf{sql_host}", $conf{sql_login}, $conf{sql_pass}, { RaiseError => 1 });

$dbh->do(qq~SET NAMES "cp1251"~);

foreach (sort <$logs/chat-*-????????.txt>) {
	unlink $_ if (-M $_) > 5;
}

foreach (sort <$logs/fab-fabzon-??????????????.txt>) {
	m#^\Q$logs\E/(.*\.txt)$#;
	my $fname = $1;

	if (open my $file, "$logs/$fname") {
		my ($p_day, $line_no) = (0, 0);

		foreach (<$file>) {
			if (/^(\d+)\.(\d+)\.(\d+) (\d+):(\d+)\t(.+?)\t(.*)(\r?\n)?$/) {
				my ($day, $mon, $year, $hour, $min, $event, $data) = ($1, $2, $3, $4, $5, $6, $7);

				next if $event == 3105;

				my $date = "20$year-$mon-$day";
				my $time = "$hour:$min:00";

				$data =~ s/\t/|/gs;

				if ($day != $p_day) {
					$line_no = 1;
					$p_day = $day;
				}

				my $sth = $dbh->prepare(qq~SELECT * FROM bot_prison_logs WHERE log_date="$date" AND line_no="$line_no"~);
				$sth->execute;
				my $row = $sth->fetchrow_hashref;
				$sth->finish;

				my $process;

				unless ($row) {
					$process = 1;

					$dbh->do(qq~INSERT bot_prison_logs SET log_date="$date", log_time="$time", line_no="$line_no", event="$event", data="$data"~);

				} elsif (($row->{log_time} ne $time) || ($row->{event} ne $event) || ($row->{data} ne $data)) {
					$process = 1;

					msg("Warning! Rollback detected: bot_prison_logs $date $line_no");

					$sth = $dbh->prepare(qq~SELECT * FROM bot_prison_logs WHERE id>="$row->{id}"~);
					$sth->execute;

					while ($row = $sth->fetchrow_hashref) {
						if ($row->{event} == 12200) {
							if ($row->{data} =~ /^(.+)\|.*\[(\d+)\]$/) {
								my ($login, $count) = ($1, $2);

								$dbh->do(qq~UPDATE prison_chars SET collected=collected-"$count" WHERE nick="$login"~);
								$dbh->do(qq~UPDATE prison_rating SET collected=collected-"$count" WHERE nick="$login" AND date="$row->{log_date}"~);
							}
						}

						$dbh->do(qq~DELETE FROM bot_prison_logs WHERE id="$row->{id}"~);
					}

					$sth->finish;
				}

				if ($process) {
					if ($event == 12200) {
						if ($data =~ /^(.+)\|.*\[(\d+)\]$/) {
							my ($login, $count) = ($1, $2);

							if ($dbh->do(qq~UPDATE prison_chars SET last_pay="$^T", collected=collected+"$count" WHERE nick="$login"~) < 1) {
								$dbh->do(qq~INSERT prison_chars SET nick="$login", term=-1, collected="$count", last_pay="$^T", reason="-1", remark="Добавлен автоматически", dept=-1, add_date="~.strftime("%Y-%m-%d", localtime $^T).qq~", add_by="Автомат"~);
                            				}

							if ($dbh->do(qq~UPDATE prison_rating SET collected=collected+"$count" WHERE nick="$login" AND date="$date"~) < 1) {
								$dbh->do(qq~INSERT prison_rating SET nick="$login", collected="$count", date="$date"~);
							}
						}
					}
				}

				$line_no++;
			}
		}

		close $file;

		unlink "$logs/$fname";
	}
}

msg("TZBOT maint done");