<?php
class mod_core
	{
		function mod_register_hook($func, $perem)
			{
				$list = split(',', $perem);
				foreach($list as $hook)
					{
						if(trim($hook)) $this->hooks[trim($hook)][] = trim($func);
					}
			}

		function mod_register_module($func)
			{
			//	echo $this->home_dir;
				$list = split(',', $func);
				if(count($list)>0){
					foreach($list as $function)
						{
						//	echo $this->home_dir;
							$function = 'module_' . trim($function);
							require_once($this->home_dir.'/modules/' . $function . '.class.php');
							if(function_exists($function))
								{
									$this->functions[$function] = $function;
									@call_user_func($function.'_init', &$this);
									$this->mod_register_hook($function, $this->hk[$function]);
								}
						}
				}
			}

		function mod_process()
			{				if (count($this->functions)>0) {					foreach($this->functions as $function)
						if(function_exists($function . '_process')) call_user_func($function . '_process', &$this);				}				foreach($this->listen as $resp)
					{
						$socket = 				$resp[0];
						$packet = 				$resp[1];
						$ret					= $this->parse_node($packet);
						$param					= $this->comm->base[$socket];
						$this->mod_break		= false;
						if($this->hooks[$ret['tag']])
							foreach($this->hooks[$ret['tag']] as $hook)
								{									if($this->mod_break)
										break;
									else
										call_user_func($hook, &$this, $param['pid'], $param, $ret['tag'], $ret['param'], $packet);
								}
					}
			}
	}
?>
