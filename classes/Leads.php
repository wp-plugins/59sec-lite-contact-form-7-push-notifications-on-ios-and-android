<?php

class Leads
{
	private $_db;
	private $_prefix;
	private $_itemsPerPage = 10;
	public $_statuses = array(
		'1' => 'new',
		'2' => 'taken & not contacted',
		'3' => 'contacted',
		'4' => 'pending',
		'5' => 'followup',
		'6' => 'finalized',
		'7' => 'rejected',
	);
	
	/**
	 * Load the resources
	 */
	public function __construct($dbResource, $prefix)
	{
		$this -> _db = &$dbResource;
		$this -> _prefix = $prefix;
	}
	
	/**
	 * Run at plugin activation
	 */
	public function constructSQL()
	{	
		$sqls = array();
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `{$this->_prefix}59_leads` (
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`entity_id` bigint(20) NOT NULL,
		`subject` varchar(255) NOT NULL,
		`user_comments` text,
		`user_name` varchar(256) DEFAULT NULL,
		`user_id` int(11) DEFAULT NULL,
		`status` varchar(100) DEFAULT NULL,
		`boss_alert` int(1) NOT NULL,
		`type` varchar(10) NOT NULL,
		`created_time` varchar(10) DEFAULT NULL,
		`reserved_time` varchar(10) DEFAULT NULL,
		`completed_time` varchar(10) DEFAULT NULL,
		`postdata` text NOT NULL,
		`flag` INT(1) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
		";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `{$this->_prefix}59_tokens` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`device_token` VARCHAR(255) NOT NULL,
		`type` VARCHAR(100) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
		";
		
		return $sqls;
	}
	
	/**
	 * Run at plugin deactivation
	 */
	public function destructSQL()
	{
		// $tableName = $this->_prefix.'59_leads';
		
		// return "DROP TABLE IF EXISTS `{$tableName}`";
	}
	
	/**
	 * Update from old version to current version.
	 */
	public function updateVersion($version)
	{
		
	}
   
	/**
	 * Add a new lead into the database
	 */
	public function add($data)
	{
		// secure the data
		$data['type'] = addslashes($data['type']);
		$data['entity_id'] = intval($data['entity_id']);
		$data['subject'] = addslashes($data['subject']);
		$data['status'] = addslashes($data['status']);
		$data['created_time'] = time();
		$data['postdata'] = addslashes($data['postdata']);
		
		$sql = "INSERT 
		INTO `{$this -> _prefix}59_leads` (`type`, `entity_id`, `subject`, `status`, `created_time`, `postdata`)
		VALUES ('{$data['type']}','{$data['entity_id']}','{$data['subject']}','{$data['status']}','{$data['created_time']}','{$data['postdata']}')
		";
		
		mysql_query($sql, $this -> _db);
	}
	
	/**
	 * Update one lead in the database.
	 */
	public function update($data, $id = 0)
	{
		$id = intval($id);
		
		if (!empty($data))
		{
			$fields = array();
			
			foreach ($data as $key => $value)
			{
				$value = addslashes($value);
				$fields[] = "`{$key}` = '{$value}'";
			}
			
			$fields = implode(', ', $fields);
			
			$sql = "UPDATE `{$this -> _prefix}59_leads`
					SET $fields
					WHERE `id` = '$id'
					";
					
			mysql_query($sql, $this -> _db);
			
			return mysql_error($this -> _db);
		}
		else
		{
			// should never happen
			echo 'Error: missing data!';
		}
	}
	
	/**
	 * Construct table headers from serialized email data.
	 */
	public function tableHeaders(&$results)
	{
		if (!empty($results))
		{
			$postdata = @unserialize($results['0']['postdata']);
			
			if (!empty($postdata))
			{
				return array_keys($postdata);
			}
		}
		
		return array();
	}
	
	/**
	 * Get the number of leads this month as a total limit.
	 */
	public function isLimitReached()
	{
		$since = strtotime('first day of this month');
		$since = addslashes($since);
		$total = 0;
		
		$sql = "SELECT COUNT(*) AS `tot`
				FROM `{$this -> _prefix}59_leads`
				WHERE `created_time` > '{$since}'
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			$r = mysql_fetch_assoc($q);
			$total = $r['tot'];
		}
		
		if ($total > 20)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get the latest contact form 7 leads
	 * @used: on leads page
	 */
	public function getNewLeads($entity_id = 0)
	{
		$entity_id = intval($entity_id);
		$results = array();
		
		$sql = "SELECT *
				FROM `{$this -> _prefix}59_leads`
				WHERE `entity_id` = '{$entity_id}'
					AND `user_id` IS NULL
					AND `type` = 'form'
				ORDER BY `created_time` ASC
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			while ($r = mysql_fetch_assoc($q))
			{
				$results[] = $r;
			}
		}
		return $results;
	}
	
