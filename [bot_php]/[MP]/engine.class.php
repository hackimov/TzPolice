<?php
include 'comm.class.php';
include 'mod.class.php';
class bot extends mod_core
	{
		var $home_dir;
		
		public function __construct($home_dir)
			{
			//	preg_match('#t="(.+)"#i', file_get_contents('http://game.timezero.ru/clientversion.xml?'.lcg_value()), $v_regs);
				$this->comm 	= & new comm($this);
				$this->client[] = 'city1.timezero.ru';
				$this->client[] = 'city2.timezero.ru';
				$this->chat 	= 'main.timezero.ru';
				$this->port 	= '5190';
//				$this->port 	= '5191';
			//	$this->v2		= $v_regs[1];
				$this->v2		= '0.1 (TZPD)';
				$this->home_dir = $home_dir;
				$this->mod_register_module('std');
		    }

		public function add_bot ($nick, $password, $pid = null, $server = null, $master = null)
			{
				$tek_index 	= ($pid) ? $pid : ++$this->count;
				$sv_num 	= ($server) ? $server : '0';
				if(!isset($this->client[$sv_num])) return(0);
				foreach ($master as $key=>$val)
					$master[$key] = strtolower($val);
				$this->bots[$tek_index] = array
				(
					'pid'			=> $tek_index,
					'nick' 			=> trim($nick),
					'password' 		=> trim($password),
					'v'				=> (int) file_get_contents($this->home_dir.'/version.txt'),
					'client_socket' => $this->comm->open($this->client[$sv_num], $this->port, $tek_index,  'client'),
					'chat_socket' 	=> $this->comm->open($this->chat, $this->port, $tek_index, 	'chat'),
					'sv_num'		=> $sv_num,
					'master'		=> $master
				);
			}

		public function process()
			{
				$this->listen = $this->comm->listen();
				$this->mod_process();
			}

		public function add_mysql($name, $host, $user, $pass, $db)
			{
				$link = mysql_pconnect($host, $user, $pass);
				if (mysql_errno())
				{
					$fp = fopen ('home/sites/police/bot_php/MP/logs/error_log.txt', 'a+');
					fwrite ($fp, date('d.m.y H:i:m ').mysql_errno().":".mysql_error()." ~~ ".$php_errormsg."\n");
					fclose ($fp);
				}
				mysql_select_db ($db);
				mysql_query('SET NAMES CP1251');
				$this->mysql_links[$name] = $link;
			}

		public function time_hook($time, $id)
			{
				$time_now = time();
				if (!isset($this->last_time[$id])) $this->last_time[$id] = $time_now + 1;
				if (($time_now - $this->last_time[$id]) >= $time)
					{
						$this->last_time[$id] = $time_now;
						return (1);
					}else{
						return (0);
					}
			}

		public function parse_node($a)
			{
				$a = mb_convert_encoding($a, 'utf-8', 'cp1251');
				$a=preg_replace('/frank="([\d\.]+:?)+"/','',$a,1);//---AVE--- quick&dirty hack for double frank!
				$xml_parser = xml_parser_create();
				xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
				xml_parse_into_struct($xml_parser, str_replace(array ('"/>', "\t"), array ('" />', '::TABULATION::'), $a), $val);
				xml_parser_free($xml_parser);

				list($result['tag'], $result['param']) = array($val[0]['tag'], $val[0]['attributes']);
				if($result['tag'] and $result['param'])
					{
						foreach ($result['param'] as $key=>$val)
						{
							$val					= str_replace('::TABULATION::', "\t", $val);
							$result['param'][$key]	= mb_convert_encoding($val, 'cp1251', 'utf-8');
						}
					}
				return($result);
			}

		public function view()
			{
				@ob_flush();
			}

		public function parse_private($prv)
			{
				preg_match_all('#(private|to) \[([^\]]+)\]#i', $prv, $match);
				$ret['for'] = $match[2];
				$prv = substr($prv, 9); 
				$prv = preg_replace('#(private|to) \[([^\]]+)\]\s?#i', '', $prv);
				preg_match('#^(([01][0-9]|2[0-3]):[0-5][0-9]) \[([^\]]+)\] (.+)\s([0-9]{1,2})$#i', $prv, $match);
				$ret['time'] = $match[1];
				$ret['from'] = $match[3];
				$ret['mess'] = preg_replace("/ {1,}/ims", ' ', $match[4]);
				$ret['mess'] = trim($ret['mess']);
				$ret['colr'] = $match[5];
				return($ret);
			}


		public function encrypt ($pass, $skey)
			{
				$pass = substr ($pass,0,1) . substr($skey,0,10) . substr ($pass,1) . substr($skey,10);
				$str = strtoupper(sha1($pass));
				$out = 'abcdeabcdeabcdeabcdeabcdeabcdeabcdeabcde';
				$mix = Array (35, 6, 4, 25, 7, 8, 36, 16, 20, 37, 12, 31, 39, 38, 21, 5, 33, 15, 9, 13, 29, 23, 32, 22, 2, 27, 1, 10, 30, 24, 0, 19, 26, 14, 18, 34, 17, 28, 11, 3);
				for ($i=0; $i<40; $i++) {$out[$mix[$i]] = substr($str, $i, 1);}
				return ($out);
// Все что ниже - нахер не надо. Но написано красиво =)
/*
				$pass_len = strlen($pass);
				do {
					$x = Array();
					$local2 = 0;
					$local28 = ($pass_len*8);
					while ($local2<$local28)
						{
							@$x[$local2 >> 5] = $x[$local2 >> 5] | ((ord(substr($pass,$local2/8,1))&255) << (24-($local2%32)));
							$local2 = ($local2+8);
						}
				} while (@!strlen($x));

				$x[$local28 >> 5] = $x[$local28 >> 5] | (128 << (24-($local28%32)));
				$x[((($local28+64) >> 9) << 4)+15] = $local28;
				$local3 = Array(80);
				$local9 = 1732584193;
				$local35 = -271733879;
				$local33 = -1732584194;
				$local34 = 271733878;
				$local36 = -1009589776;
				$local2 = 0;
				$AST_MOD_LOC5 = @strlen($x);
				while ($local2<$AST_MOD_LOC5)
					{
						$pass = $local9;
						$local26 = $local35;
						$local25 = $local33;
						$local23 = $local34;
						$local27 = $local36;
						$local1 = 0;
						while ($local1<80)
							{
								if ($local1<16) {
									@$local3[$local1] = $x[$local2+$local1];
								} else {
									$local12 = (($local3[$local1-3] ^ $local3[$local1-8]) ^ $local3[$local1-14]) ^ $local3[$local1-16];
									$local13 = 1;
									$local3[$local1] = ($local12 << $local13) | $this->bits($local12,(32-$local13));
								}
								$local15 = ($local9 << 5) | $this->bits($local9,27);
								$local5 = $local36;
								$local6 = $local3[$local1];
								$local7 = ($local5 & 65535)+($local6 & 65535);
								$local11 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
								$local18 = (($local1<20) ? 1518500249 : ((($local1<40) ? 1859775393 : ((($local1<60) ? -1894007588 : -899497514)))));
								$local5 = $local11;
								$local6 = $local18;
								$local7 = ($local5 & 65535)+($local6 & 65535);
								$local19 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
							///////////////////////////////
								if ($local1<20) {
									$local4 = ($local35 & $local33) | ((~$local35) & $local34);
								} else if ($local1<40) {
									$local4 = ($local35 ^ $local33) ^ $local34;
								} else if ($local1<60) {
									$local4 = (($local35 & $local33) | ($local35 & $local34)) | ($local33 & $local34);
								} else {
									$local4 = ($local35 ^ $local33) ^ $local34;
								}
								$local5 = $local15;
								$local6 = $local4;
								$local7 = ($local5 & 65535)+($local6 & 65535);
								$local14 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
							///////////////////////////////
								$local5 = $local14;
								$local6 = $local19;
								$local7 = ($local5 & 65535)+($local6 & 65535);
								$local17 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
								$local36 = $local34;
								$local34 = $local33;
								$local33 = ($local35 << 30) | $this->bits($local35,2);
								$local35 = $local9;
								$local9 = $local17;
								$local1++;
							}
						$local5 = $local9;
						$local6 = $pass;
						$local7 = ($local5 & 65535)+($local6 & 65535);
						$local9 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
						$local5 = $local35;
						$local6 = $local26;
						$local7 = ($local5 & 65535)+($local6 & 65535);
						$local35 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
						$local5 = $local33;
						$local6 = $local25;
						$local7 = ($local5 & 65535)+($local6 & 65535);
						$local33 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
						$local5 = $local34;
						$local6 = $local23;
						$local7 = ($local5 & 65535)+($local6 & 65535);
						$local34 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
						$local5 = $local36;
						$local6 = $local27;
						$local7 = ($local5 & 65535)+($local6 & 65535);
						$local36 = (((($local5 >> 16)+($local6 >> 16))+($local7 >> 16)) << 16) | ($local7 & 65535);
						$local2 = $local2+16;
					}
				$local22 = Array($local9, $local35, $local33, $local34, $local36);
				$local29 = Array(40);
				$symbols = '0123456789ABCDEF';
				$local32 = Array(35, 6, 4, 25, 7, 8, 36, 16, 20, 37, 12, 31, 39, 38, 21, 5, 33, 15, 9, 13, 29, 23, 32, 22, 2, 27, 1, 10, 30, 24, 0, 19, 26, 14, 18, 34, 17, 28, 11, 3);
				$local31 = Array();
				$local2 = 0;
				$ast_mod_loc1 = (@strlen($local22)*4);
				while ($local2<$ast_mod_loc1)
					{
						$l=(($local22[$local2 >> 2] >> (((3-($local2%4))*8)+4)) & 15);
						$local16=substr($symbols,$l,1);
						array_push ($local31,$local16);
						$local29[array_shift($local32)]=$local16;
						$l=(($local22[$local2 >> 2] >> ((3-($local2%4))*8)) & 15);
						$local16=substr($symbols,$l,1);
						array_push($local31,$local16);
						$local29[array_shift($local32)]=$local16;
						$local2++;
					}
				$i=0;
				$ast_mod_loc2 = count($local29);
				while ($i<$ast_mod_loc2)
					{
						$ret.=$local29[$i];
						$i++;
					}
				return $ret;
*/
			}

		private function bits($num, $count)
			{
				if ($count > 0)
					{
						$num >>= 1;
						if ($num & 0x80000000) {
							$num &= 0x7fffffff;
						}
						$num >>= $count - 1;
					}
				else
					{
						$num <<= -$count;
					}
				return $num;
			}

		public function __destruct()
			{
				@$this->comm->close_all();
			}
	}
?>