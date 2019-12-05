<?php
if (str_replace(".",NULL,phpversion()) < 436) {exit("You should update PHP to 4.3.6.");}
if (!extension_loaded('sockets'))
{
 if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {dl('php_sockets.dll');}
 else {dl('sockets.so');}
}

if (!extension_loaded('sockets')) {exit("Sockets extension needed!");}

class ICQclient
{
 var $socket,$server,$port,$connected,
	 $uin,$pass,$logged,
	 $dbgstream,$errorstream,$lasterror,$XORseq,
	 $const,$recvknownSNACs,$inLastN,$inLastCmd,$inLastVar,$srv,$misc,$status,
	 $BOS,$timeout,$blocking,$requests,$events,$settings,$inturn,
	 $errlevel,$selfonlineinfo;

 function ICQclient($uin="",$pass="",$server="login.icq.com",$port=5190)
 {
  $this->XORseq = "\xf3\x26\x81\xc4\x39\x86\xdb\x92";
  $this->const = array(
   "STATUSFLAG_WEBAWARE"	=> 0x0001,	"STATUSFLAG_SHOWIP"		=> 0x0002,
   "STATUSFLAG_BIRTHDAY"	=> 0x0008,	"STATUSFLAG_WEBFRONT"	=> 0x0020,
   "STATUSFLAG_DCDISABLED"	=> 0x0100,	"STATUSFLAG_DCAUTH"		=> 0x1000,
   "STATUSFLAG_DCCONT"		=> 0x2000,

   "STATUS_ONLINE"			=> 0x0000,
   "STATUS_AWAY"			=> 0x0001,	"STATUS_DND"			=> 0x0002,
   "STATUS_NA"				=> 0x0004,	"STATUS_OCCUPIED"		=> 0x0010,
   "STATUS_FREE4CHAT"		=> 0x0020,	"STATUS_INVISIBLE"		=> 0x0100,

   "DC_DISABLED"			=> 0x0000,	"DC_HTTPS"				=> 0x0001,
   "DC_SOCKS"				=> 0x0002,	"DC_NORMAL"       		=> 0x0004,
   "DC_WEB"					=> 0x0006,

   "MTYPE_PLAIN"			=> 0x01,	"MTYPE_CHAT"			=> 0x02,
   "MTYPE_FILEREQ"			=> 0x03,	"MTYPE_URL"				=> 0x04,
   "MTYPE_AUTHREQ"			=> 0x06,	"MTYPE_AUTHDENY"		=> 0x07,
   "MTYPE_AUTHOK"			=> 0x08,	"MTYPE_SERVER"			=> 0x09,
   "MTYPE_ADDED"			=> 0x0C,	"MTYPE_WWP"				=> 0x0D,
   "MTYPE_EEXPRESS"			=> 0x0E,	"MTYPE_CONTACTS"		=> 0x13,
   "MTYPE_PLUGIN"			=> 0x1A,	"MTYPE_AUTOAWAY"		=> 0xE8,
   "MTYPE_AUTOBUSY"			=> 0xE9,	"MTYPE_AUTONA"			=> 0xEA,
   "MTYPE_AUTODND"			=> 0xEB,	"MTYPE_AUTOF4C"			=> 0xEC,

   "MFLAG_NORMAL"			=> 0x01,	"MFLAG_AUTO"			=> 0x03,
   "MFLAG_MULTI"			=> 0x80,

   "SRV_ERR" => array(
     "xx" => array(
		0x01 => "ERR_SNAC_INVALID",			0x02 => "ERR_SRV_RATE_EXCEED",
		0x03 => "ERR_CLI_RATE_EXCEED",		0x04 => "ERR_RECP_NOT_LOGGED",
		0x05 => "ERR_SRVC_UN_AVAILABLE",	0x06 => "ERR_SRVC_NOT_DEFINED",
		0x07 => "ERR_SNAC_OBSOLETE",		0x08 => "ERR_SRV_NOT_SUPP",
		0x09 => "ERR_CLI_NOT_SUPP",			0x0A => "ERR_CLI_REFUSED",
		0x0B => "ERR_REPLY_TOO_BIG",		0x0C => "ERR_RESPS_LOST",
		0x0D => "ERR_REQ_DENIED",			0x0E => "ERR_SNAC_FORMAT",
		0x0F => "ERR_INSUFF_RIGHTS",		0x10 => "ERR_SNAC_LOCAL_DENY",
		0x11 => "ERR_SENDER_TOO_EVIL",  	0x12 => "ERR_RECVER_TOO_EVIL",
		0x13 => "ERR_USER_TEMP_UNAVAIL",	0x0E => "ERR_SNAC_FORMAT",
		0x0F => "ERR_INSUFF_RIGHTS",		0x14 => "ERR_NO_MATCH",
		0x15 => "ERR_LIST_OVERFLOW",		0x16 => "ERR_REQ_AMBIQUOUS",
		0x17 => "ERR_SRV_QUEUE_FULL",	    0x18 => "ERR_NOT_WHILE"
	 ),
	 0x04 => array(
		0x04 => "ERR_TRY_SEND_TO_OFFLINE",
		0x09 => "ERR_MSG_NOT_SUPP",
		0x0E => "ERR_MSG_FORMAT_INVALID",
		0x10 => "ERR_RECEIVERorSENDER_BLOCKED"
	 ),
	 "DISCONNECT_REASONS" => array(
		0x01 => "ERR_MULTIPLE_LOGINS"
	 )
	)
  );
  $this->recvknownSNACs = array(
   "xx.".(0x01) 	   => "SRV_ERROR",
   (0x01).".".(0x03) => "SRV_FAMILIES",
   (0x01).".".(0x07) => "SRV_RATE_LIMIT_INFO",
   (0x01).".".(0x0F) => "SRV_ONLINE_INFO",
   (0x01).".".(0x12) => "SRV_MIGRATION",
   (0x01).".".(0x13) => "SRV_MOTD",
   (0x01).".".(0x15) => "SRV_WELL_KNOWN_URLS",
   (0x01).".".(0x18) => "SRV_FAMILIES2",
   (0x01).".".(0x21) => "SRV_EXT_STATUS",
   (0x03).".".(0x0B) => "SRV_USER_ONLINE",
   (0x03).".".(0x0C) => "SRV_USER_OFFLINE",
   (0x04).".".(0x07) => "SRV_CLIENT_ICBM",
   (0x04).".".(0x0C) => "SRV_MSG_ACK",
   (0x04).".".(0x12) => "SRV_MY_OFFLINE",
   (0x13).".".(0x15) => "SRV_SSI_FUTURExAUTHxGRANTED",
   (0x13).".".(0x19) => "SRV_SSI_AUTH_REQUEST",
   (0x13).".".(0x1C) => "SRV_SSI_YOU_WERE_ADDED",
   (0x13).".".(0x1B) => "SRV_SSI_AUTH_REPLY",
   (0x15).".".(0x03) => "SRV_META",
   (0x17).".".(0x05) => "SRV_NEW_UIN"
  );
  $this->events = array(
   "onunwanteddisconnect" => "\$this->connect();",
   "onmessagerecv" => NULL,
   "onlogin" => NULL
  );
  $this->inturn = array();
  $this->misc = array();
  $this->settings = array();
  $this->settings["autorecvofflinemsgs"] = 2;
  $this->settings["client"] = array(
   "name"		=> "c99phpicq",
   "country"	=> "ru",
   "language"	=> "ru",
   "build"		=> 1,
   "id"			=> 234,
   "major"		=> 0,
   "minor"		=> 2,
   "lesser"		=> 1
  );
  $this->settings["DC"] = array(
   "internal_ip"	=> "127.0.0.1",
   "port" 			=> "12345",
   "type"			=> $this->const["DC_DISABLED"]
  );
  $this->settings["FAMILIES"] = array(
    1   => 3,   19 => 4,
    2   => 1,   3  => 1,
    21  => 1,   4  => 1,
    6   => 1,   9  => 1,
    10  => 1
  );
  $this->settings["perms"] = array(
    "auth" => FALSE,
    "webaware" => TRUE,
    "DC" => 2 // 0-any, 1-contact, 2-authorization
  );
  $this->srv["outseq"] = rand(0x0001,0x8000);
  $this->error_reporting(999);
  $this->setstatus($this->const["STATUS_ONLINE"]);
  $this->server = $server;
  $this->port = $port;
  $this->uin = $uin;
  $this->pass = $pass;
 }
 function socket_wait($sec=NULL,$usec=NULL)
 {
  if (!$this->socket) {$this->_error("socket","socket failed (".socket_last_error()." - ".socket_strerror(socket_last_error()).")",__FILE__,__LINE__); return FALSE;}
  else
  {
   if ($sec === NULL) {list($sec,$usec) = explode(" ",$this->timeout);}
   $sec = intval($sec);
   $usec = intval($usec);
   $read = array($this->socket);
   $ret = @socket_select($read,$write = NULL,$except = NULL,$sec,$usec);
   if ($ret === FALSE) {return FALSE;}
   elseif ($ret) {return $ret;}
   else {return 0;}
  }
 }
 function socket_connect($host,$port)
 {
  $this->socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
  socket_connect($this->socket,$host,$port);
  if (!$this->socket) {return FALSE;}
  else
  {
   socket_set_nonblock($this->socket);
   return $this->socket;
  }
 }
 function connect($server="",$port="")
 {
  if (!empty($server)) {$this->server = $server;}
  if (!empty($port)) {$this->port = $port;}
  $this->socket_connect($this->server,$this->port);
  if (!$this->socket) {$this->_error("connect","connection to ".$this->server.":".$this->port." has failed: ".$errno." - ".$errstr,__FILE__,__LINE__); return FALSE;}
  else
  {
   $this->set_timeout();
   list($channel,$data) = $this->recv();
   if ($channel != 0x01 or $data != _dword(0x01)) {$this->_error("connect","connection to ".$this->server.":".$this->port." has failed: SRV_HELLO isn't recieved",__FILE__,__LINE__); $this->disconnect(); return FALSE;}
   else
   {
    $this->connected = TRUE;
    return TRUE;
   }
  }
 }
 function _connect_migration($server,$cookie)
 {
  if (!empty($server)) {$this->BOS = explode(":", $server); list($server,$port) = $this->BOS;}
  $this->socket_connect($server,$port);
  if (!$this->socket) {$this->_error("connect_migration","migration to ".$server.":".$port." has failed: ".$errno." - ".$errstr,__FILE__,__LINE__); return FALSE;}
  else
  {
   $this->set_timeout();
   list($channel,$data) = $this->recv();
   if ($channel != 0x01 or $data != _dword(0x01)) {$this->_error("_connect_migration","connection to ".$this->server.":".$this->port." has failed: SRV_HELLO isn't recieved",__FILE__,__LINE__); $this->disconnect(); return FALSE;}
   else
   {
    $this->send("CLI_COOKIE",$cookie);
    $this->listen();
    $this->connected = TRUE;
    return TRUE;
   }
  }
 }
 function set_timeout($timeout=NULL)
 {
  if ($timeout === NULL) {$timeout = "0 500000";}
  $this->timeout = $timeout;
 }
 function read ($len=0,$sec=NULL,$usec=NULL)
 {
  @socket_clear_error($this->socket);
  $wait = $this->socket_wait($sec,$usec);
  $waiterr = @socket_last_error($this->socket);
  $recv = @socket_recv($this->socket,$data,$len,0);
  if ($wait === FALSE) {return FALSE;}
  elseif ($wait === 1)
  {
   if ($recv === FALSE)
   {
    $this->_error("read","connection closed by remote host or communication error (".socket_last_error()." - ".socket_strerror(socket_last_error()).")",__FILE__,__LINE__);
    $this->disconnect();
   }
  }
  {
   if ($waiterr)
   {
    $this->_error("read","connection failed (".socket_last_error()." - ".socket_strerror(socket_last_error()).")",__FILE__,__LINE__);
    @socket_clear_error($this->socket);
   }
   else
   {
    if ($recv < 0) {$this->_error("read","socket_recv() failed (".socket_last_error()." - ".socket_strerror(socket_last_error()).")",__FILE__,__LINE__);}
    @socket_clear_error($this->socket);
    if ($recv === FALSE) {$datalen = 0; $data = NULL;}
    elseif (is_numeric($recv)) {$datalen = $recv;}
    if ($datalen !== strlen($data)) {$this->_error("read","recieved data length (".$datalen.") !== real data length (".strlen($data).")",__FILE__,__LINE__);}
    if ($datalen !== $len and $datalen !== 0) {$this->_error("read","recieved data length (".$datalen.") !== needed data length (".$len.") ",__FILE__,__LINE__);}
    return $data;
   }
  }
 }
 function connect_pass()
 {
  if ($this->socket) {return TRUE;}
  else
  {
   if (!$this->connected) {$this->connect();}
   else {$this->exec_event("onunwanteddisconnect");}
   return is_resource($this->socket);
  }
 }
 function login_pass() {$this->connect_pass(); return $this->socket and $this->logged;}
 function login($uin="",$pass="")
 {
  if ($uin) {$this->uin = $uin;}
  if ($pass !== "") {$this->pass = $pass;}
  if (!$this->connect_pass()) {return FALSE;}
  else
  {
   $this->srv["outseq"] = rand(0x0001,0x8000);
   $this->send("CLI_LOGIN");
   list($channel,$data) = $this->recv();
   if ($channel == 0x04) {$TLV = $this->TLV2array($data);}
   else {$TLV = array();}
   if (isset($TLV[0x05]) and isset($TLV[0x06]))
   {
    if (!$this->_connect_migration($TLV[0x05],$TLV[0x06])) {return FALSE;}
    else
    {
     $this->logged = TRUE;
     $this->updatestatus();
     //$this->send("CLI_FAMILIES");
     $this->send("CLI_META","USER_INFO","SET_PERMS");
     if ($this->settings["autorecvofflinemsgs"]) {var_dump($this->recv_offline_msgs($this->settings["autorecvofflinemsgs"] == 2));}
     $this->send("CLI_READY");
     return TRUE;
    }
   }
   else
   {
    if (@strpos($TLV[0x04],"MISMATCH_PASSWD")) {$reason = "MISMATCH_PASSWD";}
    elseif (@strpos($TLV[0x04],"www.aol.com?ccode=us&lang=en") or !isset($TLV[0x04])) {$reason = "TOO_FAST";}
    else {$reason = "unknown";}
    return TRUE;
    $this->_error("login","authorization failed: ".$reason,__FILE__,__LINE__);
    return FALSE;
    $this->disconnect();
   }
  }
 }
 function setstatus($status)
 {
  if (!empty($status)) {$this->status = $status;}
  return TRUE;
 }
 function setstatusflags($flags)
 {
  if (!is_array($flags)) {$flags = func_get_args();}
  $this->settings["STATUS_FLAGS"] = $flags;
  return TRUE;
 }
 function updatestatus()
 {
  if (!$this->connect_pass()) {return FALSE;}
  else {$this->send("CLI_SETxSTATUS"); return TRUE;}
 }
 function message_send($uin,$message,$type=NULL,$flag=NULL,$confirm=FALSE,$store=TRUE)
 {
  $this->login_pass();
  if ($this->logged and $this->is_uin($uin))
  {
   if (!$type) {$this->send("CLI_SEND_ICBM_CH1",$uin,$message);}
   else {$this->send("CLI_SEND_ICBM_CH4",$uin,$message,$type,$flag,$confirm,$store);}
   return TRUE;
  }
  else {return FALSE;}
 }
 function chkinvisible($uin)
 {
  $this->login_pass();
  if ($this->logged and $this->is_uin($uin))
  {
   $this->send("CLI_CHKINVISIBLE_ICBM",$uin);
   $errrep = $this->errlevel;
   $this->error_reporting(0);

   while($this->socket)
   {
    $this->listen(TRUE);
    if ($this->inLastCmd == "SRV_ERROR")
    {
     if ($this->inLastVar["errfamily"] == 0x04 // ICBM
     and $this->inLastVar["errcode"] == 0x09) // ERR_MSG_NOT_SUPP
     {return 1; $this->unsetinturn($this->inLastN); break;}
    }
    elseif ($this->inLastCmd == "SRV_MY_OFFLINE" ||
			$this->inLastCmd == "SRV_MSG_ACK" and
			$this->inLastVar["uin"] == $uin) {return 0; $this->unsetinturn($this->inLastN); break;}
   }
   $this->error_reporting($errrep);
   return FALSE;
  }
  else {return FALSE;}
 }
 function recv_offline_msgs($REMOVE=TRUE)
 {
  $messages = array();
  $this->login_pass();
  if ($this->logged)
  {
   $this->send("CLI_META","OFFLINE_MESS_REQ");
   while($this->socket)
   {
    $this->listen(TRUE);
    if ($this->inLastCmd == "SRV_META")
    {
     if ($this->inLastVar["type"] == "OFFLINE_MESSAGE") {$messages[] = array_slice($this->inLastVar,1);}
     if ($this->inLastVar["type"] == "OFFLINE_MSGS_DONE") {break;}
     $this->unsetinturn($this->inLastN);
    }
    elseif ($this->inLastCmd == "SRV_ERROR") {return FALSE; break; $this->unsetinturn($this->inLastN);}
   }
   if ($REMOVE) {$this->send("CLI_META","DELETE_OFFLINE_MESS_REQ");}
   return $messages;
  }
  else {return FALSE;}
 }
 function search_whitepages($info=array())
 {
  $result = array();
  $this->login_pass();
  if ($this->logged)
  {
   $this->send("CLI_META","USER_INFO","WHITEPAGES_TLV",$info);
   $seq = $this->srv["metaseq"];
   while($this->socket)
   {
    $this->listen(TRUE);
    if ($this->inLastCmd == "SRV_META")
    {
     $subtype = $this->inLastVar["subtype"];
     if ($this->inLastVar["type"] == "INFO" and $subtype == "USER_FOUND")
     {
      $vars = $this->inLastVar;
      unset($vars["type"],$vars["subtype"],$vars["metaseq"],$vars["SNACflags"]);
      if (!$this->inLastVar["success"]) {return FALSE; break;}
      else
      {
       $result[] = $vars;
       $l = count($result)-1;
       if ($l < 0) {$l = 0;}
       if ($this->inLastVar["metaseq"] > $seq) {$this->unsetinturn($this->inLastN);}

		   if ($this->inLastVar["lastitem"] == FALSE) {continue;}
       elseif ($this->inLastVar["lastitem"] == TRUE)
       {
        $result["uinsleft"] = $result[$l]["uinsleft"];
        unset($result[$l]["uinsleft"]);
        break;
       }
       else {$this->_error("search_whitepages","can't recieve SNAC-flags, 0x".dechex($this->inLastVar["SNACflags"])." is invalid!");}
      }
     }
    }
   }
   if (count($result) == 0) {return FALSE;}
   else
   {
    $this->inLastVar["refresh"] = getmicrotime();
    return $result;
   }
  }
  else {return FALSE;}
 }
 function getinfo($uin,$cache=TRUE,$self=FALSE)
 {
  $info = array();
  $this->login_pass();
  if ($this->logged)
  {
   if ($self) {$this->send("CLI_META","USER_INFO","REQ_SELF_FULLINFO",array("uin" => $uin));}
   else {$this->send("CLI_META","USER_INFO","REQ_FULLINFO",array("uin" => $uin));}
   $seq = $this->srv["metaseq"];
   while($this->socket)
   {
    $this->listen(TRUE);
    if ($this->inLastCmd == "SRV_META")
    {
     if ($this->inLastVar["type"] == "INFO")
     {
      $subtype = $this->inLastVar["subtype"];
      $vars = $this->inLastVar;
      unset($vars["type"],$vars["subtype"],$vars["metaseq"],$vars["SNACflags"]);
      if (!empty($subtype) and @$this->inLastVar["success"]) {$info[$uin][$subtype] = $vars;}
      if ($this->inLastVar["metaseq"] > $seq) {$this->unsetinturn($this->inLastN);}

		  if ($this->inLastVar["SNACflags"] == 0x01) {continue;}
      elseif ($this->inLastVar["SNACflags"] == 0x00) {break;}

      else {$this->_error("getinfo","can't recieve SNAC-flags, 0x".dechex($this->inLastVar["SNACflags"])." is invalid!");}
     }
    }
   }
   if (count($info) == 0) {return FALSE;}
   else
   {
    $this->inLastVar["refresh"] = getmicrotime();
    if ($self and $cache) {$this->misc["selfinfo"] = $info[$uin];}
    elseif ($cache) {@$this->misc["info"] = array_merge($this->misc["info"],$info);}
    return $info;
   }
  }
  else {return FALSE;}
 }
 function uinreg($password)
 {
  $this->connect_pass();
  if (!$this->connected or !$this->is_password($password)) {return FALSE;}
  else
  {
   $this->send("CLI_REGISTRATION_REQUEST",$password);
   $errrep = $this->errlevel;
   //$this->error_reporting(0);
   sleep(5); // Delay
   while($this->socket)
   {
    $this->listen(TRUE);
    if ($this->inLastCmd == "SRV_ERROR")
    {
     if ($this->inLastVar["errfamily"] == 0x17) // Authorization/registration service
     {return FALSE; $this->unsetinturn($this->inLastN); break;}
    }
    elseif ($this->inLastCmd == "SRV_NEW_UIN") {return array($this->inLastVar["uin"],$password); break;}
   }
   //$this->error_reporting($errrep);
   return FALSE;
  }
 }
 function listen($tointurn=FALSE)
 {
  if (!$this->socket) {return FALSE;}
  else
  {
   $this->inLastVar = FALSE;
   $this->inLastCmd = NULL;
   $c = count($this->inturn);
   if ($c > 0 and !$tointurn) {list($this->inLastN,$this->inLastCmd,$this->inLastVar) = array_shift($this->inturn);}
   else
   {
    $packet = $this->recv();
    if (!$packet) {return FALSE;}
    else
    {
     list ($channel,$data) = $packet;
     if ($channel == 0x02) {$this->_SNAC_parse($data);}
     if ($channel == 0x05) {return TRUE;}
     elseif (!$tointurn) {if ($this->ismessage()) {$this->exec_event("onmessagerecv");} return TRUE;}
     else {$this->inturn[] = array($this->inLastN,$this->inLastCmd,$this->inLastVar); return TRUE;}
    }
   }
  }
 }
 function unsetinturn($id) {unset($this->inturn[$id]); return;}
 function _FLAP_parse($h)
 {
  if (ord($h{0}) !== 0x2A) {$this->_error("_FLAP_parse","ICQ protocol sync error: bad first byte in FLAP",__FILE__,__LINE__); return FALSE;}
  $channel = ord($h{1});
  $seq = ord($h{3})+(ord($h{2})<<8);
  if (!isset($this->srv["inseq"])) {$this->srv["inseq"] = $seq;}
  if ($seq != $this->srv["inseq"]) {$this->_error("_FLAP_parse","ICQ protocol sync error: my seq ".$seq." != ".$this->srv["inseq"],__FILE__,__LINE__); return FALSE;}
  else {$this->_update_sequence($this->srv["inseq"]);}
  $len = ord($h{5})+(ord($h{4})<<8);
  return array($channel,$len);
 }
 function ismessage() {return @$this->inLastCmd == "SRV_CLIENT_ICBM";}
 function error_reporting($level=NULL) {if ($level === NULL) {return $this->errlevel;} else {$this->errlevel = intval($level);}}
 function recv()
 {
  $h = "";
  if (!$this->socket) {return FALSE;}
  else {$h = $this->read(6);}
  if (strlen($h) != 6) {return FALSE;}
  {
   list($channel,$len) = $this->_FLAP_parse($h);
   $data = $this->read($len);
   if ($channel == 0x04) {$this->disconnect(!!$this->socket,$data);}
   return array($channel,$data);
  }
 }
 function send($SNACtype,$SNAC=FALSE)
 {
  if (!$this->socket) {return FALSE;}
  else
  {
   if ($SNACtype != "data") {$args = func_get_args(); $SNAC = $this->_SNAC_gen($SNACtype,array_slice($args,1));}
   if (!is_array($SNAC)) {return FALSE;}
   else
   {
    list($channel,$SNAC) = $SNAC;
    $FLAP = $this->_FLAP_gen($channel,strlen($SNAC));
    $packet = $FLAP.$SNAC;
    socket_write($this->socket,$packet,strlen($packet));
    $this->_update_sequence($this->srv["outseq"]);
   }
  }
 }
 function _FLAP_gen($channel,$len)
 {
  return
   _byte(0x2A).
   _byte($channel).
   _word($this->srv["outseq"]).
   _word($len);
 }
 function _SNAC_gen_header($family,$subfamily)
 {
  return
    _word($family).
    _word($subfamily).
    _word(0x0000).
    _dword($this->_newreqid("REQUEST",$family,$subfamily));
 }
 function _SNAC_gen($cmd,$args=array())
 {
  $channel = 0x02;
  if ($cmd == "CLI_HELLO")
  {
   $data = _dword(0x01);
   $channel = 0x01;
  }
  elseif ($cmd == "CLI_LOGIN")
  {
   $array = array( 	// TLV-data
     0x01 => $this->uin,
     0x02 => $this->XORencrypt($this->pass),
     0x03 => $this->settings["client"]["name"],
     0x16 => _word($this->settings["client"]["id"]),
     0x17 => _word($this->settings["client"]["major"]),
     0x18 => _word($this->settings["client"]["minor"]),
     0x19 => _word($this->settings["client"]["lesser"]),
     0x1A => _word($this->settings["client"]["build"]),
     0x14 => _dword(85),
     0x0F => $this->settings["client"]["language"],
     0x0E => $this->settings["client"]["country"]
    );
   $data =
    _dword(0x01).
    $this->array2TLV($array)
   ;
   $channel = 0x01;
  }
  elseif ($cmd == "CLI_COOKIE")
  {
   $data =
    _dword(0x01).
    $this->array2TLV(
     array(0x06 => $args[0])
    );
   $channel = 0x01;
  }
  elseif ($cmd == "CLI_READY")
  {
   $data =
    _word(0x01).
    _word(0x02).
    i2b(5,0x00).

    _word(0x0200).
    _word(0x0100).
    _word(0x0301).

    _word(0x1002).
    _word(0x8A00).
    _word(0x0200).
    "\x01\x01\x01\x02\x8A\x00\x03\x00\x01\x01\x10".
    "\x02\x8A\x00\x15\x00\x01\x01\x10\x02\x8A\x00\x04\x00\x01\x01\x10".
    "\x02\x8A\x00\x06\x00\x01\x01\x10\x02\x8A\x00\x09\x00\x01\x01\x10".
    "\x02\x8A\x00\x0A\x00\x01\x01\x10\x02\x8A";
  }
  elseif ($cmd == "CLI_RATES_REQUEST") {$data = $this->_SNAC_gen_header(0x01,0x06);}
  elseif ($cmd == "CLI_KEEPALIVE") {$channel = 0x05; $data = NULL;}
  elseif ($cmd == "CLI_REQ_SELF_ONLINEINFO") {$data = $this->_SNAC_gen_header(0x01,0x0E); $data = NULL;}
  elseif ($cmd == "CLI_FAMILIES")
  {
   $data = $this->_SNAC_gen_header(0x01,0x17);
   foreach($this->settings["FAMILIES"] as $k=>$v) {$data .= _word($k)._word($v);}
  }
  elseif ($cmd == "CLI_SETxSTATUS")
  {
   $flags = 0;
   foreach($this->settings["STATUS_FLAGS"] as $v) {$flags |= $v;}
   $TLV = array();
   $TLV[0x06] =
    _word($flags).
    _word($this->status);
   if (!in_array($this->const["STATUSFLAG_DCDISABLED"],$this->settings["STATUS_FLAGS"]))
   {
    $TLV[0x0C] =
     _dword(ip2long($this->settings["DC"]["internal_ip"])).
     _dword($this->settings["DC"]["port"]).
     _byte($this->settings["DC"]["type"]);
   }
   $data = $this->_SNAC_gen_header(0x01,0x1E).$this->array2TLV($TLV);
  }
  elseif ($cmd == "CLI_SETxIDLExTIME")
  {
   $IDLE_SECS = intval(@$args[0]);
   $data = $this->_SNAC_gen_header(0x01,0x11)._dword($IDLE_SECS);
  }

  elseif ($cmd == "CLI_BUDDYLIST_ADD")
  {
   $BUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x03,0x04).$this->_gen_uinlist($BUINS);
  }
  elseif ($cmd == "CLI_BUDDYLIST_REMOVE")
  {
   $BUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x03,0x05).$this->_gen_uinlist($BUINS);
  }

  // ICBM service (04)
  elseif ($cmd == "CLI_SEND_ICBM_CH1")
  {
   $MUIN = @$args[0];
   $MTEXT = @$args[1];
   $TLV = array(
    0x02 =>
     _byte(0x05).
     _byte(0x01).
     _LV(0x01,2).
     _byte(0x01).
     _byte(0x01).
     _LV(
      _word(0x00).
      _word(0x00).
      $MTEXT
      ,2
     )
   );
   $data =
    $this->_SNAC_gen_header(0x04,0x06).
    _qword($this->_newreqid($cmd,$MUIN)).
    _word(0x01).
    _LV($MUIN).
    $this->array2TLV($TLV);
  }
  elseif ($cmd == "CLI_SEND_ICBM_CH4")
  {
   $MUIN = @$args[0];
   $MTEXT = @$args[1];
   $MTYPE = @$args[2];
   $MFLAG = @$args[3];
   $MCONFIRM = @!!$args[4];
   $MSTORE = @!!$args[5];
   if (empty($MTYPE)) {$MTYPE = $this->const["MTYPE_PLAIN"];}
   if (empty($MFLAG)) {$MFLAG = $this->const["MFLAG_NORMAL"];}
   $TLV = array(
    0x05 =>
     _dwordl($this->uin).
     _byte($MTYPE).
     _byte($MFLAG).
     _LVnull($MTEXT)
   );
   if ($MCONFIRM) {$TLV[0x03] = NULL;}
   if ($MSTORE) {$TLV[0x06] = NULL;}
   $data =
    $this->_SNAC_gen_header(0x04,0x06).
    _qword($this->_newreqid($cmd,$MUIN)).
    _word(0x04).
    _LV($MUIN).
    $this->array2TLV($TLV);
  }
  elseif ($cmd == "CLI_CHKINVISIBLE_ICBM")
  {
   $MUIN = @$args[0];
   $data =
    $this->_SNAC_gen_header(0x04,0x06).
    _qword($this->_newreqid($cmd,$MUIN)).
    _word(0x02).
    _LV($MUIN).
    "\x00\x05\x00\x60\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00".
    "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x0a\x00\x02\x00\x01\x00\x0f\x00\x00".
    "\x5c\x11\x00\x38\x1b\x00\x07\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00".
    "\x00\x00\x00\x00\x00\x00\x03\x00\x00\x00\x00\x00\x00\x0e\x00\x00\x00\x00\x00\x00".
    "\x00\x00\x00\x00\x00\x00\x00\x00\x00\xe8\x03\x00\x00\x21\x00\x03\x00\x00\x01\x00".
    "\x00\x06\x00\x00";
  }

  elseif ($cmd == "CLI_VISIBLE_ADD")
  {
   $PUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x09,0x05).$this->_gen_uinlist($PUINS);
  }
  elseif ($cmd == "CLI_VISIBLE_REMOVE")
  {
   $PUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x09,0x06).$this->_gen_uinlist($PUINS);
  }
  elseif ($cmd == "CLI_INVISIBLE_ADD")
  {
   $PUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x09,0x07).$this->_gen_uinlist($PUINS);
  }
  elseif ($cmd == "CLI_INVISIBLE_REMOVE")
  {
   $PUINS = @$args[0];
   $data = $this->_SNAC_gen_header(0x09,0x08).$this->_gen_uinlist($PUINS);
  }

  elseif ($cmd == "CLI_SSI_AUTH_GRANT")
  {
   $AUIN = intval(@$args[0]);
   $AREASON = @$args[1];
   $data = $this->_SNAC_gen_header(0x13,0x14)._LV($AUIN)._LV($AREASON,2)._word(0x00);
  }
  elseif ($cmd == "CLI_SSI_DEL_YOUR_SELF")
  {
   $DUIN = intval(@$args[0]);
   $data = $this->_SNAC_gen_header(0x13,0x16)._LV($DUIN);
  }
  elseif ($cmd == "CLI_SSI_AUTH_REQUEST")
  {
   $AUIN = intval(@$args[0]);
   $AREASON = @$args[1];
   $data = $this->_SNAC_gen_header(0x13,0x18)._LV($AUIN)._LV($AREASON,2)._word(0x00);
  }
  elseif ($cmd == "CLI_SSI_AUTH_REPLY")
  {
   $AUIN = intval(@$args[0]);
   $AFLAG = $args[1]?1:0;
   $AREASON = @$args[2];
   $data = $this->_SNAC_gen_header(0x13,0x1A)._LV($AUIN)._byte($AFLAG)._LV($AREASON,2);
  }

  elseif ($cmd == "CLI_META")
  {
   $MREQTYPE = @$args[0];
   $MREQSUBTYPE = @$args[1];
   $INPUT = @$args[2];
   $reqdata = $reqtype = $reqsubtype = NULL;
	   if ($MREQTYPE == "OFFLINE_MESS_REQ") {$reqtype = 0x003C;}
   elseif ($MREQTYPE == "DELETE_OFFLINE_MESS_REQ") {$reqtype = 0x003E;}
   elseif ($MREQTYPE == "USER_INFO")
   {
    $reqtype = 0x07D0;
    if ($MREQSUBTYPE == "SET_BASIC")
    {
     $reqsubtype = 0x03EA;

     if (!isset($INPUT["homecountrycode"]))  {$INPUT["homecountrycode"] = 7;}
     if (!isset($INPUT["GMToffset"]))  {$INPUT["GMToffset"] = 0x00;}
     if (!isset($INPUT["pubprimary"])) {$INPUT["pubprimary"] = 0x00;}

     foreach(array(
      @$INPUT["nickname"],    @$INPUT["firstname"],
      @$INPUT["lastname"],    @$INPUT["email"],

      @$INPUT["homecity"],    @$INPUT["homestate"],
      @$INPUT["homephone"],   @$INPUT["homefax"],
      @$INPUT["homeaddress"], @$INPUT["cellphone"],
      @$INPUT["homezipcode"]
     ) as $v) {$reqdata .= _LVnull($v);}

     $reqdata .= _wordl($INPUT["homecountrycode"]);
     $reqdata .= _byte($INPUT["GMToffset"]);
     $reqdata .= _byte($INPUT["pubprimary"]);
    }
    elseif ($MREQSUBTYPE == "SET_WORK")
    {
     $reqsubtype = 0x03F3;

     if (!isset($INPUT["workcountrycode"]))     {$INPUT["workcountrycode"] = 7;}
     if (!isset($INPUT["workoccupationcode"]))  {$INPUT["workoccupationcode"] = 0;}

     foreach(array(
      @$INPUT["workcity"],				@$INPUT["workstate"],
      @$INPUT["workphone"],				@$INPUT["workfax"],

      @$INPUT["workaddress"],     		@$INPUT["workzipcode"],
      @$INPUT["workcountrycode"], 		@$INPUT["workcompany"],

      @$INPUT["workdepartment"],		@$INPUT["workposition"],
      @$INPUT["workoccupationcode"],	@$INPUT["workurl"]
     ) as $k=>$v)
     {
      if ($k == 6 or $k == 10) {$reqdata .= _wordl($v);}
      else {$reqdata .= _LVnull($v);}
     }
    }
    elseif ($MREQSUBTYPE == "SET_MORE")
    {
     $reqsubtype = 0x03FD;

     if (!isset($INPUT["age"]))     {$INPUT["age"] = 0;}
     if (!isset($INPUT["gender"]))  {$INPUT["gender"] = 0;}

     foreach(array(
      @$INPUT["age"],				@$INPUT["gender"],
      @$INPUT["homeurl"],			@$INPUT["birthyear"],
      @$INPUT["birthmonth"], 		@$INPUT["birthday"],
      @$INPUT["1dlang"],			@$INPUT["2dlang"],
      @$INPUT["3dlang"]
     ) as $k=>$v)
     {
      if ($k == 0 or $k == 3) {$reqdata .= _wordl($v);}
      elseif ($k == 1 or $k > 3) {$reqdata .= _byte($v);}
      else {$reqdata .= _LVnull($v);}
     }
    }
    elseif ($MREQSUBTYPE == "SET_NOTES")
    {
     $reqsubtype = 0x0406;
     $reqdata = _LVnull($INPUT["notes"]);
    }
    elseif ($MREQSUBTYPE == "SET_EMAIL")
    {
     $reqsubtype = 0x040B;
     $reqdata = _byte(count($INPUT["emails"]));
     foreach($INPUT["emails"] as $email)
     {
      if ($email[1]) {$email[1] = 0;}
      $reqdata .= _byte($email[1])._LVnull($email[0]);
     }
    }
    elseif ($MREQSUBTYPE == "SET_INTERESTS")
    {
     $reqsubtype = 0x0410;
     $reqdata = _byte(0x04);
     $interests = $INPUT["interests"];
     if (count($interests) < 4) {for($i=0;$i<4-count($interests);$i++) {$interests[] = array();}}
     foreach($interests as $cat) {$reqdata .= _wordl($cat["id"])._LVnull($cat["keyword"]);}
    }
    elseif ($MREQSUBTYPE == "SET_PERMS")
    {
     $reqsubtype = 0x0424;
     $this->settings["perms"] = array(
      "auth" => TRUE,
      "webaware" => FALSE,
      "DC" => 0x2
     );
     foreach(array(
       $this->settings["perms"]["auth"],
       $this->settings["perms"]["webaware"],
       $this->settings["perms"]["DC"]
      )
      as $v)
     {
      if ($v === FALSE) {$v = 0x0;} elseif ($v === TRUE) {$v = 0x1;}
      $v = intval($v);
      $reqdata .= _byte($v);
     }
     $reqdata .= _byte(0x00);
    }
    elseif ($MREQSUBTYPE == "SET_PASSWORD") {$reqsubtype = 0x042E; $reqdata = _LVnull($INPUT["password"]);}
    elseif ($MREQSUBTYPE == "REQ_FULLINFO") {$reqsubtype = 0x04B2; if (!@$INPUT["uin"]) {$INPUT["uin"] = $this->uin;} $reqdata = _dwordl($INPUT["uin"]."\x00",2,TRUE);}
    elseif ($MREQSUBTYPE == "REQ_SHORTINFO") {$reqsubtype = 0x04BA; if (!@$INPUT["uin"]) {$INPUT["uin"] = $this->uin;} $reqdata = _dwordl($INPUT["uin"]."\x00",2,TRUE);}
    elseif ($MREQSUBTYPE == "REQ_SELF_FULLINFO") {$reqsubtype = 0x04D0; if (!@$INPUT["uin"]) {$INPUT["uin"] = $this->uin;} $reqdata = _dwordl($INPUT["uin"]."\x00",2,TRUE);}
    elseif ($MREQSUBTYPE == "UNREGISTER")
    {
     $reqsubtype = 0x04C4;
     if (empty($INPUT["uin"])) {$INPUT["uin"] = $this->uin;}
     if (empty($INPUT["pass"])) {$INPUT["password"] = $this->pass;}
     $reqdata = _dwordl($INPUT["uin"])._LVnull($INPUT["password"]);
    }
    elseif ($MREQSUBTYPE == "FIND_BY_DETAILS")
    {
     $reqsubtype = 0x0515;
     $reqdata =
      _LVnull($INPUT["firstname"]).
      _LVnull($INPUT["lastname"]).
      _LVnull($INPUT["nickname"])
     ;
    }
    elseif ($MREQSUBTYPE == "FIND_BY_UIN")
    {
     $reqsubtype = 0x051F;
     $reqdata = _dwordl($INPUT["uin"]);
    }
    elseif ($MREQSUBTYPE == "FIND_BY_EMAIL")
    {
     $reqsubtype = 0x0529;
     $reqdata = _LVnull($INPUT["email"]);
    }
    elseif ($MREQSUBTYPE == "FIND_BY_UIN_TLV")
    {
     $reqsubtype = 0x051F;
     $TLV = array(0x0136 => $INPUT["uin"]);
     $reqdata = $this->array2TLV($TLV,TRUE);
    }
    elseif ($MREQSUBTYPE == "WHITEPAGES_TLV")
    {
     $reqsubtype = 0x055F;
     $TLV = array();
      if (isset($INPUT["city"]))			{$TLV[0x0190] = _LVnull($INPUT["city"]);}
      if (isset($INPUT["state"]))			{$TLV[0x019A] = _LVnull($INPUT["state"]);}
      if (isset($INPUT["company"]))			{$TLV[0x01AE] = _LVnull($INPUT["company"]);}
      if (isset($INPUT["department"]))		{$TLV[0x01B8] = _LVnull($INPUT["department"]);}
      if (isset($INPUT["position"]))		{$TLV[0x01C2] = _LVnull($INPUT["position"]);}
      if (isset($INPUT["age_min"]))			{$TLV[0x0168] = _dword($INPUT["age_min"])._dword($INPUT["age_max"]);}
      if (isset($INPUT["gender"]))			{$TLV[0x017C] = _byte($INPUT["gender"]);}
      if (isset($INPUT["lang_code"]))		{$TLV[0x0186] = _word($INPUT["lang_code"]);}
      if (isset($INPUT["country_code"]))	{$TLV[0x01A4] = _word($INPUT["country_code"]);}
      if (isset($INPUT["occupation_code"]))	{$TLV[0x01CC] = _word($INPUT["occupation_code"]);}
      if (isset($INPUT["past_cat"]))		{$TLV[0x01D6] = _wordl($INPUT["past_cat"])._LVnull($INPUT["past_keywords"]);}
      if (isset($INPUT["interests_cat"]))	{$TLV[0x01EA] = _wordl($INPUT["interests_cat"])._LVnull($INPUT["interests_keywords"]);}
      if (isset($INPUT["aff_cat"]))			{$TLV[0x01FE] = _wordl($INPUT["aff_cat"])._LVnull($INPUT["aff_keywords"]);}
      if (isset($INPUT["home_cat"]))		{$TLV[0x0212] = _wordl($INPUT["homepage_cat"])._LVnull($INPUT["homepage_keywords"]);}
      if (isset($INPUT["firstname"]))		{$TLV[0x0140] = _LVnull($INPUT["firstname"]);}
      if (isset($INPUT["lastname"]))		{$TLV[0x014A] = _LVnull($INPUT["lastname"]);}
      if (isset($INPUT["nickname"]))		{$TLV[0x0154] = _LVnull($INPUT["nickname"]);}
      if (isset($INPUT["keywords"]))		{$TLV[0x0226] = _LVnull($INPUT["keywords"]);}
      if (isset($INPUT["email"]))			{$TLV[0x015E] = _LVnull($INPUT["email"]);}
      $TLV[0x0230] = _byte(@$INPUT["online"]?0x01:0x00);
     $reqdata = $this->array2TLV($TLV,TRUE);
    }
    elseif ($MREQSUBTYPE == "FIND_BY_EMAIL_TLV")
    {
     $reqsubtype = 0x0529;
     $TLV = array(0x015E => $INPUT["uin"]);
     $reqdata = $this->array2TLV($TLV,TRUE);
    }
   }
   if (!isset($this->srv["metaseq"])) {$this->srv["metaseq"] = 0x00;}
   $req =
    _dwordl($this->uin).
    _wordl($reqtype).
    _wordl($this->srv["metaseq"]);
   if ($reqsubtype !== NULL) {$req .= _wordl($reqsubtype);}
   $req .= $reqdata;
   $request = _LV($req,2,TRUE);
   $data =
    $this->_SNAC_gen_header(0x15,0x02).
    $this->array2TLV(array(0x01 => _LV($req,2,TRUE)));
    $this->_update_sequence($this->srv["metaseq"]);
  }

  elseif ($cmd == "CLI_REGISTRATION_REQUEST")
  {
   $RPASS = @$args[0];

   $COOKIE = _dword(0x00);
   $NULL = _dword(0x00);

   $TLV = array();
   $TLV[0x01] =
    $NULL.
    _wordl(0x28).
    _wordl(0x03).
    $NULL.$NULL.
    $COOKIE.$COOKIE.
    $NULL.$NULL.$NULL.$NULL.
    _LVnull($RPASS).
    $COOKIE.
    _dword(0xEE070000)
    ;
   $data = $this->_SNAC_gen_header(0x17,0x04).$this->array2TLV($TLV);
  }
  elseif ($cmd == "CLI_DISCONNECT" or $cmd == "CLI_GOODBYE") {$channel = 0x04; $data = NULL;}
  else {$this->_error("_SNAC_gen() error: unknown/unsupported SNAC-type (".$family.".".$subfamily.")."); return FALSE;}
  return array($channel,$data);
 }
 function _SNAC_parse_onlineuserinfo($TLV)
 {
  $result = array();
  if (@$TLV[0x01]) {$result["userclass"] = b2i($TLV[0x01]);}
  if (@$TLV[0x02]) {$result["create_time"] = b2i($TLV[0x02]);}
  if (@$TLV[0x03]) {$result["signon_time"] = b2i($TLV[0x03]);}
  if (@$TLV[0x04]) {$result["idle_time"] = b2i($TLV[0x04]);}
  if (@$TLV[0x05]) {$result["acc_create_time"] = b2i($TLV[0x05]);}
  if (@$TLV[0x06]) {$result["status_flags"] = b2i(substr($TLV[0x06],0,2));}
  if (@$TLV[0x06]) {$result["user_status"] = b2i(substr($TLV[0x06],2,2));}
  if (@$TLV[0x0A]) {$result["external_ip"] = long2ip(b2i($TLV[0x0A]));}
  if (@$TLV[0x0C]) {$result["DC"] = $TLV[0x0C];}
  if (@$TLV[0x0D]) {$result["capabilities"] = $TLV[0x0D];}
  if (@$TLV[0x0F]) {$result["online_time"] = b2i($TLV[0x0F]);}
  return $result;
 }
 function _SNAC_parse_lea($p,$num=-1)
 {
  $out = array();
  if ($num == -1) {$num = strlen($p);}
  $i = 0;
  while(strlen($p) > 0 and $i < $num)
  {
   $len = @b2i(substr($p,0,2),TRUE); $p = @substr($p,2);
   $value = substr($p,0,$len-1); $p = @substr($p,$len);
   $out[] = $value;
   $i++;
  }
  return $out;
 }
 function _SNAC_parse($SNAC)
 {
  if (!$this->login_pass()) {return FALSE;}
  else
  {
   $result = NULL;
   $family = b2i($SNAC{0}.$SNAC{1});
   $subfamily = b2i($SNAC{2}.$SNAC{3});
   $SNACType = $family.".".$subfamily;
   $flags = b2i($SNAC{4}.$SNAC{5});
   $reqid = b2i(substr($SNAC,6,4));
   $body = substr($SNAC,10);
   if ($subfamily == 0x01) {$SNACType = "xx.".(0x01);}
   $cmd = @$this->recvknownSNACs[$SNACType];
   if (empty($cmd)) {$cmd = "unknown";}
   $this->inLastCmd = $cmd;
   $this->inLastVar = array();
   if ($cmd == "SRV_ERROR")
   {
    $errcode = b2i(substr($body,0,2));
    $errsubcode = $this->TLV2array(substr($body,2));
    $this->inLastVar["errfamily"] = $family;
    $this->inLastVar["errcode"] = $errcode;
    $errstr = @$this->const["SRV_ERR"][$family][$errcode];
    if (empty($errstr)) {$errstr = @$this->const["SRV_ERR"]["xx"][$errcode];}
    $this->inLastVar["errstr"] = $errstr;
    $this->inLastVar["errsubcode"] = $errsubcode;
    if ($this->errlevel > 100) {$this->_error("SRV_ERROR","Family: 0x".dechex($family).", Err. code: ".$errcode.", Err. string: ".$errstr.", Err. sub-code: ".dechex($errsubcode));}
    $result = TRUE;
   }
   elseif ($cmd == "SRV_ONLINE_INFO")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["warn_level"] = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLVnum = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLV = $this->TLV2array($p);
    $this->inLastVar = array_merge($this->inLastVar,$this->_SNAC_parse_onlineuserinfo($TLV));
    $this->inLastVar["refresh"] = getmicrotime();
    $this->misc["selfonlineinfo"] = $this->inLastVar;
    $result = TRUE;
   }
   elseif ($cmd == "SRV_MIGRATION")
   {
    $TLV = $this->TLV2array($body);
    return $this->_connect_migration($TLV[0x05],$TLV[0x06]);
    $result = TRUE;
   }
   elseif ($cmd == "SRV_MOTD")
   {
    $TLV = $this->TLV2array(substr($body,2));
    $this->srv["MOTD"] = $TLV[0x0B];
    $result = TRUE;
   }
   elseif ($cmd == "SRV_RATE_LIMIT_INFO")
   {
    $num = dechex(ord($SNAC{13})+(ord($SNAC{12})<<8));
    $TLV = $this->TLV2array(substr($SNAC,14));
    foreach($TLV as $k=>$v) {$this->srv["RATE_LIMITS"][$k] = $v[1];}
    $result = TRUE;
   }
   elseif ($cmd == "SRV_FAMILIES")
   {
    $this->srv["FAMILIES"] = array();
    $TLV = $this->TLV2array($body);
    foreach($TLV as $k=>$v) {$this->srv["FAMILIES"][$k] = $v[1];}
    $result = TRUE;
   }
   elseif ($cmd == "SRV_FAMILIES2")
   {
    $this->srv["FAMILIES2"] = array();
    $TLV = $this->TLV2array($body);
    foreach($TLV as $k=>$v) {$this->srv["FAMILIES2"][$k] = $v[1];}
    $result = TRUE;
   }
   elseif ($cmd == "SRV_WELL_KNOWN_URLS")
   {
    /// coming soon
    $result = TRUE;
   }
   elseif ($cmd == "SRV_EXT_STATUS")
   {
    /// coming soon
    $result = TRUE;
   }
   elseif ($cmd == "SRV_USER_ONLINE")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["warn_level"] = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLVnum = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLV = $this->TLV2array($p);
    $this->inLastVar = array_merge($this->inLastVar,$this->_SNAC_parse_onlineuserinfo($TLV));
    $this->inLastVar["refresh"] = getmicrotime();
    $this->misc["onlineinfo"][$this->inLastVar["uin"]] = $this->inLastVar;
    $result = TRUE;
   }
   elseif ($cmd == "SRV_USER_OFFLINE")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["warn_level"] = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLVnum = b2i(substr($p,0,2));
    $p = substr($p,2);
    $TLV = $this->TLV2array($p);
    $this->inLastVar = array_merge($this->inLastVar,$this->_SNAC_parse_onlineuserinfo($TLV));
    $this->inLastVar["refresh"] = getmicrotime();
    $this->misc["onlineinfo"][$uin] = $this->inLastVar;
    $result = TRUE;
   }
   elseif ($cmd == "SRV_CLIENT_ICBM")
   {
    $result = TRUE;
    $this->inLastVar["time"] = time();
    $this->inLastVar["mid"] = b2i(substr($body,0,8));
    $this->inLastVar["channel"] = b2i($body{9});
    $uin_size = b2i($body{10});
    $this->inLastVar["uin"] = substr($body,11,$uin_size);
    $this->inLastVar["warnlevel"] = b2i(substr($body,12+$uin_size,2));
    $TLV = substr($body,15+$uin_size);
    $TLV = $this->TLV2array($TLV);
    $this->inLastVar["userclass"] = b2i(@$TLV[0x01]);
    $this->inLastVar["ustatus"] = b2i(@$TLV[0x06]);

    $this->inLastVar["ctime"] = b2i($TLV[0x03]);
    $this->inLastVar["auto"] = @is_array($TLV[0x04]);
    if ($this->inLastVar["channel"] == 0x01)
    {
     $this->inLastVar["idle"] = b2i($TLV[0x0F]);
     $str = $TLV[0x02];
     $s1 = ord($str{3})+(ord($str{2})<<8);
     $s = substr($str,6+$s1,2);
     $s2 = ord($s{1})+(ord($s{0})<<8);
     $this->inLastVar["text"] = substr($str,12+$s1,$s2+4);
    }
    elseif ($this->inLastVar["channel"] == 0x02)
    {
     $this->inLastVar["onlinetime"] = b2i($TLV[0x0F]);
     $str = $TLV[0x05];
     $s1 = substr($str,2,2);
     $s2 = substr($str,6+$s1,2);
     $this->inLastVar["text"] = substr($str,8+$s1,$s2);
    }
    else {$result = FALSE;}
   }
   elseif ($cmd == "SRV_MY_OFFLINE")
   {
    $result = TRUE;
   }
   elseif ($cmd == "SRV_MISSED_MESSAGE")
   {
    $result = TRUE;
   }
   elseif ($cmd == "SRV_MSG_ACK")
   {
    $p = $body;
    $id = b2i(substr($p,0,8)); $p = substr($p,8);
    $channel = b2i(substr($p,0,2)); $p = substr($p,2);
    $len = b2i($p{0}); $p = substr($p,1);
    $uin = substr($p,0,$len); $p = substr($p,$len);
    $this->_setreqidstatus($id,1);
    $this->inLastVar["id"] = $id;
    $this->inLastVar["channel"] = $channel;
    $this->inLastVar["uin"] = $uin;
    $result = TRUE;
   }
   elseif ($cmd == "SRV_SSI_FUTURE_AUTH_GRANTED")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["reason"] = substr($p,1,b2i($p{0}));
    $result = TRUE;
   }
   elseif ($cmd == "SRV_SSI_AUTH_REQUEST")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["reason"] = substr($p,1,b2i($p{0}));
    $result = TRUE;
   }
   elseif ($cmd == "SRV_SSI_YOU_WERE_ADDED")
   {
    $this->inLastVar["uin"] = substr($body,1,b2i($body{0}));
    $result = TRUE;
   }
   elseif ($cmd == "SRV_SSI_AUTH_REPLY")
   {
    $p = $body;
    $this->inLastVar["uin"] = substr($p,1,b2i($p{0}));
    $p = substr($p,1+b2i($p{0}));
    $this->inLastVar["flag"] = b2i($p{0});
    $p = substr($p,1);
    $this->inLastVar["reason"] = substr($p,1,b2i($p{0}));
    $result = TRUE;
   }
   elseif ($cmd == "SRV_META")
   {
    $result = TRUE;
    $TLV = $this->TLV2array($body);
    $p = $TLV[0x01];
    $datalen = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
    $uin = b2i(substr($p,0,4),TRUE); $p = substr($p,4);
    $type = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
    $seq = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
    if (!isset($this->srv["metaseq"])) {$this->srv["metaseq"] = 0x00;}
    $responces = array(
     0x0041 => "OFFLINE_MESSAGE",
     0x0042 => "OFFLINE_MSGS_DONE",
     0x07DA => "INFO"
    );
    $responce = @$responces[$type];
    if ($responce) {$this->inLastVar["type"] = $responce;}
    if ($responce == "OFFLINE_MESSAGE")
    {
     $this->inLastVar["uin"] = b2i(substr($p,0,4),TRUE); $p = substr($p,4);
     $this->inLastVar["date_year"]  = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
     $this->inLastVar["date_month"] = b2i($p{0}); $p = substr($p,1);
     $this->inLastVar["date_day"]   = b2i($p{0}); $p = substr($p,1);
     $this->inLastVar["date_hour"]  = b2i($p{0}); $p = substr($p,1);
     $this->inLastVar["date_min"]   = b2i($p{0}); $p = substr($p,1);
     $this->inLastVar["mtype"]      = b2i($p{0}); $p = substr($p,1);
     $this->inLastVar["flags"]      = b2i($p{0}); $p = substr($p,1);
     $textlen   				    = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
     $this->inLastVar["text"]	    = substr($p,0,$textlen-1);
    }
    elseif ($responce == "OFFLINE_MSGS_DONE") {$this->inLastVar["dropped_msgs_flag"] = b2i($p);}
    elseif ($responce == "INFO")
    {
     $inforesponces = array(
	  0x0001 => "ERROR",
 	  0x0064 => "SET_HOME_ACK",			0x006E => "SET_WORK_ACK",
  	  0x0078 => "SET_MORE_ACK",			0x0082 => "SET_NOTES_ACK",
  	  0x0087 => "SET_EMAIL_ACK",		0x008C => "SET_INTERESTS_ACK",
  	  0x0096 => "SET_AFF_ACK",			0x0096 => "SET_SMS_ACK",
  	  0x00A0 => "SET_PERMS_ACK",		0x00AA => "SET_PASSWORD_ACK",
  	  0x00B4 => "UNREGISTER_ACK",		0x00BE => "SET_HOMEPAGE_ACK",
  	  0x00C8 => "REPLY_BASIC",			0x00D2 => "REPLY_WORK",
  	  0x00DC => "REPLY_MORE",			0x00E6 => "REPLY_NOTES",
  	  0x00EB => "REPLY_EMAIL",			0x00F0 => "REPLY_INTERESTS",
  	  0x00FA => "REPLY_AFF",			0x0104 => "REPLY_SHORT",
  	  0x010E => "REPLY_HOMEPAGE",		0x01A4 => "USER_FOUND",
  	  0x01AE => "USER_FOUND",			0x0302 => "REPLY_REG_STATS_ACK",
  	  0x0366 => "REPLY_RND_SEARCH",		0x08A2 => "REPLY_VAR_XML",
  	  0x0C3F => "REPLY_FULLINFO_ACK",	0x2012 => "REPLY_SPAMREPORT_ACK",
     );
     $subtype = @b2i(substr($p,0,2),TRUE); $p = @substr($p,2);
     $inforesp = $this->inLastVar["subtype"] = $inforesponces[$subtype];
     if (empty($inforesp)) {$this->_error("_SNAC_parse","unknown meta info responce - ".'#0x'.dechex($subtype).".",__FILE__,__LINE__); return FALSE;}
     else
     {
      $this->inLastVar["SNACflags"] = $flags;
      $this->inLastVar["metaseq"] = $seq;
      if ($inforesp == "ERROR")
      {
       $this->inLastVar["errcode"] = b2i($p{0}); $p = substr($p,1);
       $this->inLastVar["errstr"] = substr($p,0,-1); $p = NULL;
       $this->_error("SRV_META_ERROR","Request-type: ".$responce.", Err. code: ".$this->inLastVar["errcode"].", Err. string: ".$this->inLastVar["errstr"].".");
       $result = TRUE;
      }
      elseif (substr($inforesp,-4) == "_ACK") {$this->inLastVar["success"] = @b2i($p{0}) == 0x0A;}
      elseif ($inforesp == "USER_FOUND")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
        $p = substr($p,1);
        $this->inLastVar["lastitem"] = $subtype == 0x01AE;
        $p = substr($p,2);
        $this->inLastVar["uin"] = @b2i(substr($p,0,4),TRUE); $p = substr($p,4);
        @list(
         $this->inLastVar["nickname"],		$this->inLastVar["firstname"],
         $this->inLastVar["lastname"],		$this->inLastVar["email"]
		) = $this->_SNAC_parse_lea(&$p,4);
		$this->inLastVar = array_reverse($this->inLastVar);
		$this->inLastVar["auth"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["online_status"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["gender"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["age"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
	if ($subtype == 0x01AE)
	{
		$this->inLastVar["uinsleft"] = b2i(substr($p,0,4),TRUE); $p = substr($p,4);
	}
		$result = TRUE;
       }
      }
      elseif ($inforesp == "REPLY_BASIC")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
        $p = substr($p,1);
        @list(
         $this->inLastVar["nickname"],
         $this->inLastVar["firstname"],     $this->inLastVar["lastname"],
         $this->inLastVar["email"],			$this->inLastVar["homecity"],
         $this->inLastVar["homestate"],		$this->inLastVar["homephone"],
		 $this->inLastVar["homefax"],		$this->inLastVar["homeadress"],
		 $this->inLastVar["cellphone"],		$this->inLastVar["homezip"],
		) = $this->_SNAC_parse_lea(&$p,10);
		$this->inLastVar = array_reverse($this->inLastVar);
		$this->inLastVar["homecountrycode"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["GMToffset"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["auth"] = $p{0}; $p = substr($p,1);
		$this->inLastVar["webaware"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["DC"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["pubprimary"] = b2i($p{0}); $p = substr($p,1);
		$result = TRUE;
       }
      }
      elseif ($inforesp == "REPLY_EMAIL")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
        $p = substr($p,1);
        $this->inLastVar["num"] = @b2i($p{0}); $p = substr($p,1);
        for($i=0;$i<$this->inLastVar["num"];$i++)
        {
         $k = "email".$i;
		 $this->inLastVar[$k]["pub"] = b2i($p{0}) !== 0; $p = substr($p,1);
		 $len = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		 $this->inLastVar[$k]["email"] = substr($v,0,$len-1); $p = substr($p,$len);
		}
		$result = TRUE;
       }
      }
      elseif ($inforesp == "REPLY_AFF")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
          $result = TRUE;
       }
      }
      elseif ($inforesp == "REPLY_MORE")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
        $p = substr($p,1);
		$this->inLastVar["age"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["gender"] = $p{0}; $p = substr($p,1);
		$len = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["homepage"] = substr($p,0,$len-1); $p = substr($p,$len);
		$this->inLastVar["birth_year"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["birth_month"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["birth_day"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["1dlang"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["2dlang"] = b2i($p{0}); $p = substr($p,1);
		$this->inLastVar["3dlang"] = b2i($p{0}); $p = substr($p,1);
		$p = substr($p,2); // unknown word le
		$len = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["origfrom_city"] = b2i(substr($p,0,$len-1),TRUE); $p = substr($p,$len);
		$len = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["origfrom_state"] = b2i(substr($p,0,$len-1),TRUE); $p = substr($p,$len);
		$this->inLastVar["origfrom_countrycode"] = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["timezone"] = b2i($p{0}); $p = substr($p,1);
		$result = TRUE;
       }
      }
      elseif ($inforesp == "REPLY_NOTES")
      {
       if (!$this->inLastVar["success"] = @b2i($p{0}) == 0x0A) {$result = FALSE;}
       else
       {
        $p = substr($p,1);
		$len = b2i(substr($p,0,2),TRUE); $p = substr($p,2);
		$this->inLastVar["notes"] = substr($p,0,$len-1); $p = substr($p,$len);
		$result = TRUE;
       }
      }
      else {$this->_error("_SNAC_parse","unsupported meta info responce - ".'#0x'.dechex($subtype).".",__FILE__,__LINE__); return FALSE;}
     }
    }
    else {$this->inLastVar["type"] = "UNKNOWN";}
    if ($this->inLastVar["type"] == "UNKNOWN") {$this->_error("_SNAC_parse","unknown/unsupported meta responce - ".$inforesp." (".'#0x'.dechex($type).").",__FILE__,__LINE__); return FALSE;}
   }
   elseif ($cmd == "SRV_NEW_UIN") {$this->inLastVar["uin"] = b2i(substr($body,-10,4),TRUE);}
   if ($result === NULL) {$this->_error("_SNAC_parse","unknown/unsupported SNAC ".'#0x'.dechex($family).".0x".dechex($subfamily).".",__FILE__,__LINE__); return FALSE;}
   elseif ($result === FALSE) {$this->_error("_SNAC_parse","processing ".$cmd." (".'#0x'.dechex($family).".0x".dechex($subfamily).") failed: ".@$err.".",__FILE__,__LINE__); return FALSE;}
   else {return TRUE;}
  }
 }
 function _error($procedure,$error,$file="unknown",$line="unknown")
 {
  $string = $procedure."() error: ".$error." in file ".$file." at line ".$line.".\n";
  $this->lasterror = $string;
  echo $string;
  return FALSE;
 }
 function lasterror() {return $this->lasterror;}
 function is_uin($uin) {$uin = $this->defake($uin); return is_numeric($uin) and strlen($uin) >= 5 and strlen($uin) <= 9;}
 function is_password($password) {return strlen($password) <= 8;}
 function defake($uin) {while ($uin - 4294967296 > 0) {$uin -= 4294967296;} return $uin;}
 function disconnect($sendpacket=FALSE,$data=NULL)
 {
  $this->connected = $this->logged = FALSE;
  $TLV = $this->TLV2array($data);
  if (!isset($TLV[0x05]) or !isset($TLV[0x06]))
  {
   $reason = "communication";
   if ($data !== NULL)
   {
    $errcode = b2i(@$TLV[0x09]);
    $errdescr = @$TLV[0x0b];
    $reason = @$this->const["SRV_ERR"]["DISCONNECT_REASONS"][$errcode];
    if (!$reason) {$reason = "unknown (0x".dechex($errcode).")";}
   }
   $this->_error("disconnect","reason: ".$reason,__FILE__,__LINE__);
  }
  if ($this->socket and $sendpacket) {$this->send("CLI_DISCONNECT");}
  unset($this->srv["inseq"]);
  if ($this->socket) {@socket_close($this->socket);}
  $this->socket = FALSE;
  return FALSE;
 }
 function array2TLV($array,$rev=FALSE)
 {
  $out = "";
  foreach($array as $i=>$item)
  {
   if (is_array($item)) {$item = $this->array2TLV($item);}
   if (!$rev) {$out .= _word($i)._word(strlen($item));}
   else {$out .= strrev(_word($i)).strrev(_word(strlen($item)));}
   $out .= $item;
  }
  return $out;
 }
 function TLV2array($str="",$rev=FALSE)
 {
  $out = array();
  while ($str != NULL)
  {
   if (!$rev)
   {
    $i = @ord($str{1})+(@ord($str{0})<<8);
    $size = @ord(@$str{3})+(@ord($str{2})<<8);
   }
   else
   {
    $i = @ord($str{0})+(@ord($str{1})<<8);
    $size = @ord(@$str{2})+(@ord($str{3})<<8);
   }
   $data = @substr($str,4,$size);
   $out[$i] = $data;
   $str = @substr($str,4+$size);
  }
  unset($out[0x0]);
  return $out;
 }
 function TLV2array_numbered($str="")
 {
  $out = array();
  while ($str != NULL)
  {
   $i = @ord($str{1})+(@ord($str{0})<<8);
   $size = @ord(@$str{3})+(@ord($str{2})<<8);
   $data = @substr($str,4,$size);
   $out[] = @array($data,$i);
   $str = @substr($str,4+$size);
  }
  return $out;
 }
 function http_checklogin($uin,$password)
 {
  $http = fsockopen("www.icqmail.com",80,$errno,$errstr,1);
  if ($http)
  {
   $post ="user=admin&AltID=".$uin."&pwd=".urlencode($password)."&repwd=".urlencode($password)."&firstname=&lastname=&tosagree=&action=register&xo=RU";
   fwrite($http,"POST http://www.icqmail.com/s/icq/reg_icq.asp HTTP/1.1\n".
			"Host: www.icqmail.com\n".
			"Connection: close\n".
			"TE: deflate, gzip, chunked, identity, trailers\n".
			"Content-Type: application/x-www-form-urlencoded\n".
			"Content-Length: ".strlen($post)."\n".
			"\n".$post."\n");
   while (!feof($http) and $line = fgets($http)) {if (strpos($line,"someone has already chosen")) {return 1; break;} elseif (strpos($line,"Password do not match")) {return 0; break;}}
   return FALSE;
  }
  else {$this->_error("http_checklogin","connection to www.icqmail.com:80 has failed",__FILE__,__LINE__);}
 }
 function XORencrypt($pass) {$pass = substr($pass,0,strlen($this->XORseq)); $XORed = ""; for ($i=0; $i<strlen($pass); $i++) {$XORed .= chr(ord($this->XORseq{$i}) ^ ord($pass{$i}));} return $XORed;}
 function XORdecrypt($XORed) {$pass = ""; for($i=0; $i<strlen($XsORed); $i++) {$pass .= chr(ord($XORed{$i}) ^ ord($this->XORseq{$i}));} return $pass;}
 function XORseq($plain,$XORed) {$XORseq = ""; for($i=0;$i<strlen($plain);$i++) {$XORseq .= chr(ord($plain{$i}) ^ ord($XORed{$i}));} return $XORseq;}

 function set_event($event,$code) {$this->events[$event] = $code; return TRUE;}
 function get_event($event) {return $this->events[$event];}
 function exec_event($event,$params=array()) {extract($params); eval($this->events[$event]); return;}

 function extractconst($prefix="ICQ_",$var=FALSE)
 {
  if ($var === FALSE) {$var = &$this->const;}
  foreach($this->const as $k=>$v)
  {
   if (is_array($v)) {$this->extractconst($prefix.$k."_",$var[$k]);}
   else {define($prefix.$k,$v);}
  }
 }
 function _gen_uinlist($list=FALSE) {if (!is_array($list)) {$list = func_get_args();} $str = ""; foreach ($list as $uin) {$str .= _LV($uin);} return $str;}

 function _update_sequence(&$seq) {@$seq++; if ($seq >= 256*256) {$seq %= 128*256;} if ($this->srv["inseq"] == 0x8000) {$this->srv["inseq"] = 0x0000;}}

 function _newreqid($cmd,$info=array())
 {
  $rnd = substr(round(getmicrotime()+rand(0,1000)+time()),0,8);
  $this->requests[$rnd] = array(
   "time" => time(),
   "status" => 0,
   "cmd" => $cmd,
   "info" => $info
  );
  return $rnd;
 }
 function _setreqidstatus($id,$status) {return $this->requests[$id]["status"] = $status;}
 function _getreqidstatus($id) {return $this->requests[$id]["status"];}
 function _getreqid($id) {return $this->requests[$id];}
}
if (!function_exists("getmicrotime")) {function getmicrotime() {list($usec,$sec) = explode(" ",microtime()); return ((float)$usec + (float)$sec);}}

