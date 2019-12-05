use strict;
srand(time);
our $home2 = "/home/sites/police/bot";

our (%mods, %conf, $logs, @res_names, $db2, $db, %twistsended);

#���������, ������� �� ���������� �� ���������� ���� �� ������
my @usekeys = ('login','serverid','nochat','btlformat','X','Y');
#����� ������� ���� �������
my @otherNicks = ('Battle Zone 1','Battle Zone 2','Battle Zone 3','Battle Zone 4','Battle Zone 5');
#������� �����
my $onCounter =0;
#����� ���������� ������� ���� � �����
my $retryinfotime = 300;
#������ ������ �� �������
my @savestring = ();

open STDERR, ">>$home2/test.log";
open STDOUT, ">&STDERR";

select STDERR; $| = 1;
select STDOUT; $| = 1;

$mods{pl_online} = {

	root_cmds	=> [],
	boss_cmds	=> [],

	on_connect	=> \&pl_online_on_connect,
	on_disconnect	=> \&pl_online_on_disconnect,
	on_idle		=> \&pl_online_on_idle,
	on_data		=> \&pl_online_on_data,
	on_root_cmd	=> \&pl_online_on_root_cmd,
	on_boss_cmd	=> \&pl_online_on_boss_cmd,

};

our %alerts = ();

