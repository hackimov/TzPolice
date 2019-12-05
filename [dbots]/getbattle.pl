#!/usr/bin/perl
use strict;
use POSIX;
use DBI;
use Encode qw(encode decode);
use HTTP::Request::Common qw(GET POST);
use LWP::UserAgent;
use LWP::Simple;
use Time::Local;
use Digest::MD5 qw(md5 md5_hex md5_base64);

my $ua = LWP::UserAgent->new;

my $dbpolice = DBI->connect("DBI:mysql:database=tzpolice;host=192.168.253.6", "tzpolice", "Vt5mwJso", {RaiseError => 1}) or die ('DB!');
$dbpolice->do('SET NAMES cp1251');
our $home = "/home/sites/stalkerz/bots";
our $logs = "$home/logs";


#���� ������
sub imsg {
	my $action = shift;
	my $fbreak = strftime("%d%m%Y", localtime);
	if(open my ($file), ">>$logs/test.html") {
		print $file strftime("%d.%m.%Y %H:%M:%S", localtime);
		print $file qq~ \t $action \n ~;
		close $file;
	}
}
#�����
sub getUserRank {
	my $uranks = shift;
	$uranks = $uranks ? $uranks : 0;
	my $i=0;
	my @ranks = ("0", "20", "60", "120", "250", "600", "1100", "1800", "2500", "3200", "4000", "5000", "6000", "7200", "10000", "15000", "50000", "1000000");

	foreach (@ranks) {
 		$i++;
		last if($uranks >= $ranks[$i-1] && $uranks < $ranks[$i]);
 	}
	return $i;
}