	/**
	 * Are there new leads since $time ?
	 */
	public function checkForNewLeads($time = 0)
	{
		$sql = "SELECT 1
				FROM `{$this -> _prefix}59_leads`
				WHERE `user_id` IS NULL
					AND `created_time` > $time
				";
		$q = mysql_query($sql, $this -> _db);
		return mysql_num_rows($q);
	}
	
	/**
	 * Get paged cf7 user leads.
	 * @used: in crm page.
	 */
	public function getUserLeads($entity_id = 0, $user_id = 0, $page = 0, $filters = array())
	{
		$page = intval($page);
		$entity_id = intval($entity_id);
		$user_id = intval($user_id);
		$results = array();
		$where = (!empty($user_id)) ? " AND `user_id` = '{$user_id}'" : " AND `user_id` IS NOT NULL";
				
		// filters
		foreach ($filters as $key => $value)
		{
			$value = addslashes($value);
			
			if ($key == 'keyword')
			{
				$where .= " AND (`user_name` LIKE '%{$value}%' OR `postdata` LIKE '%{$value}%')";
			}
			else
			{
				$where .= " AND `{$key}` = '{$value}'";
			}
		}
		
		// paging init
		$start = $page * $this->_itemsPerPage;

		$sortBy = $_COOKIE['59sec_sb'];if($sortBy=='') $sortBy='grabbed';
		$sortDir = $_COOKIE['59sec_sd'];if($sortDir=='') $sortDir='desc';
		$sort = "";
		if($sortBy=='time' || $sortBy == '')
			$sort = "reserved_time - created_time";
		else if($sortBy=='grabbed')
			$sort = "reserved_time";
		else if($sortBy=='agent')
			$sort = "user_id";
		
		$sql = "SELECT *
				FROM `{$this -> _prefix}59_leads`
				WHERE `entity_id` = '{$entity_id}'
					AND `type` = 'form'
					{$where}
				ORDER BY ".$sort." ".$sortDir."
				LIMIT {$start}, {$this->_itemsPerPage}
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			while ($r = mysql_fetch_assoc($q))
			{
				$results[] = $this -> stripslashes_deep($r);
			}
		}
		