sub load_alerts
	{
		open (BASE, "<$home2/send-police.txt");
		my @base = <BASE>;
		close (BASE);
		unlink("$home2/send-police.txt");
		touch("$home2/send-police.txt");
		my $q = 0666;
		my $w = "$home2/send-police.txt";
		chmod($q, $w);
		for (my $i=$#base; $i>=0; $i--)
			{
				(my $nick, my $msg)=split (/ \|\| /, $base [$i]);
				$::alerts{$nick}=$msg;
			}
	}

our @otherhistoryqueue;
my $sended = 0;
sub pl_online_on_connect
{
	my $bot = shift;
	$bot->{pl_online} = {} unless $bot->{pl_online};
	$bot->{pl_online_ses} = {};
	#client_out($bot, qq~<GETINFO login="$otherNicks[0]"/>~, time+5) if(defined($otherNicks[0]));
	unlink glob "$home2/*.ses";
	touch("$home2/$bot->{ses}.ses");
}

sub pl_online_on_disconnect
{
	my $bot = shift;
	msg("$bot->{login}  Client: Clearing session data...");
	unlink glob "$home2/*.ses";
	delete $bot->{pl_online_ses}
}

sub pl_online_on_idle
{
	my $bot = shift;
	my $wait = time-($bot->{pl_online}{lastscan}||0);
	my $wait2 = time-($bot->{pl_online}{lastsend}||0);
	my $wait3 = time-($bot->{pl_online}{lasthistoryscan}||0);
	my $wait4 = time-($bot->{pl_online}{lastotherhistoryscan}||0);
	my $wait5 = time-($bot->{pl_online}{lastscancmpl}||0);
	my $wait6 = time-($bot->{pl_online}{lastscanprisone}||0);
	my $wait7 = time-($bot->{pl_online}{lastscanposts}||0);  # �� ���� �� ������ �����
	
	if ($bot->{pl_online_ses}{busy}) {

		client_out($bot, "<LOGOUT/>") if $wait > $conf{pl_online_hangs};
		unlink glob "*.ses";
		return;
		
	}
	
	if($sended < 1) {
		msg("$wait, $wait2, $wait3, $wait4, $wait5, $wait6");
	    $sended = 1;
	}

	#my $fnamee = "/home/sites/police/bot/debug.txt";
	#open my ($filee), ">>$fnamee";
	#print $filee "Begin\n";
	#print $filee "$wait7\n";
	#print $filee "$conf{pl_online_postsscan}\n";
	#print $filee "$bot->{pl_online}{lastscanposts}\n";

	if ($wait7 > $conf{pl_online_postsscan}) {
		$bot->{pl_online}{lastscanposts} = time;
		msg("$bot->{login}: Posts scan");
        my $empty_post_cp = $db->prepare(qq~SELECT count(id) as i FROM posts_report WHERE city = 1 and post_g = 0~);
        $empty_post_cp->execute(); 
		$empty_post_cp = $empty_post_cp->fetchrow_hashref();
        $empty_post_cp = $$empty_post_cp{i};
		my $empty_post_nma = $db->prepare(qq~SELECT count(id) as i FROM posts_report WHERE city = 5 and post_g = 0~);
        $empty_post_nma->execute(); $empty_post_nma = $empty_post_nma->fetchrow_hashref();
        $empty_post_nma = $$empty_post_nma{i};
		my $empty_post_forum = $db->prepare(qq~SELECT count(id) as i FROM posts_report WHERE city = 3 and post_g = 0~);
        $empty_post_forum->execute(); $empty_post_forum = $empty_post_forum->fetchrow_hashref();
        $empty_post_forum = $$empty_post_forum{i};
		my $free_posts = "";

		if ($empty_post_cp == 0) {
			$free_posts = $free_posts."�� ������";
		}		

		if ($empty_post_nma == 0) {
			if (length($free_posts) > 0) {
				$free_posts = $free_posts.", ";
			}
			$free_posts = $free_posts."��������";
		}

		if ($empty_post_forum == 0) {
			if (length($free_posts) > 0) {
				$free_posts = $free_posts.", ";
			}
			$free_posts = $free_posts."������";
		}
		
		my $post = (length($free_posts) > 9)?'������':"�����";
		my $postr = (length($free_posts) > 9)?'�������� �����':"�������� ����";
		my $posts = (length($free_posts) > 9)?'�����':"����";
		my $postv = (length($free_posts) > 9)?'������':"�����";

		my @before_messages;
		push(@before_messages,"�� $post $free_posts ������� ��������� �����! :idea: ");
		push(@before_messages,":mad: ��� ����� ����� ���������? ������ �� $post $free_posts �����?");
		push(@before_messages,"���� �������� ����������� �� ����� ��������� �� $post $free_posts ?");
		push(@before_messages,"����������, ����������, �� �������������� :obm: $postr $free_posts !");
		push(@before_messages,"������� �������! :godig: �� $post $free_posts ���� ������ �������� :hehe: ");
		push(@before_messages,"�� ���������!!! :tyt: �� $post $free_posts � ��������� ����� ����������� ������� �������!");
		push(@before_messages,"�����!! :shy: $postr $free_posts !");
		push(@before_messages,"������ �� ����, � ������������ ���� �� ����������! $postr $free_posts .");
		push(@before_messages,"������ �� ����, ������ - work! $postr $free_posts .");
		push(@before_messages,"���� � ��, ������ �� ���� ��...  ��� ����?  :nobody: �� $post $free_posts � ������������ ������, ��?");
		push(@before_messages,"�� $post $free_posts ����������������� ������������� ���! :swans: ���, ��������? ������ ��� ���������� ����. ");
		push(@before_messages,"�� ��� ��� ����� ��, �? ������ � ��� ����� ���? $posts $free_posts ������, ��������, ������ �� ������ �����, ����� �� ����������������! ");
		push(@before_messages,"��� ��! ����� �� $post $free_posts ������. ����� ��� �������� ���������. � ����, ���. � � ���, � � � ������� ���������, ���� � � �����. ");
		push(@before_messages,"��� �� ��� ����� ������������, � ����� ������ �� $post $free_posts ���� ����� ��������� ���������� ������ ������. ");
		push(@before_messages,"� �������� �� ���� ������� ��� ������ � ��������� ���������� ��������? � ������ � ������ ��� ��������? $posts $free_posts �������� ������ ��� � ����������. ");
		push(@before_messages,"��� � ����� ���� $postv $free_posts - � ��� - ������! ");
		push(@before_messages,"����� ����� ����������� $posts $free_posts �� ��������. � �� �������� �������������... :hehe: . ");
		push(@before_messages,"�� ����������, �� ������������� ���-������ $posts $free_posts ! :mol: . ");
		push(@before_messages,"�����... ����!!! ��@#$^����. � ���� �� ���� ��� ����� ����� ������. ��� ��� ������� �� $posts $free_posts - ������� ���� ������ ���������. ");
		
		my @after_messages;
		push(@after_messages," ������� ������!");
		push(@after_messages," �����, �����! ������!");
		push(@after_messages," ����� ����������!");
		push(@after_messages," ��� ������?");
		push(@after_messages," ������ � �������!!!");
		push(@after_messages," ������ ������ � ������!");
		push(@after_messages," � ������ ��� ���� ������� �� ����������� ����������, ���.");
		push(@after_messages," ����-�-�-�-�-� ����!");
		push(@after_messages," ����� ����!");
		push(@after_messages," Go go go!");
		push(@after_messages," �����!");
		push(@after_messages," �����!");
		push(@after_messages," �� ��!");
		push(@after_messages," �� ��� ����!");
		push(@after_messages," �� ���� � �� �����!");
		push(@after_messages," �������! :horse:");
		push(@after_messages," ���������� ���� ���������!");
		

		my @allright_messages;
		push(@allright_messages,"� ������� ��� ����������� :serenade: ");
		push(@allright_messages,"��� �� ������.. ������� :dunno: ");
		push(@allright_messages,"��� ��������� � ����� � � �����?! ���������, ���������� �� :mol: ");
		push(@allright_messages,"� �� �������� ������ �� ����� ��������, ��? � ������. ������! :greedy: ");
		push(@allright_messages,"����������! � �� ���� ���� ������, �� ����� ������, � ������� ������ ��! ����� �������! :nerv: ");
		push(@allright_messages,"���-������, ��������� ��������. � �� ����� ��� �������� �� ����. :mosk: ");
		push(@allright_messages,"�����������! :bulldog: �������������� ����� ����. �����. ������ ��� ����� ������. �� �� ��� �����, ��� ����������.");
		push(@allright_messages,"������ ��� � �� ���, � ���� ��������, ��� ����� ��� ����������. ����. ������ � ���� ����, � �� ���? :nerv: ");
		push(@allright_messages,"�� ��-� ������ ���� �������, ������ ��� �� ������ �� ������� :serenade:");
		push(@allright_messages,"��� ��. ���� ������� �� �� ����. ��� ��� �����.");
		push(@allright_messages,"���������� ������ ��! �������� ��� ������������� �� ����� ������������� ��������� � ������. ������ ������� ������� ������ ���.");
		push(@allright_messages,"��������������� (��� � ���������. ����� ��������� �������� ������, ����� ���������)");
		

        if(length($free_posts) > 0) {
			my $rnd1 = int rand values @before_messages;
			my $mess1 = @before_messages[$rnd1];
			$rnd1 = int rand values @after_messages;
			$mess1 = $mess1.@after_messages[$rnd1];


        	chat_msg($bot,"private [clan] $mess1");
        	# msg("$bot->{login}: $mess1");
        } else {
			my $rnd1 = int rand values @allright_messages;
			my $mess1 = @allright_messages[$rnd1];

        	chat_msg($bot,"private [clan] $mess1");
        	# msg("$bot->{login}: $mess1");
		
		}

		return;
	}

	if ($wait6 > $conf{pl_online_prisonescan}) {
		$bot->{pl_online}{lastscanprisone} = time;
        msg("$bot->{login}: Prisone scan");
        my $have_no_reason = $db2->prepare(qq~SELECT count(id) as i FROM prison_chars WHERE reason < 1 and term < 0~);
        $have_no_reason->execute();
		$have_no_reason = $have_no_reason->fetchrow_hashref();
        $have_no_reason = $$have_no_reason{i};

        my $ready_to_freedom = $db2->prepare(qq~SELECT count(id) as i FROM prison_chars WHERE `term` > 0 AND `term` <= `collected`~);
        $ready_to_freedom->execute(); $ready_to_freedom = $ready_to_freedom->fetchrow_hashref();
        $ready_to_freedom = $$ready_to_freedom{i};

        if($ready_to_freedom > 0 || $have_no_reason > 0) {        	chat_msg($bot,"private [clan] $ready_to_freedom ������� ������������. $have_no_reason ��� �������������� ��������������.");
        	msg("$bot->{login}: ���� $ready_to_freedom ��������� �������, ���� $have_no_reason ��� �������������� ��������������");
        }
		return;
	}

	if ($wait > $conf{pl_online_rescan}) {
		$bot->{pl_online}{lastscan} = time;
		$bot->{pl_online_ses}{busy} = 1;

		msg("$bot->{login}: Clan-list scan");
		client_out($bot, qq~<STATUS clan="1" />~);

		return;
	}

	if ($wait5 > $conf{pl_cmpl_rescan}) {
		
		$bot->{pl_online}{lastscancmpl} = time;

		#return if ($bot->{pl_online_ses}{cmplbusy});

		$bot->{pl_online_ses}{cmplbusy} = 1;
	
		# ���� �����
		my $complaintsreq = '<CMPL a="list" date="'.strftime("%d%m%y", localtime).'" />';
		msg("$bot->{login} ���� ����� $complaintsreq");
		client_out($bot, $complaintsreq);

		return;
	}

	if ($wait2 > $conf{pl_online_send}) {

		$bot->{pl_online}{lastsend} = time;
		$bot->{pl_online}{sendbusy} = 1;
		%alerts = ();
		load_alerts();
		msg("$bot->{login} send alerts");
		foreach my $person (keys %alerts)
			{
				client_out($bot, qq~<PDA id="1654248.1" p1="$person" p2="$alerts{$person}" />~);
			}

		$bot->{pl_online}{sendbusy} = 0;
		return;
	}

	if ($wait3 > $conf{pl_online_historyscan}) {

		return if ($bot->{pl_online_ses}{otherhistorybusy}); #other history scan is in progress
		$bot->{pl_online}{lasthistoryscan} = time;
		$bot->{pl_online_ses}{historybusy} = 1;

		# �������� �� 3 ������
		#	my $time = time-3600*24*30*3;
		# �������� �� 50 ����
		#	my $time = time-3600*24*50;
		# ������� �����
		my $time = time-3600*6;

		msg("$bot->{login}: My History scan ".strftime("%d.%m.%y", localtime $time));
		client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" />~);
		$bot->{pl_online_ses}{history} = [];
		client_out($bot, qq~<SCANHIS/>~);
		return;
	}

	if ($wait4>$conf{pl_online_otherhistoryscan}) {
		$bot->{pl_online}{lastotherhistoryscan} = time;
		
		#my $historybusyll = $bot->{pl_online_ses}{historybusy} || '0';
		#my $otherhistorybusyll = $bot->{pl_online_ses}{otherhistorybusy} || '0';
		#msg(qq~$bot->{login}: busy $historybusyll, other $otherhistorybusyll~);
		
		
		if ($bot->{pl_online_ses}{historybusy}) {
			msg("$bot->{login}: Impossible scan because historybusy.");
			return;
		} #test for own history scan in progress
		
		if ($bot->{pl_online_ses}{otherhistorybusy}) {
			msg("$bot->{login}: Impossible scan because otherhistorybusy.");
			return;
		} #other history scan is in progress


		my $newreq=shift @otherhistoryqueue;
		return unless defined $newreq; #test for empty queue
		$bot->{pl_online_ses}{otherhistorybusy} = 1;
		unshift @otherhistoryqueue, $newreq;
		$bot->{pl_online_ses}{otherhistory}=[];
		my ($cmd,$from,$who,$date_begin,$date_end)=split /\|/,$newreq;
		msg("$bot->{login}: $who history scan $date_begin-$date_end requested by $from");
		#chat_msg($bot,"private [AVE] $newreq - $from $who $date_begin $date_end");
		if ($cmd eq "inf"){
			client_out($bot,qq~<GETH date="~.$date_begin.qq~" login="~.$who.qq~" a="P" />~);
		}
		else
		{
			client_out($bot,qq~<GETH date="~.$date_begin.qq~" login="~.$who.qq~" b="1" a="U" />~);
		}
	}
}

sub pl_online_on_data
{
	my ($bot, $key, $data) = @_;

	#msg("$bot->{login}  pl_fab_on_data");

	if ($key->{name} eq "FC") {
		#my $time = time-2505600;
		#my $time = time-259200;
		#my $time = time-172800;
		my $time = time-7200;
		#my $time = time - 7776000;

#		client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="deadbeef" b="1"/>~);

		$bot->{pl_online_ses}{history} = [];

		return 1;

	} elsif ($key->{name} eq "CMPL") {
		#msg("$bot->{login}: $data");
		if ($data =~ /<CMPL.*?><S (.*)<\/CMPL>/s)
			{
				my @achtung = ('���������! ����� � �������! ��� ������ ������������! :tyt:','���� �� ��� �� ���� ��������, ���� ������ �� �����������!  :csotona:','��������������� ������ - ��� ������� ������ ���� � �������! (�) ������� XIV  :king:','�� ������� ������� �� �������! ����� ��� ������������� ������!  :dont:','������������ ������ ���������! ��� �������� ������ �����!  :hehe:','� �� ��� ������ �����! ������������...:bulldog:','��, ��������... ���o������, ��������, ��������! ��� ����� ������ �����������?! :nunu:','������, ����� ����������� ���� � ���� ������� ��� ����! ���� ������ �����������! :smoke:','�� ������, �� ������, ���� �������� ������. ������� ������ ������� �������� � �������� �� ������� ������ :crazy:','���������� ������� �� ������ ��������. ��-��-��, � ��� �������� ����� :horse:');
				my $random111 =  int(rand(8));
				msg("$bot->{login}: Sent clan reminder $random111");
				my $ach_message = $achtung[$random111];
				msg("$bot->{login}: Sent clan reminder $random111: $ach_message");
				chat_msg($bot,"private [clan] $ach_message");
			}
		else
			{
				# msg("$bot->{login}: no new complaints $data");
			}

		delete $bot->{pl_online_ses}{cmplbusy};

	} elsif ($key->{name} eq "HISTORY") {
#		if ($data =~ /<HISTORY.*?>(.*)<\/HISTORY>/s) {
#			my $history = $1;
#			foreach (split /\r?\n/, $history) {
#				push @{$bot->{pl_online_ses}{history}}, $_;
#			}
#		}
#
#		if ($key->{data}{date} ne strftime "%d.%m.%y", localtime) {
#			my ($day, $mon, $year) = split /\./, $key->{data}{date};
#			my $time = 86400 + mktime 0, 0, 0, $day, $mon-1, $year+100;
#
#			#msg("$bot->{login}  Plant: History ".strftime("%d.%m.%y", localtime $time));
#			client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="deadbeef" b="1"/>~);
#
#		} else {
#			my $fname = "$logs/pers-$bot->{id}-".strftime("%Y%m%d%H%M%S", localtime);
#
#			if (open my ($file), ">$fname.tmp") {
#				print $file join "\n", @{$bot->{pl_fab_ses}{history}};
#				close $file;
#
#				rename "$fname.tmp", "$fname.txt";
#			}
#
#			delete $bot->{pl_online_ses}{busy};
#			#msg("$bot->{login}  Plant: End scan");
#		}

#		msg("Incoming history");
		if ($data =~ /<HISTORY.*?>(.*)<\/HISTORY>/s) {
			my $history = $1;
			foreach (split /\r?\n/, $history)
				{
					if ($bot->{pl_online_ses}{historybusy}){
						push @{$bot->{pl_online_ses}{history}}, $_;
					}
					else{
						push (@{$bot->{pl_online_ses}{otherhistory}}, $_);
					}
				}
		}

		my ($cmd,$from,$who,$date_begin,$date_end)=split /\|/,$otherhistoryqueue[0];
#		msg("Incoming history $cmd,$from,$who,$date_begin,$date_end");
		if ($key->{data}{date} ne ($bot->{pl_online_ses}{historybusy}?strftime("%d.%m.%y", localtime):$date_end)) {
			
			#msg("$bot->{login}: =========== Dont scan becouse: keydatadate = $key->{data}{date}");

			my ($day, $mon, $year) = split /\./, $key->{data}{date};
			my $time = 93600 + mktime( 0, 0, 0, $day, $mon-1, $year+100);
			
			
			if ($bot->{pl_online_ses}{historybusy}){
				msg("$bot->{login}: My History scan next day ".strftime("%d.%m.%y", localtime $time));
				client_out($bot, qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" />~);
			}
			else{
				if ($cmd eq "inf"){
					msg("$bot->{login}: #1");
					client_out($bot,qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="~.$who.qq~" a="P" />~);
				}
				else
				{
					msg("$bot->{login}: #2");
					client_out($bot,qq~<GETH date="~.strftime("%d.%m.%y", localtime $time).qq~" login="~.$who.qq~" b="1" a="U" />~);
				}
			}

		} else {
			if ($bot->{pl_online_ses}{historybusy}){
				msg("$bot->{login}: My history scan ended.");
				my $fname = "$logs/myhist-$bot->{id}-".strftime("%Y%m%d%H%M%S", localtime);

				if (open my ($file), ">$fname.tmp") {
					print $file join "\n", @{$bot->{pl_online_ses}{history}};
					close $file;
					rename "$fname.tmp", "$fname.txt";
				}
				delete $bot->{pl_online_ses}{historybusy};
			}
			else
			{
				shift @otherhistoryqueue;
				delete $bot->{pl_online_ses}{otherhistorybusy};
				my $fname = "/home/sites/police/www/otherhistory/[$from] [$who] $date_begin $date_end";
				if (open my ($file), ">$fname.tmp") {
					print $file join "\n", @{$bot->{pl_online_ses}{otherhistory}};
					close $file;
					rename "$fname.tmp", "$fname.txt";
				}
				chat_msg($bot,"private [$from] ��� ������ �� $who ��������. ���������� �� �����.");
				msg("$bot->{login}: $who History scan ended.");
				if ($cmd eq "inf"){
					client_out($bot, qq~<DI a="G" login="$who" p1="History view requested by \'$from\'"  />~);
				}
				#open (BASE, ">>$home2/send-police.txt");
				#print BASE "$who || ���� ������� ���� ����������� ����������� \'$from\'";
				#close(BASE);

			}
			#msg("$bot->{login} : End scan");
			return 1;
		}

		return 1;

	} elsif ($key->{name} eq "STATUS") {
		if (defined $key->{data}{clan}) {
			my $fname = "$logs/police-".strftime("%Y%m%d%H%M%S", localtime);

			if (open my ($file), ">$fname.tmp") {
				print $file $key->{data}{clan};
				close $file;

				rename "$fname.tmp", "$fname.txt";
			}

			delete $bot->{pl_online_ses}{busy};
		#	msg("$bot->{login}: End scan");
		}
	} elsif ($key->{name} eq "USERPARAM") {
        my %user = {};
		$user{$_} = $key->{data}{$_} foreach(@usekeys);

        $user{'nochat'} = ($user{'nochat'} == 1)?'+':0;
        if($user{'btlformat'} eq '-107' || $user{'btlformat'} eq '-102') {
			$user{'online'} = 1;
			$user{'battle'} = 0;
		} elsif($user{'btlformat'} eq '-104') {
			$user{'online'} = 0;
			$user{'battle'} = 0;
		} elsif($user{'btlformat'} eq '-106') {
			$user{'online'} = 1;
			$user{'battle'} = 1;
		}
		#login:X/Y/Server/Chat || login:0
        my $savestring = ($user{'online'} == 1)?qq~$user{'login'}:$user{'X'}/$user{'Y'}/$user{'serverid'}/$user{'nochat'}~:qq~$user{'login'}:0~;
		push(@savestring,$savestring);
		#imsg(qq~INCOMING $savestring~);

        $onCounter++;
        my $nextParse = $otherNicks[$onCounter];
        unless(defined($nextParse)) {
			$onCounter = 0;
			my $fname = "$logs/police-".strftime("%Y%m%d%H%M%S", localtime);
            $savestring = join(',',@savestring);
			if (open my ($file), ">$fname.tmp") {
				print $file $savestring;
				close $file;

				rename "$fname.tmp", "$fname.txt";
			}
			@savestring = ();
			msg("$bot->{login}: #3");
	        client_out($bot, qq~<GETINFO login="$otherNicks[0]"/>~, time+$retryinfotime);
        } else {
			msg("$bot->{login}: #4");
        	client_out($bot, qq~<GETINFO login="$nextParse"/>~);
        }

	} elsif($key->{name} eq "NOUSER") {
     	$onCounter++;
	}

	return 0;
}

sub pl_online_on_root_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_root_cmd");

	return 0;
}

sub pl_online_on_boss_cmd
{
	my ($bot, $cmd, $arg) = @_;

	#msg("$bot->{login}  pl_fab_on_boss_cmd");

	return 0;
}

1;