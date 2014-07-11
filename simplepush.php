<?php

//send push notifications to devices
$devices = $leadsModel -> getAppTokens();

$i=0;
foreach($devices as $device)
{
	$deviceTokens[$i]['type'] = $device['type'];
	$deviceTokens[$i]['token'] = $device['token'];
	$i++;
}

$message = $leadsModel -> leadAsMail($lead);
$message = substr($message, 0, 150);

if(!empty( $deviceTokens ))
{
	if (trim($message) != '')
	{
		for($i = 0; $i<count($deviceTokens); $i++)
		{
			$payload['device_tokens'][] = $deviceTokens[$i];
		}
		$payload['aps'] = array('alert' => "$message", 'sound' => 'cashregister.aiff', 'badge' => 1);
	}
	
	$payload['domain'] = get_real_site_url();
	$payload = "json=".serialize($payload);
	
	$url = 'https://www.59sec.com/notifications.php';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	
	if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	curl_exec($ch);
}