		return $results;
	}
	
	/**
	 * Set paging links for contact form 7 tables.
	 * @used: in crm page
	 */
	public function pagerUserLeads($entity_id = 0, $user_id = 0, $page = 0, $filters = array())
	{
		$paging = new stdClass();
		$paging -> current = intval($page);
		$paging -> pages = array();
		$entity_id = intval($entity_id);
		$user_id = intval($user_id);
		$where = (!empty($user_id)) ? " AND `user_id` = '{$user_id}'" : ' AND `user_id` IS NOT NULL';
		
		// filters
		foreach ($filters as $key => $value)
		{
			$value = addslashes($value);
			
			if ($key == 'keyword')
			{
				$where .= " AND (`user_name` LIKE '%{$value}%' OR `postdata` LIKE '%{$value}%')";
			}
			else
			{
				$where .= " AND `{$key}` = '{$value}'";
			}
		}
		
		$sql = "SELECT COUNT(`ID`) AS `total`
				FROM `{$this -> _prefix}59_leads`
				WHERE `entity_id` = '{$entity_id}'
					AND `type` = 'form'
					{$where}
				ORDER BY `created_time` ASC
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		$r = (!empty($q)) ? mysql_fetch_assoc($q) : array();
		$totalItems = $r['total'];
		$nrPages = ceil($totalItems / $this->_itemsPerPage);
		
		if ($totalItems > $this->_itemsPerPage)
		{
			for ($i = 0; $i < $nrPages; $i++)
			{	
				$paging -> pages[] = array(
				'title' => $i + 1,
				'page' => $i,
				'active' => ($paging -> current == $i) ? 'active' : '',
				);
			}
		}
		
		$paging -> next = $paging -> current + 1;
		$paging -> prev = $paging -> current - 1;
		
		if ($paging -> next == $nrPages || $nrPages == 0)
		{
			$paging -> next = false;
		}
		
		if ($paging -> prev < 0 || $nrPages == 0)
		{
			$paging -> prev = false;
		}
		
		return $paging;
	}
	
	/**
	 * Get data for 1 lead.
	 * @used: in notes.
	 */
	public function get($id = 0)
	{
		$id = intval($id);
		
		$sql = "SELECT *
				FROM `{$this -> _prefix}59_leads`
				WHERE `id` = '{$id}'
				";
				
		$q = mysql_query($sql, $this -> _db);
		$r = mysql_fetch_assoc($q);
		$r = $this -> stripslashes_deep($r);
		
		return $r;
	}
	
	/**
	 * User Statistics: average response time
	 */
	public function getAgentAverageResponse($user_id)
	{
		$user_id = intval($user_id);
		$created = 0;
		$reserved = 0;
		$count = 1;
		
		$sql = "SELECT *
				FROM `{$this -> _prefix}59_leads`
				WHERE `user_id` = '{$user_id}'
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			while ($r = mysql_fetch_assoc($q))
			{
				$created += $r['created_time'];
				$reserved += $r['reserved_time'];
				$count++;
			}
			
			$created = round($created / $count);
			$reserved = round($reserved / $count);
		}
		
		$result = $reserved - $created;
		
		if (empty($result))
		{
			return 'N/A';
		}
		return $this->timeFormat($created, $reserved);
	}
	
	public function getAgentLeads($user_id, $status = '')
	{
		$user_id = intval($user_id);
		$status = addslashes($status);
		$where = (!empty($status)) ? "`status` = '{$status}'" : '';
		
		$sql = "SELECT COUNT(*) AS `total`
				FROM `{$this -> _prefix}59_leads`
				WHERE `user_id` = '{$user_id}'
					{$where}
				ORDER BY `created_time` ASC
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			$r = mysql_fetch_assoc($q);
			
			return $r['total'];
		}
		
		return 0;
	}
	
	public function getTotalUnansweredLeads()
	{	
		$sql = "SELECT COUNT(*) AS `total`
				FROM `{$this -> _prefix}59_leads`
				WHERE `user_id` IS NULL
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			$r = mysql_fetch_assoc($q);
			
			return $r['total'];
		}
		
		return 0;
	}
	
	public function getTotalLeads($date)
	{
		$date1 = date('y-m-d 00:00:01', $date);
		$date2 = date('y-m-d 23:59:59', $date);
		$date1 = strtotime($date1);
		$date2 = strtotime($date2);
		
		$sql = "SELECT COUNT(*) AS `total`
				FROM `{$this -> _prefix}59_leads`
				WHERE `created_time` BETWEEN '{$date1}' AND '{$date2}'
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			$r = mysql_fetch_assoc($q);
			
			return $r['total'];
		}
		
		return 0;
	}
	
	public function getTotalAgentLeads($user_id = 0, $date)
	{
		$user_id = intval($user_id);
		$date1 = date('y-m-d 00:00:01', $date);
		$date2 = date('y-m-d 23:59:59', $date);
		$date1 = strtotime($date1);
		$date2 = strtotime($date2);
		
		$sql = "SELECT COUNT(*) AS `total`
				FROM `{$this -> _prefix}59_leads`
				WHERE `user_id` = '{$user_id}'
					AND `created_time` BETWEEN '{$date1}' AND '{$date2}'
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			$r = mysql_fetch_assoc($q);
			
			return $r['total'];
		}
		
		return 0;
	}
	
	public function saveAppToken($data)
	{
		$device_token = $data['device_token'];
		$device_token = addslashes($device_token);
		$type = addslashes($data['type']);
		
		$sql = "DELETE
				FROM `{$this -> _prefix}59_tokens`
				WHERE `device_token` = '{$device_token}'
					AND `type` = '{$type}'
				";
				
		mysql_query($sql, $this -> _db);
		
		$sql = "INSERT
				INTO `{$this -> _prefix}59_tokens` (`device_token`, `type`)
				VALUES ('{$device_token}', '{$type}')
				";
				
		mysql_query($sql, $this -> _db);
	}
	
	public function deleteAppToken($data)
	{
		$device_token = $data['device_token'];
		$device_token = addslashes($device_token);
		$type = addslashes($data['type']);
		
		$sql = "DELETE
				FROM `{$this -> _prefix}59_tokens`
				WHERE `device_token` = '{$device_token}'
					AND `type` = '{$type}'
				";
				
		mysql_query($sql, $this -> _db);
	}
	
	function getAppTokens()
	{
		$results = array();
		
		$sql = "SELECT `device_token` AS `token`, `type`
				FROM `{$this -> _prefix}59_tokens`
				";
				
		$q = mysql_query($sql, $this -> _db);
		
		if (!empty($q))
		{
			while ($r = mysql_fetch_assoc($q))
			{
				$results[] = $r;
			}
		}
		
		return $results;
	}
	
	/**
	 * Format lead data as email
	 */
	public function leadAsMail($lead)
	{
		$lead = (array) $lead;
		$message = '';
		$create = date('d/m/y H:i:s', $lead['created_time']);
		$postdata = (array) unserialize($lead['postdata']);
		$message .= "{$lead['subject']}: \n";
		$message .= "created: {$create}: \n";
		
		foreach ($postdata as $key => $value)
		{
			$message .= "{$key}: {$value}\n";
		}
		
		return $message."\n\n";
	}
	
	public function leadStatusOptions($status = '')
	{
		$options = '';
		
		foreach($this -> _statuses as $id => $item)
		{
			$selected = ($id == $status) ? 'selected="selected"' : '';
			$options .= "<option value=\"{$id}\" {$selected}>{$item}</option>";
		}
		
		return $options;
	}
	
	public function leadFilterStatusOptions()
	{
		$options = "<option value=\"\">All</option>";;
		$status = (isset($_POST['status'])) ? $_POST['status'] : '';
		
		foreach($this -> _statuses as $id => $item)
		{
			$selected = ($id == $status) ? 'selected="selected"' : '';
			$options .= "<option value=\"{$id}\" {$selected}>{$item}</option>";
		}
		
		return $options;
	}
	
	public function timeFormat($seconds = 0, $to = 0)
	{
		if (empty($to))
		{
			$to = time();
		}
		
		$seconds = $to - $seconds;
		$time = '';
		$hours = intval($seconds / 3600);
		$seconds = $seconds - ($hours * 3600);
		$minutes = intval($seconds / 60);
		$seconds = $seconds - ($minutes * 60);
		
		if ($hours > 0)
		{
			$time .= $hours.'h ';
		}
		if ($minutes > 0)
		{
			$time .= $minutes.'min ';
		}
		$time .= $seconds.'sec';
		
		return $time;
	}
	
	public function grabedAt($timestamp = 0)
	{
		return date('d/m/y H:i', $timestamp);
	}
	
	public function getIP() 
	{
		$ip_address = $_SERVER['REMOTE_ADDR'];

		if (!empty($ip_address))
		{
			return $ip_address;
		}
		
		return 'unknown';
	}
	
	public function stripslashes_deep($value)
	{
		if (is_array($value))
		{
			foreach ($value as $key => $data)
			{
				if ($key != 'postdata')
				{
					$value[$key]= stripslashes_deep($data);
				}
			}
		}
		elseif (is_object($value))
		{
			$vars = get_object_vars($value);
			
			foreach ($vars as $key => $data)
			{
				if ($key != 'postdata')
				{
					$value->{$key} = stripslashes_deep($data);
				}
			}
		}
		elseif (is_string($value))
		{
			$value = stripslashes($value);
		}

		return $value;
	}
	
	function repairSerializedArray($serialized)
	{
		$tmp = preg_replace('/^a:\d+:\{/', '', $serialized);
		// operates on and whittles down the actual argument
		return $this -> repairSerializedArray_R($tmp); 
	}
	
	function repairSerializedArray_R(&$broken)
	{
		// array and string length can be ignored
		// sample serialized data
		// a:0:{}
		// s:4:"four";
		// i:1;
		// b:0;
		// N;
		$data		= array();
		$index		= null;
		$len		= strlen($broken);
		$i			= 0;
		
		while(strlen($broken))
		{
			$i++;
			if ($i > $len)
			{
				break;
			}
			
			if (substr($broken, 0, 1) == '}') // end of array
			{
				$broken = substr($broken, 1);
				return $data;
			}
			else
			{
				$bite = substr($broken, 0, 2);
				switch($bite)
				{	
					case 's:': // key or value
						$re = '/^s:\d+:"([^\"]*)";/';
						if (preg_match($re, $broken, $m))
						{
							if ($index === null)
							{
								$index = $m[1];
							}
							else
							{
								$data[$index] = $m[1];
								$index = null;
							}
							$broken = preg_replace($re, '', $broken);
						}
					break;
					
					case 'i:': // key or value
						$re = '/^i:(\d+);/';
						if (preg_match($re, $broken, $m))
						{
							if ($index === null)
							{
								$index = (int) $m[1];
							}
							else
							{
								$data[$index] = (int) $m[1];
								$index = null;
							}
							$broken = preg_replace($re, '', $broken);
						}
					break;
					
					case 'b:': // value only
						$re = '/^b:[01];/';
						if (preg_match($re, $broken, $m))
						{
							$data[$index] = (bool) $m[1];
							$index = null;
							$broken = preg_replace($re, '', $broken);
						}
					break;
					
					case 'a:': // value only
						$re = '/^a:\d+:\{/';
						if (preg_match($re, $broken, $m))
						{
							$broken			= preg_replace('/^a:\d+:\{/', '', $broken);
							$data[$index]	= $this -> repairSerializedArray_R($broken);
							$index = null;
						}
					break;
					
					case 'N;': // value only
						$broken = substr($broken, 2);
						$data[$index]	= null;
						$index = null;
					break;
				}
			}
		}
		
		return $data;
	}
}