function _LV($string,$len=1,$lrev=FALSE) {$l = i2b($len,strlen($string)); if ($lrev) {$l = strrev($l);} return $l.$string;}
function _LVnull($string) {return _LV($string."\x00",2,TRUE);}
function _byte($int) {return i2b(1,$int);}

function _word($int) {return i2b(2,$int);}
function _wordl($int) {return strrev(_word($int));}

function _dword($int) {return i2b(4,$int);}
function _dwordl($int) {return strrev(_dword($int));}

function _qword($int) {return i2b(8,$int);}
function _qwordl($int) {return strrev(_qword($int));}

function int2bytes($len,$int=0x00)
{
 $hexstr = dechex($int);
 if ($len === NULL) {if (strlen($hexstr) % 2) {$hexstr = "0".$hexstr;}}
 else {$hexstr = str_repeat("0",$len*2-strlen($hexstr)).$hexstr;}
 $bytes = strlen($hexstr)/2;
 $bin = "";
 for($i=0;$i<$bytes;$i++) {$bin .= chr(hexdec(substr($hexstr,$i*2,2)));}
 return $bin;
}

function i2b($bytes,$val=0) {return int2bytes($bytes,$val);}
function bytes2int($str,$l=FALSE)
{
 if ($l) {$str = strrev($str);}
 $dec = 0;
 $len = strlen($str);
 for($i=0;$i<$len;$i++){$dec += ord(substr($str,$i,1))*pow(256,$len-$i-1);}
 return $dec;
}
function b2i($hex=0,$l=FALSE) {return bytes2int($hex,$l);}
function hecho($string) {return preg_replace('#.#se',sprintf("\\x%02x",ord("$0")),$string);}
?>