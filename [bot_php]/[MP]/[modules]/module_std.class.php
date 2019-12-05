<?php
function module_std_init(&$bot)
	{
		$bot->hk['module_std']	= 'KEY, OK, MYPARAM, S, R, ERROR';
	}

function module_std(&$bot, $pid, $param, $tag, $pack, $packet)
	{
		switch ($tag)
			{//////////////////////////////////////LOGIN/////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////
				case 'KEY':
					$pas_enc = $bot->encrypt($bot->bots[$pid]['password'], $pack['S']);
					$send2client = sprintf('<LOGIN l="%s" p="%s" v="%s" v2="%s" lang="ru" />',
											$bot->bots[$pid]['nick'],
											$pas_enc,
											$bot->bots[$pid]['v'],
											$bot->v2
											);
					$bot->comm->log_it($bot->bots[$pid]['client_socket'], 'connecting...');
					break;
					
				case 'OK':
					$bot->ses		= $pack['SES'];
					$send2client	= '<GETME />';
					$send2chat		= sprintf('<CHAT ses="%s" l="%s" />',
											$bot->ses,
											$bot->bots[$pid]['nick']
										);
					$bot->comm->log_it($bot->bots[$pid]['client_socket'], 'connected!');
					$bot->comm->log_it($bot->bots[$pid]['chat_socket'], 'connecting to chat.');
					break;
					
				case 'MYPARAM':
					foreach(split("\n", str_replace('><', ">\n<", $packet)) as $p_tag)
						{							$x = $bot->parse_node($p_tag);
							if($x['tag'] == 'MYPARAM')
								{
									$bot->bots[$pid]['myparam'] = array_merge(($bot->bots[$pid]['myparam'] ? $bot->bots[$pid]['myparam'] : array()), $pack);
							//		if($pack['ID1'])
							//			$send2client = '<CHANGE param="name" txt="' . $bot->owner . '" /><CHANGE param="city" txt="у руля" />';
								}
							elseif($x['tag'] == 'O')
								$bot->bots[$pid]['items'][$x['param']['TXT']] = $x['param'];
						}
					break;
					
///////////////////////////////////////CHAT/////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////
				case 'S':
					$private = $bot->parse_private($pack['T']);
					if(in_array(strtolower($private['from']), $bot->bots[$pid]['master'])
						and in_array($bot->bots[$pid]['nick'], $private['for']))
						{							$commands = split(' ', $private['mess'], 2);
							switch ($commands[0])
	                		{
/*
		                       case 'КОММАНДА':
	                           		Действия;
	                           		$bot->mod_break = true;
		                         break;
*/
		                       case 'restart':
									$bot->comm->log_it($bot->bots[$pid]['client_socket'], 'Restart by: '.$private['from']);
									exit();
								break;

								case 'change_wave':
									$bot->comm->log_it($bot->bots[$pid]['client_socket'], 'Wave changed to '.((int) $commands[1]).' by: '.$private['from'].')');
									$send2client = '<USE w="' . ((int) $commands[1]) . '" radio=" ' . $bot->bots[$pid]['items']['Radio R200']['ID'] . '" />';
									$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $private['from'] . '] Волна изменена на '.((int) $commands[1]).'.');
									$bot->mod_break = true;
									break;
							}
						}
					break;

				case 'R':
					$loc 							= split("\t", $pack['T']);
					$bot->bots[$pid]['location']	= $loc[0];
					break;
//////////////////////////////////////ERRORS////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////
				case 'ERROR':
					$bot->comm->log_it(0, $bot->bots[$pid]['nick'].': ERROR = '.$pack['CODE']);
					switch ($pack['CODE'])
						{
							case '1':
								$bot->comm->restart($pid, true);
								return(0);
								break;
								
							case '2':
								break;
							
							case '3':
								sleep(300);
								break;
								
							case '4':
								return(0);
								break;
								
							case '5':
								file_put_contents($bot->home_dir.'/version.txt', ++$bot->bots[$pid]['v']);
							//	sleep(60);
								break;
								
							case '9':
								sleep(300);
								break;
								
							case '10':
								sleep(300);
							break;
						}
					$bot->comm->restart($pid);
					break;
			}
		if($send2client) $bot->comm->write($bot->bots[$pid]['client_socket'], $send2client);
		if($send2chat) $bot->comm->write($bot->bots[$pid]['chat_socket'], $send2chat);
	}
?>