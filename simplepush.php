<?php

//send push notifications to devices
$devices = $leadsModel -> getAppTokens();

$i=0;
foreach( $devices as $device )
{
	// if( $device['type'] == "ios" )
	{
		$deviceTokens[$i]['type'] = $device['type'];
		$deviceTokens[$i]['token'] = $device['token'];
		$i++;
	}
}


// $message = "Test push notification4!";
$message = $leadsModel -> leadAsMail($lead);
$message = substr($message, 0, 150);

if( !empty( $deviceTokens ) )
{
	if (trim($message) != '')
	{
		for($i = 0; $i<count($deviceTokens); $i++) {
			$payload['device_tokens'][] = $deviceTokens[$i];
		}
		$payload['aps'] = array('alert' => "$message", 'sound' => 'cashregister.aiff', 'badge' => 1);
	}
	
	// $payload = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($payload));
	$payload = "json=".serialize($payload);
	
	$url = 'http://www.59sec.com/notifications.php';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	
	if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off')
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

	curl_exec($ch);
	// $curl_scraped_page = curl_exec($ch);
	// print "<pre>"; print_r( $curl_scraped_page ); print "</pre>"; exit;
}
