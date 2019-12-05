<?php
include('module_MP_MySQL.class.php');
function module_MP_init(&$bot)
	{		$bot->hk['module_MP'] 	= 'S, USERPARAM';
		$bot->module_MP['wait'] = array();
		foreach($bot->bots as $num=>$cur_bot)
			if($cur_bot['nick'] == $bot->MP_main)
					$bot->MP_main_id = $num;
	}

function module_MP_process(&$bot)
	{
		foreach($bot->module_MP['wait'] as $from=>$param)
			if(((time() - $param['time']) >= 60) and $param['time'] > 0)
				{
					$bot->comm->write2chat($bot->bots[$param['pid']]['chat_socket'], 'private [' . $from . '] Извините, все сотрудники заняты. :sad:');
					unset($bot->module_MP['wait'][$from]);
				}
	}

function module_MP(&$bot, $pid, $param, $tag, $pack, $packet)
	{
		switch ($tag)
			{
				case 'S':
					$private  = $bot->parse_private($pack['T']);
				
				// Личный приват
					if(in_array($bot->bots[$pid]['nick'], $private['for'])) $to['me'] = 1;
					else $to['me'] = 0;
				
				// Клан приват
					if(in_array('clan', $private['for'])) $to['clan'] = 1;
					else $to['clan'] = 0;
				
				// Радио приват
					if(in_array('radio', $private['for'])) $to['radio'] = 1;
					else $to['radio'] = 0;
				
				// Альянс приват
					if(in_array('alliance', $private['for'])) $to['alliance'] = 1;
					else $to['alliance'] = 0;
					
					$from   = trim($private['from']);
					$mess   = trim($private['mess']);

//$fp = fopen ('/home/sites/police/bot_php/logs/log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$from." ".$mess."\n");
//fclose ($fp);
					
					if($to['radio'] and ($mess == '+' or $mess == '++' or $mess == '+++')
				//		and $bot->module_MP['wait'][$from]['pid'] == $pid
						)
						{
						foreach($private['for'] as $pers)
								if($bot->module_MP['wait'][$pers])
									{
										confirm_MP(&$bot, $pers, $bot->module_MP['wait'][$pers]['bid'], $from);
										$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $pers . '] private [' . $from . '] Ваша заявка принята данным сотрудником. :cop:');
										unset($bot->module_MP['wait'][$pers]);
										return(0);
									}
						}
					elseif($to['me'] && !$to['radio'] && !$to['alliance'] && !$to['clan'] && preg_match('#log (\d+)#i', $mess, $log_reg))
						{
							if(!battle_result(&$bot, $log_reg[1], $from))
								$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Вы не принимали заявку по данному бою.');
							else{
								$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Отчет принят. :cop:');
							}
						}
					elseif($to['me'] && !$to['radio'] && !$to['alliance'] && !$to['clan'] && preg_match('#\-\-\- (.+):(.*)#ims', $mess, $log_reg))
						{
							$deny_name = battle_result_deny(&$bot, $log_reg, $from);
							if(!$deny_name)
								$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Ошибка при отказе от заявки.');
							else{
								$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $deny_name . '] private [' . $from . '] Cотрудник отказался от Вашей заявки.');
							}
						}
					elseif($to['me'])
						{
							if(preg_match('#(sos|help|помогите) (Location \[([0-9\-]+)/([0-9\-]+)\](.*))#i' , $mess, $log_reg))
								{	
								//	print_r($log_reg);
//$fp = fopen ('/home/sites/police/bot_php/logs/log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$from." ".$mess."\n");
//fclose ($fp);
									if (strpos($log_reg[2], '|') === false)
										{
											$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Извините, локация над шахтой не охраняется');
										
										}
									else
										{
//$fp = fopen ('/home/sites/police/bot_php/logs/-log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$from." REQUESTED INFO\n");
//fclose ($fp);
											$send2client .= '<GETINFO login="' . $from . '"/>';
											$bot->module_MP['wait'][$from] = $mess;
										}
								}
							else
								$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Ошибка! Правильный пример запроса: sos ' . $bot->bots[$pid]['location']);
						}
				break;

				case 'USERPARAM':
					$from = $pack['LOGIN'];
//$fp = fopen ('/home/sites/police/bot_php/logs/-log.txt', 'a+');
//fwrite ($fp, date('d.m.y H:i:m ').$from." RECEIVED INFO\n");
//fclose ($fp);
					if($pack['X'] == $bot->bots[$pid]['myparam']['X'] and $pack['Y'] == $bot->bots[$pid]['myparam']['Y'])
						{							if(in_grey_list(&$bot, $from))
								{
									$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Извините, но Вам отказанно в сервисе.');
									unset($bot->module_MP['wait'][$from]);
									return(0);
								}
							if($pack['BATTLEID'] <= 999)
								{
									$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Извините, но Вы вне боя.');
									unset($bot->module_MP['wait'][$from]);
									return(0);
								}							if($pack['CLAN'])
								{									$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Извините, клановые персонажи не обслуживаются.');
									unset($bot->module_MP['wait'][$from]);
									return(0);
								}
							if($pack['UNIQUEST'] == '1' or $pack['PRO'] == '1')
								{
									$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Извините, корсары и недокорсы не обслуживаются.');
									unset($bot->module_MP['wait'][$from]);
									return(0);
								}
							$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Идёт поиск сотрудника МП... Ждите...');
							$bot->comm->write2chat($bot->bots[($bot->bots[$pid]['items']['Radio R200']) ? $pid : $bot->MP_main_id]['chat_socket'], 'private [radio] private [' . $from . '] >>> ' . $bot->module_MP['wait'][$from]);
							$bot->module_MP['wait'][$from] = array(
																	'from' 	=> $from,
																	'time' 	=> time(),
																	'pid'	=> ($bot->bots[$pid]['items']['Radio R200']) ? $pid : $bot->MP_main_id,
																	'text' 	=> $bot->module_MP['wait'][$from],
																	'bid'	=> $pack['BATTLEID']
																);
							add_battle(&$bot, $pack['BATTLEID'], $from);
						}
					else
						{
							$bot->comm->write2chat($bot->bots[$pid]['chat_socket'], 'private [' . $from . '] Этот терминал не обслуживает данную локацию.');
						}
				break;
			}
		if($send2client) $bot->comm->write($bot->bots[$pid]['client_socket'], $send2client);
	}
////////////////////////////////////////////////////////////////////////////////
?>