#������� ��������� �����
sub battle {
	my $lognum = shift;
	my $initiator = shift;
	my $ID = shift;

    my $url = "http://city2.timezero.ru/getbattle?id=$lognum";
	my $content = $ua->request(HTTP::Request->new(GET=>$url));
	$content = $content->content;

	if(!$content) {		$dbpolice->do(qq~UPDATE import_info SET status=0 WHERE `id`='$ID'~);		return undef;
	}
	if($content =~ m/err=-12$/) {		$dbpolice->do(qq~UPDATE import_info SET status=3 WHERE `id`='$ID'~);
		return undef;
	}

	$content =~ s/(\n|\t|\r|\f|\e|  )/ /g;

	#��������� ���� �� ���� � ���
	$content = encode("cp1251",decode("utf8", $content));

	#������� ����� ����
    $content =~ s/(<MAP[^>]+>)//ig;
    $content =~ s/(<M[^>]+>)//ig;
	$content =~ s/([\s]?left="[\S]+")//ig;
	$content =~ s/([\s]?right="[\S]+")//ig;
	$content =~ s/([\s]?top="[\S]+")//ig;
	$content =~ s/([\s]?bottom="[\S]+")//ig;
	$content =~ s/([\s]?rf="[\S]+")//ig;


	my($params,$data,$uparams,$udata,$ddata,$tparams,$tdata,$tuparams,$tudata,$brokenturns,
    $dir,$c,$i,$realturns,$bendtime,@battle,@collected_drop,%drop,%gdrop,%sdrop,@users,@firstusers,
    $dies,$lives,%uddagm,$kills,%rkills,$mkills,%bsides,%udrop,%mdrop,$uut,%receipts,@rankedkills,%globalusers,
    %uniquest,%uniquestlist,@codeusers,@userline,$userline,$cheat,$curcheat,$ranksinbtl,@insertusers);
    my $server = 1;
    my $enemies = 0;


	#��������� ����
	$i=0; $realturns = -1; #������� �����, 0 ��� = ��� ����.
	while($content =~ s/<(?:TURN|BATTLE)[\s]*([^>]*)>((?:(?!<\/(?:TURN|BATTLE)>)[^\r])*)<\/(?:TURN|BATTLE)>//i) {
    	$tparams=$1; $tdata=$2; my(%btmp,@tmp); $realturns++;

        #��������� ��������� ����
        $btmp{$1}=$2 while($tparams =~ s/[\s]*([\w]+)=\"([^\"]+)\"[\s]*//i);
		$battle[$i]->{'tag'} = \%btmp;

        #�������� ����, � ����� ������ ���� �� ������� 0_�
        $i = $btmp{'turn'} if($i != $btmp{'turn'} && $i > 0);
        $battle[0]->{'bend'} = $btmp{'time'} if($btmp{'time'});

		#���������� <user>
		while($tdata =~ s/<USER([^>]+)>((?:(?!<\/(?:USER)>)[^'])*)<\/USER>//si) {
			$tuparams=$1; $tudata=$2; my %tmp;

            #�������� ��������� ���� <user>
			$tmp{$1} = $2 while($tuparams =~ s/[\s]*([\w_]+)=\"([^\"]*)\"[\s]*//i);
            my ($die,$away,$kill,$who,$howr,$mkill,$lmkills,$lukills);
            #����, ����. ����, ������. ����, �������, ����. �������, ������. �������, ������, ���� � ���� ���
            my($shothp,$cshothp,$bshothp,$shot,$cshot,$bshot,$miss,$nshot,$havesoul,$luranks) = (0,0,0,0,0,0,0,0,0,0);

            #�������� � ����
            while($tudata =~ s/<a[\s]+([^>]+)[\s]*\/>//i) {
                my $act=$1; my %tact;

                #��������� ��������
            	$tact{$1}=$2 while($act =~ s/([\w]+)=\"([^\"]*)\"[\s]*//i);

              	#����
            	if($tact{'t'} eq 7) {
            		$die++;
            		$dies++;
            	#������
            	} elsif($tact{'t'} eq 9) {
            		$away++;
            	#���� �����
            	} elsif($tact{'t'} eq 19) {            		#<a sf="318" t="19" n="100" login="���� ������"/>            		$havesoul++;
            		$kill++;
            		$kills++;
            		$who = $tact{'login'};
            		$howr = $tact{'n'};
            		$lukills .= "$i:$howr:$who|";
            		$luranks += $howr;
            		$battle[0]->{'ranks'} += $howr;
            	#���� ���� ��� �������
             	} elsif($tact{'t'} eq 20) {
            		$who = $tact{'login'};
            		$howr = 0;
                    #�� ����
            		unless($who =~ m/\$([^\d]+)\d+/i) {                    	$lukills .= "$i:$howr:$who|";
                    	$havesoul++;
	            		$kill++;
	            		$kills++;
            		} else {            			$mkill++;
            			$mkills++;
            		}
            	#��������
            	} elsif($tact{'t'} eq 5) {
                    #���-�� � ������� ������ ����.  � ������ ��� ���� $tact{'p'}
            		if($tact{'HP'} && $tact{'type'} != 8 && $tact{'type'} != 17) {
            			my (@shottype,@shottmp,@shottype2,$queue);
                        @shottype2 = split(",",$tact{'HP'});
                        $shottype[0] = 0;
						$shottype[1] = 0;
                        if(defined($shottype2[1])) {
	                       	foreach(@shottype2) {
	                       		@shottmp = split(":",$_);
	                       		$queue++ unless($shottmp[1] =~ m/(-\d+)/ig);
                       			$shottype[1] += $shottmp[1] unless($shottmp[1] =~ m/[^\d]+/ig);
	                       	}
                        } else {
                        	$queue++;
            				@shottype = split(":",$tact{'HP'});
            			}
            			$shottype[1] = ($shottype[1] =~ m/B(-\d+)/ig) ? $1 : $shottype[1];
                        $shottype[1] = ($shottype[1] =~ m/[^-\d]+/ig) ? 0 : $shottype[1];

            			if($shottype[1] > -1) {
            				#imsg("$shottype[0] : $shottype[1] == $tact{'HP'} ($queue) / $lognum");
	                        #�����
	            			if($shottype[0] eq 1 && $shottype[1] > 0) {
	            				$cshot+=$queue;
	            				$cshothp += $shottype[1];
	            			#��������
	            			} elsif($shottype[0] eq 2 && $shottype[1] > 0) {
	            				$bshot+=$queue;
	            				$bshothp += $shottype[1];
	            			#������� �������
	            			} elsif($shottype[0] eq 0 && $shottype[1] > 0) {
	            				$shot+=$queue;
	            				$shothp += $shottype[1];
	            			#������� ���������
	            			} else {
	            				$nshot++;
	            			}
            			} elsif(int($shottype[1]) < 0) {
            				#imsg("HEAL? $shottype[1]");
            			} else {
            				#imsg("WTF?? $shottype[0] : $shottype[1] == $tact{'HP'} / $lognum");
            			}
            		#������!
            		} elsif(!$tact{'HP'} && $tact{'type'} != 8 && $tact{'type'} != 17 && !$tact{'p'}) {
            			$miss++;
            		}

            	}

            }

            #$l = id �����. $uex = ������� ����� � �������
			my($l,$uex)=(0,0);
            my $m_ob = ($tmp{'login'} =~ m/\$([^\d]+)\d+/i) ? 1 : 0;

			foreach(@users) {
                $server = $tmp{'serverid'} if(defined($tmp{'serverid'}));

                #���� ����� ����� ���� � �������, ���������.
                if($users[$l]->{'login'} eq $tmp{'login'}) {
                	$users[$l]->{'die'} = $i if($die);
                	$users[$l]->{'live'} = ($die)?0:1;
                	$users[$l]->{'away'} = $i if($away);
                	if($kill) {
                		$users[$l]->{'kills'} .= $lukills;
                		$users[$l]->{'getranks'} += $luranks;
                		$users[$l]->{'akill'} += $kills;
                	}
                	$users[$l]->{'shots'} += $shot;
                	$users[$l]->{'cshots'} += $cshot;
                	$users[$l]->{'bshots'} += $bshot;
                	$users[$l]->{'nshots'} += $nshot;
                	$users[$l]->{'damage'} += $shothp;
                	$users[$l]->{'cdamage'} += $cshothp;
                	$users[$l]->{'bdamage'} += $bshothp;
                	$users[$l]->{'misses'} += $miss;
                    $users[$l]->{'fulldamage'} += $cshothp+$bshothp+$shothp;

                	foreach(sort keys %tmp) {
                		$users[$l]->{$_} = $tmp{$_};
                	}

                	foreach my $e (sort keys %{$users[$l]}) {
                		$globalusers{$tmp{'login'}}->{$e} = $users[$l]->{$e};
                	}
                	$uex++; #���� ������ � �������, ��������� ����������
                	last;
                }
            	$l++;
			}

			#���� ������ ������ ��� � �������, ������.
			unless($uex) {
				#�� ����� ���� ���� ����� � ���
				$tmp{'entered'} = $i;
                $bsides{$tmp{'side'}}++ if($tmp{'side'});

				#���� ��� ����� � ������, �� ��� ��� = ���
				if($i < 1) {
					$battle[0]->{'tag'}->{'pvp'}=0;
					if($tmp{'man'}>1 || $tmp{'name'} || $tmp{'bot_p'} || $battle[0]->{'tag'}->{'test'} || $battle[0]->{'tag'}->{'ms_n'}){
						$battle[0]->{'tag'}->{'pvp'}=1;
					}
					#��� ���
					$battle[0]->{'tag'}->{'pvp'}=2 if($battle[0]->{'tag'}->{'pvp'} != 1);
				}
				my $un = push(@users,\%tmp);
				$un--;

				#����� ����, ���� ���� ����� ��� ���������.
				$users[$un]->{'die'} = $i if($die);
				$users[$un]->{'live'} = ($die)?0:1;
                $users[$un]->{'away'} = $i if($away);
                if($kill) {
                	$users[$un]->{'kills'} .= $lukills;
                	$users[$un]->{'getranks'} += $luranks;
                	$users[$un]->{'akill'} += $kills;
                }
                $users[$un]->{'shots'} += $shot;
                $users[$un]->{'cshots'} += $cshot;
                $users[$un]->{'bshots'} += $bshot;
                $users[$un]->{'nshots'} += $nshot;
                $users[$un]->{'damage'} += $shothp;
                $users[$un]->{'cdamage'} += $cshothp;

                $users[$un]->{'bdamage'} += $bshothp;
                $users[$un]->{'misses'} += $miss;
                $users[$un]->{'ismob'} = ($m_ob < 1)?0:1;
                $users[$un]->{'fulldamage'} += $cshothp+$bshothp+$shothp;

                #����� � ������ ���
				$users[$un]->{'start_ranks'} = $tmp{'rank_points'};

				foreach my $e (sort keys %{$users[$un]}) {
					$globalusers{$tmp{'login'}}->{$e} = $users[$un]->{$e};
				}
				if($m_ob < 1) {
					$enemies = push(@codeusers,$tmp{'login'});
					#print qq~ENEMY: $tmp{'login'}\n~;
				}
			}
		}

	$i++;
	}
    @codeusers = sort(@codeusers);
    my $md5users = join('',@codeusers);

	#print qq~MD: $md5users\n~;

	#���������� �� ��� ���� �� ����� ������������� ��������, -161 = 360-161. ������.
	($battle[0]->{'X'},$battle[0]->{'Y'},$battle[0]->{'bstart'}) = split(",", $battle[0]->{'tag'}->{'note'});
	my @bt = localtime($battle[0]->{'bstart'});
    my $n=0;
	foreach(@bt) {		$bt[$n] = (defined($bt[$n]))?$bt[$n]:0;
		if($n == 4) {
			$bt[$n]++;
		} elsif($n == 5) {			$bt[$n] += 1900;
		} else {			$bt[$n] = ($bt[$n]>9)?$bt[$n]:qq~0$bt[$n]~;
		}
		$n++;	}
	$battle[0]->{'long'} = $battle[0]->{'bend'}-$battle[0]->{'bstart'};
    $battle[0]->{'hash'} = md5_hex($md5users);
    $battle[0]->{'location'} = qq~$battle[0]->{'X'},$battle[0]->{'Y'}~;

    #Y-m-d,H:i:s timelocal($sec,$min,$hour,$mday,$mon,$year);
    $battle[0]->{'date'} = qq~$bt[5]-$bt[4]-$bt[3]~;
    $battle[0]->{'time'} = qq~$bt[2]:$bt[1]:$bt[0]~;
    $battle[0]->{'commondmg'} = 0;
    $battle[0]->{'livelvls'} = 0;
    $battle[0]->{'dielvls'} = 0;
    $battle[0]->{'ranks'} = 0;

    #print qq~E: $enemies, H: $battle[0]->{'hash'},  $battle[0]->{'date'} $battle[0]->{'time'} \n~;

    foreach(@users) {    	my $U = $_;
    	next if($$U{'ismob'} > 0 || $$U{'level'} < 1 || !$$U{'login'});

    	if(defined($$U{'kills'})) {
    		chop($$U{'kills'});
    		if($$U{'getranks'} > 0) {
    			$$U{'getranks'} = ($$U{'live'} > 0)?($$U{'getranks'}*5)/100:$$U{'getranks'}/100;
    			$battle[0]->{'ranks'} += $$U{'getranks'};
    		}
    	} else {    		$$U{'kills'} = '';
    		$$U{'getranks'} = 0;
    	}
    	if($$U{'live'} > 0) {    		$battle[0]->{'livelvls'}+=$$U{'level'};
    		$battle[0]->{'commondmg'}+=$$U{'fulldamage'};
    	} else {    		$battle[0]->{'dielvls'}+=$$U{'level'};
    	}
		push(@userline,qq~$$U{'login'},$$U{'level'},$$U{'side'},$$U{'entered'},$$U{'fulldamage'},$$U{'live'},$$U{'clan'},$$U{'HP'},$$U{'kills'}&$$U{'getranks'}~);
		push(@insertusers,{'login'=>$$U{'login'},'level'=>$$U{'level'},'ranks'=>$$U{'getranks'}});
	}

    $userline = join(';',@userline);
    $cheat = ($battle[0]->{'livelvls'} && $battle[0]->{'dielvls'})?($battle[0]->{'dielvls'}-$battle[0]->{'livelvls'})/($battle[0]->{'dielvls'}+$battle[0]->{'livelvls'}):0;
    $curcheat = ($battle[0]->{'commondmg'} > 0)?0:1;
    $ranksinbtl = ($battle[0]->{'ranks'} > 0)?1:0;



    #$userline = "login,lvl,group,income turn,damage,live,clan,user HP,ranks[$turn:$getr:$killed|$turn:$getr:$killed&$getranks];";
    #$Location = x,y
    #$cheat = (($deadls-$servivels)/($deadls+$servivels)) | $deadls += lvls, $servivels += lvls
    #$curcheat += (died users->common dmg > 0)?0:1
    #$ranksInBtl = (ranks > 0)?1:0
    #$vranks = ranks;
    if($enemies > 1) {
	    my $exists = $dbpolice->prepare(qq~SELECT * FROM battles WHERE BattleID='$lognum'~);
	    $exists->execute(); $exists = $exists->fetchrow_hashref();
	    unless($$exists{'BattleID'} > 0) {	    	imsg(qq~INSERT INTO `battles` VALUES(NULL,'$battle[0]->{date}','$battle[0]->{time}','$lognum')~);
		    $dbpolice->do(qq~INSERT INTO `battles` VALUES(NULL,'$battle[0]->{date}','$battle[0]->{time}','$lognum','$battle[0]->{hash}','$userline','$battle[0]->{location}','$cheat','$curcheat','$initiator','$ranksinbtl','$battle[0]->{ranks}',NOW())~);
		    my $rv = $dbpolice->last_insert_id(undef,undef,'battles','id');
		    foreach(@insertusers) {		    	imsg(qq~IU: INSERT INTO `battle_logins` VALUES (NULL, '$rv', '$_->{login}', '$_->{level}','$_->{ranks}')~);		        $dbpolice->do(qq~INSERT INTO `battle_logins` VALUES (NULL, '$rv', '$_->{login}', '$_->{level}','$_->{ranks}')~);
		    }
		}
    }

return 1;
}

$home = "/home/sites/police/dbots";
my $fname = "$home/test";
open my ($file), ">$fname.txt";
print $file "Stealth\n";
close $file;


my $query = $dbpolice->prepare(qq~SELECT `id`, `LogID`, `status`, `InsertBy`, `nick` FROM `import_info` WHERE `status`<2 ORDER BY `status` DESC, `doitnow` DESC, `id` ASC LIMIT 1000~);
$query->execute();

while (my $b = $query->fetchrow_hashref) {

	$dbpolice->do(qq~UPDATE `import_info` SET `status`=1 WHERE `id`='$$b{id}'~);
	if(battle($$b{LogID},$$b{InsertBy},$$b{id})) {
		imsg(qq~LOAD: $$b{LogID} [$$b{id}] > $$b{status}  $$b{InsertBy} -> ok~);
		$dbpolice->do(qq~UPDATE `import_info` SET `status`=2 WHERE `id`='$$b{id}'~);
	} else {		imsg(qq~LOAD: $$b{LogID} [$$b{id}] > $$b{status}  $$b{InsertBy} -> fail~);
	}
}

