<?php

if (!isset($sound) || empty($sound))
{
	$sound = 'cashregister.aiff';
}

//send push notifications to devices
$devices = $leadsModel -> getAppTokens();

$i=0;
foreach($devices as $device)
{
	$deviceTokens[$i]['type'] = $device['type'];
	$deviceTokens[$i]['token'] = $device['token'];
	$i++;
}

$leadsModel -> agentNotified($lead);
$message = $leadsModel -> leadAsMail($lead);
$message = substr($message, 0, 150);
$payload = array();

if(!empty($deviceTokens))
{
	if (trim($message) != '')
	{
		for($i = 0; $i<count($deviceTokens); $i++)
		{
			$payload['device_tokens'][] = $deviceTokens[$i];
		}
		$payload['aps'] = array('alert' => "$message", 'sound' => $sound, 'badge' => 1);
	}
	
	$payload['domain'] = site_url();
	$payload = "json=".urlencode(json_encode($payload));
	
	$url = 'https://www.59sec.com/notifications_json.php';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	
	if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	curl_exec($ch);
	curl_close($ch);
}
