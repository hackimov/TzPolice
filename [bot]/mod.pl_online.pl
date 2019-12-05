use strict;
srand(time);
our $home2 = "/home/sites/police/bot";

our (%mods, %conf, $logs, @res_names, $db2, $db, %twistsended);

#параметры, которые мы используем из приходящей инфы по персам
my @usekeys = ('login','serverid','nochat','btlformat','X','Y');
#персы которых надо сканить
my @otherNicks = ('Battle Zone 1','Battle Zone 2','Battle Zone 3','Battle Zone 4','Battle Zone 5');
#счётчик ботов
my $onCounter =0;
#время повторного запроса инфы о ботах
my $retryinfotime = 300;
#массив данных по юзверям
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
	my $wait7 = time-($bot->{pl_online}{lastscanposts}||0);  # не пора ли обойти посты
	
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
			$free_posts = $free_posts."ЦП Москвы";
		}		

		if ($empty_post_nma == 0) {
			if (length($free_posts) > 0) {
				$free_posts = $free_posts.", ";
			}
			$free_posts = $free_posts."Аукциона";
		}

		if ($empty_post_forum == 0) {
			if (length($free_posts) > 0) {
				$free_posts = $free_posts.", ";
			}
			$free_posts = $free_posts."Форума";
		}
		
		my $post = (length($free_posts) > 9)?'постах':"посту";
		my $postr = (length($free_posts) > 9)?'Свободны посты':"Свободен пост";
		my $posts = (length($free_posts) > 9)?'посты':"пост";
		my $postv = (length($free_posts) > 9)?'постов':"поста";

		my @before_messages;
		push(@before_messages,"На $post $free_posts имеются вакантные места! :idea: ");
		push(@before_messages,":mad: Кто будет буквы проверять? Почему на $post $free_posts пусто?");
		push(@before_messages,"Есть желающие потрудиться на благо отечества на $post $free_posts ?");
		push(@before_messages,"Мониторили, мониторили, да невымониторили :obm: $postr $free_posts !");
		push(@before_messages,"Перекур окончен! :godig: На $post $free_posts пора копать молчанки :hehe: ");
		push(@before_messages,"Не спиииииим!!! :tyt: На $post $free_posts с некоторой долей вероятности матерят полицию!");
		push(@before_messages,"Людии!! :shy: $postr $free_posts !");
		push(@before_messages,"Работа не волк, а произведение силы на расстояние! $postr $free_posts .");
		push(@before_messages,"Работа не волк, работа - work! $postr $free_posts .");
		push(@before_messages,"Тихо в ТЗ, только не спит ОМ...  Или спит?  :nobody: На $post $free_posts я модерировать должен, да?");
		push(@before_messages,"На $post $free_posts зарегистрированны множественные РВС! :swans: Что, поверили? Короче мое величество ждет. ");
		push(@before_messages,"Ну как так можно то, а? Вобоще у вас стыда нет? $posts $free_posts бедные, одинокие, никому не нужные стоят, никем не отмодерированные! ");
		push(@before_messages,"Вот ВЦ! Опять на $post $free_posts никого. Пойду сам молчанки раздавать. А хотя, нет. Я ж бот, я ж к участку прикручен, куда я ж пойду. ");
		push(@before_messages,"Вот вы тут штаны просиживаете, а между прочим на $post $free_posts даже самой маленькой молчаночки выдать некому. ");
		push(@before_messages,"И наверное вы таки думаете что попали в абсолютно культурное общество? А спорим я докажу вам обратное? $posts $free_posts быыыстро вернут вас к реальности. ");
		push(@before_messages,"Иду я такой мимо $postv $free_posts - а там - НИКОГО! ");
		push(@before_messages,"Лучше идите модерировть $posts $free_posts по хорошему. А то расскажу руковводителю... :hehe: . ");
		push(@before_messages,"Ну пожалуйста, ну помодерируйте кто-нибудь $posts $free_posts ! :mol: . ");
		push(@before_messages,"УУУУУ... ОООО!!! Ни@#$^себе. Я даже не знал что такие слова бывают. Вот так сходишь на $posts $free_posts - глядишь чему новому научишься. ");
		
		my @after_messages;
		push(@after_messages," Милости просим!");
		push(@after_messages," Шнель, шнель! Бистра!");
		push(@after_messages," Бобро пожаловать!");
		push(@after_messages," Кто первый?");
		push(@after_messages," Встаем в очередь!!!");
		push(@after_messages," Гладим шнурки и вперед!");
		push(@after_messages," Я смотрю все прям дерутся за возможность поработать, ага.");
		push(@after_messages," Шага-а-а-а-а-а Марш!");
		push(@after_messages," Магом шарш!");
		push(@after_messages," Go go go!");
		push(@after_messages," Вперёд!");
		push(@after_messages," Впердё!");
		push(@after_messages," За Бо!");
		push(@after_messages," Во имя луны!");
		push(@after_messages," За себя и за Сашку!");
		push(@after_messages," Паконям! :horse:");
		push(@after_messages," Нипастыдим наше призвание!");
		

		my @allright_messages;
		push(@allright_messages,"В Багдаде все спокойноооо :serenade: ");
		push(@allright_messages,"Все на постах.. странно :dunno: ");
		push(@allright_messages,"Кто мониторит в дождь и в грязь?! Правильно, сотрудники ОМ :mol: ");
		push(@allright_messages,"А вы наверное хотите на посту постоять, да? А нельзя. Занято! :greedy: ");
		push(@allright_messages,"Мащальнике! Я на пост ЦыПэ хадила, на форум хадила, в ауксыон хадила да! Везде ОэМнама! :nerv: ");
		push(@allright_messages,"Кто-нибудь, покормите постовых. А то пашут без перерыва на обед. :mosk: ");
		push(@allright_messages,"Спокойствие! :bulldog: Помодерировать дадут всем. Потом. Сейчас все посты заняты. Ну уж кто успел, как говориться.");
		push(@allright_messages,"Смотрю вот я на вас, и душа радуется, все посты под присмотром. Стоп. Откуда у меня душа, я же бот? :nerv: ");
		push(@allright_messages,"От ОМ-а станет всем светлей, Потому что на постах ОМ дежурит :serenade:");
		push(@allright_messages,"Вот ВЦ. Даже наехать не на кого. Все при делах.");
		push(@allright_messages,"Сотрудники отдела ОМ! Объявляю вам благодарность за такое ответственное отношение к работе. Другим отделам объявит другой бот.");
		push(@allright_messages,"ОООООООММММММММ (это я медитирую. Отдел модерации работает хорошо, можно отдохнуть)");
		

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

        if($ready_to_freedom > 0 || $have_no_reason > 0) {        	chat_msg($bot,"private [clan] $ready_to_freedom ожидают освобождения. $have_no_reason без установленного правонарушения.");
        	msg("$bot->{login}: Есть $ready_to_freedom ожидающих свободы, Есть $have_no_reason без установленного правонарушения");
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
	
		# скан жалоб
		my $complaintsreq = '<CMPL a="list" date="'.strftime("%d%m%y", localtime).'" />';
		msg("$bot->{login} скан жалоб $complaintsreq");
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

		# перескан за 3 месяца
		#	my $time = time-3600*24*30*3;
		# перескан за 50 дней
		#	my $time = time-3600*24*50;
		# обычный режим
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
				my @achtung = ('Верещагин! Уходи с баркаса! Иди жалобы рассматривай! :tyt:','Чтоб ты жил на одну зарплату, если жалобы не рассмотришь!  :csotona:','Нерассмотренная жалоба - это заговор против меня и Франции! (с) Людовик XIV  :king:','Не стучите лысиной по паркету! Идите уже рассматривать жалобы!  :dont:','Слабонервных просим удалиться! Тут таааакие жалобы висят!  :hehe:','Я на вас жалобу подам! Коллективную...:bulldog:','Ну, граждане... алкoголики, хулиганы, тунеядцы! Кто хочет жалобы рассмотреть?! :nunu:','Ватсон, самый совершенный мозг в мире ржавеет без дела! Надо жалобы рассмотреть! :smoke:','Мы писали, мы писали, наши пальчики устали. Ласкава просим размять запястье и потыкать по жалобам мышкой :crazy:','Пятнадцать человек на сундук мертвеца. Йо-хо-хо, и сто двадцать жалоб :horse:');
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
				chat_msg($bot,"private [$from] Ваш запрос по $who выполнен. Результаты на сайте.");
				msg("$bot->{login}: $who History scan ended.");
				if ($cmd eq "inf"){
					client_out($bot, qq~<DI a="G" login="$who" p1="History view requested by \'$from\'"  />~);
				}
				#open (BASE, ">>$home2/send-police.txt");
				#print BASE "$who || Ваша история была просмотрена сотрудником \'$from\'";
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