<?php

class Mail_Helper
{
	private $_server;
	private $_port;
	private $_ssl;
	private $_user;
	private $_pass;
	private $_conn;
	private $_errors;
	
	public function __construct($server, $port, $ssl, $user, $pass)
	{
		$this -> _server = $server;
		$this -> _port = $port;
		$this -> _ssl = $ssl;
		$this -> _user = $user;
		$this -> _pass = $pass;
	}
	
	public function connect()
	{
		$string = '{'.$this->_server.':'.$this->_port;
		
		if (strpos($this->_server, 'imap') === FALSE)
		{
			// it's a pop3 server
			$string .= '/pop3';
		}
		
		if ($this->_ssl == 'true')
		{
			$string .= '/ssl';
		}
		
		$string .= '/novalidate-cert}INBOX';
		
		$this->_conn = @imap_open($string, $this->_user, $this->_pass) or
			$this->_errors = imap_last_error();
		
		return $this->_errors;
	}
	
	/**
	 * get all emails for today
	 */
	public function latest()
	{
		$date = date('d-F-Y');
		
		return imap_search($this->_conn,'SINCE "'.$date.'"', SE_UID);
	}
	
	public function getData($uid)
	{
		$msgno = imap_msgno($this->_conn, $uid);
		$overview = imap_headerinfo($this->_conn, $msgno);
		
		$data = array();
		$data['subject'] = $overview -> subject;
		$data['from'] = $overview -> fromaddress.' ('.$overview -> from['0']->mailbox.'@'.$overview -> from['0']->host.')';
		$data['date'] = $overview -> date;
		$data['message_id'] = $overview -> message_id;
		$data['body'] = imap_fetchbody($this->_conn, $uid, 1.1, FT_UID);
		
		if (empty($data['body']))
		{
			// no attachments is the usual cause of this
			$data['body'] = imap_fetchbody($this->_conn, $uid, 1, FT_UID);
		}
		
		$data['body'] = strip_tags($data['body']);
		
		return $data;
	}
}