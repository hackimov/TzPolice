<?php
class comm
	{

		function comm (&$bot)
			{
				$this->bot = &$bot;
				$this->bot_base = &$this->bot->bots;
			}

		function open($address, $port, $pid, $type)
			{
				$address = $this->getIp($address);
				if ($address === false) {
					return false;
				}
				$socket = @fsockopen($address, $port, $errno = null, $errstr = null, 0.5);
				if ($socket !== false)
					{
						$this->base[$socket] = array
						(
							'type'		=> $type,
							'pid' 		=> $pid,
							'socket'	=> $socket
						);
						$this->sockets[$socket] =  $socket;
						stream_set_blocking($socket, false);
					}
				else
					{
						$socket = $this->open($address, $port, $pid, $type);
					}
				return $socket;
			}

		function write($socket, $packet)
			{
				if(!$socket){
					return(0);
					$this->log_it(0, 'Socket failed');
				}
				if(DEBUG == 1) $this->log_it($socket, $this->base[$socket]['type'].' [Outcomming] '.$packet);
				if(fwrite($socket, $send = mb_convert_encoding($packet, 'utf-8', 'cp1251')) === false) {
					$this->restart($this->base[$socket]['pid']);
				}
			}
			
		function write2chat($socket, $txt)
			{				$this->write($socket, '<POST t="' . $txt . '"/>');
			}

		function recv($socket)
			{
				while(substr($response, -1) !== "\0")
					{						while($delay++ < 1000);						$data 		= stream_socket_recvfrom($socket, 4096);
						$response 	.= $data;
						if($data === false || $data === ''){
							return(false);
							$this->log_it(0, 'Socket not response');
						}
					}
				return($response);
			}

		function listen()
			{
				$result    = array();
				$r         = $this->sockets;
				if(stream_select($r, $w = null, $e = null, 0, 60) === false) exit ();
				foreach ($r as $socket)
					{
						$pid = $this->base[$socket]['pid'];
						$response = $this->recv($socket);
						if($response === false)
							{
								$this->restart($pid);
							}
						else
							{
								if ($this->base[$socket]['type'] == 'client') {
									$response = $this->decode1($response);
								} else {
									$response = $this->decode2($response);
								}
								
								$response = mb_convert_encoding($response, 'cp1251', 'utf-8');
								$resp = explode("\0", $response);
								foreach ($resp as $cur_resp) {
									if($cur_resp) {
										$result[] = array($socket, $cur_resp);
										
										if(DEBUG == 1)
											$this->log_it($socket, $this->base[$socket]['type'].' [Incomming] '.$cur_resp);
									}
								}
							}
					}
				foreach($this->bot_base as $pid=>$curr_bot)
					{
						if($this->bot->time_hook(20, $pid) and isset($curr_bot['myparam']))
							{
								$this->write(
									$curr_bot['client_socket'],
									sprintf('<N id1="%s" id2="%s" i1="%s" />',
										$curr_bot['myparam']['ID1'],
										$curr_bot['myparam']['ID2'],
										isset($curr_bot['myparam']['I1']) ? $curr_bot['myparam']['I1'] : '0'
									)
								);
								$this->write($curr_bot['chat_socket'], '<N />');
							}
					}
				return $result;
			}

		function log_it($socket, $in)
			{	
				if($socket===0){
					$fp = fopen ($this->bot->home_dir.'/logs/error_log.txt', 'a+');
					fwrite ($fp, date('d.m.y H:i:m ').$in."\n");
					fclose ($fp);
				}
				if(!$this->bot->logging) return(0);
				if($this->bot->bots[$this->base[$socket]['pid']]['nick']){
					$fp = fopen ($this->bot->home_dir.'/logs/'. $this->bot->bots[$this->base[$socket]['pid']]['nick'].'_'.date('Y-m-d').'_log.txt', 'a+');
					fwrite ($fp, date('d.m.y H:i:m ').$in."\n");
					fclose ($fp);
				}
			}

		function close_all()
			{
				foreach ($this->sockets as $socket)
					{
						close($socket);
					}
				$this->sockets = array();
			}

		function close($socket)
			{
				unset($this->base[$socket]);
				unset($this->sockets[$socket]);
				fclose($socket);
			}

		function restart($pid, $newserv = null)
			{
				$this->close($this->bot_base[$pid]['client_socket']);
				$this->close($this->bot_base[$pid]['chat_socket']);
				$nick 	= $this->bot_base[$pid]['nick'];
				$pass 	= $this->bot_base[$pid]['password'];
				$sv_num = $this->bot_base[$pid]['sv_num'];
				$master = $this->bot_base[$pid]['master'];
				unset($this->bot_base[$pid]);
				$this->bot->add_bot($nick, $pass, $pid, $newserv ? ++$sv_num : null, $master);
			}

		function getIp($address)
			{
				$preg = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}' .
						'(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#';
				if (!preg_match($preg, $address))
					{
						$result = gethostbyname($address);
						if ($result === $address)
							{
								$result = false;
							}
					}
				else
					{
						$result = $address;
					}
				return $result;
			}

		public function decode1($in)
			{

				$a = array ("\x01^","\x01r","\x01W","\x01f","\x01M","\x01N","\x01`","\x01c","\x01j","\x01l","\x01m","\x01n","\x01B","\x01o","\x01p","\x01q","\x01s","\x01t","\x01u","\x01~","\x01","\x01A","\x01C","\x01V","\x01D","\x01E","\x01F","\x01H","\x01I","\x01J","\x01K","\x01L","\x01S","\x01T","\x01U","\x01Y","\x01Z","\x01[","\x01\\","\x01]","\x01O","\x01_","\x01a","\x01b","\x01d","\x01h","\x01e","\x01g","\x01{","\x01v","\x01X","\x01i","\x01k","\x01Q","\x01w","\x01x","\x01y","\x01z","\x01|","\x01}","\x01P","\x01R","\x01G","\x02","\x03","\x04","\x05","\x06","\x07","\x08");
				$b = array ("t=\"2\"/></USER><USER login=\"","<TURN><USER login=\"","></USER><USER login=\"","\"/><MAP v=\"","\" t=\"2\"/><a sf=\"","\" t=\"1\" direct=\"","><a sf=\"0\" t=\"","\" t=\"5\" xy=\"",".1\" slot=\"","\" quality=\"","\" massa=\"1","\" maxquality=\"","><a sf=\"6\" t=\"2\"/><a ","/><a sf=\"6\" t=\"","\" damage=\"S","\" made=\"AR$\" ","\" nskill=\"","\" st=\"G,H\" ","\" type=\"1\"","section=\"0\" damage=\"","\" section=\"","=\"1\" type=\"","protect=\"S"," ODratio=\"1\" loc_time=\"","\"/>\n</O>\n<O id=\"","\"/>\n<O id=\"","level="," min=\""," txt=\"ammo "," txt=\"BankCell Key (copy) #","\" txt=\"Coins\" massa=\"1\" "," cost=\"0\" ",".1\" name=\"b1-g2\" txt=\"Boulder\" massa=\"5\" st=\"G,H\" made=\"AR$\" section=\"0\" damage=\"S2-5\" shot=\"7-1\" nskill=\"4\" OD=\"1\" type=\"9.1\"/>"," psy=\"0\" man=\"1\" maxHP=\""," freeexchange=\"1\" ","\" virus=\"0\" login=\"","\" ne=\",,,,,\" ne2=\",,,,,\" nark=\"0\" gluk=\"0\" ","\" max_count=\"","\" calibre=\"","\" count=\"","\" build_in=\"","\" shot=\"","\" range=\"",".1\" slot=\"A\" name=\"b",".1\" slot=\"B\" name=\"b",".1\" slot=\"C\" name=\"b",".1\" slot=\"D\" name=\"b",".1\" slot=\"E\" name=\"b",".1\" slot=\"F\" name=\"b",".1\" slot=\"GH\" name=\"b","\" slot=\""," psy=\"0\" man=\"3\" maxPsy=\"0\" ODratio=\"1\" img=\"rat\" group=\"2\" battleid=\"",".1\" name=\"b2-s5\" txt=\"Silicon\" massa=\"50\" ",".1\" name=\"b2-s8\" txt=\"Venom\" massa=\"70\" ",".1\" name=\"b2-s4\" txt=\"Organic\" massa=\"30\" ",".1\" name=\"b2-s2\" txt=\"Precious metals\" massa=\"500\" ",".1\" name=\"b2-s7\" txt=\"Gems\" massa=\"80\" ",".1\" name=\"b2-s6\" txt=\"Radioactive materials\" massa=\"800\" ",".1\" name=\"b2-s3\" txt=\"Polymers\" massa=\"30\" ","<BATTLE t=\"45\" t2=\"45\" turn=\"1\" cl=\"0\" ","\" ODratio=\"1\" ","\" p=\"\"/></L><L X=\"","zzzzzz","\"/><a sf=\"",">\n<O id=\"","><O id=\"","      ","00\" "," txt=\""," name=\"b");
//temp logging
$ttt = str_replace($a,$b,$in);
//$fp = fopen ('/home/sites/police/bot_php/logs/infos-log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$ttt." \n\n");
//fclose ($fp);
				return (str_replace($a,$b,$in));
			}
		public function decode2($in)
			{
				$a = array( "\x01", "\x02", "\x03", "\x04", "\x05", "\x06");
				$b = array( '"/>', '<A t="', '<R t="', '<D t="', '<S t="', '<Z t="');
//temp logging
$ttt = str_replace($a,$b,$in);
//$fp = fopen ('/home/sites/police/bot_php/logs/infos-log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$ttt." \n\n");
//fclose ($fp);
				return (str_replace($a,$b,$in));
			}
	}
?>