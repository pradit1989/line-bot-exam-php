<?php // callback.php

require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = 'kxU1f8fK5geIhCsb7Wtt1J5rxO3sfvnRah2lr51d/2KRPRvWb4n2FmTHtCDcdc4KtraiDTY1slPupDVhOTkOrsuMjd/ST1Fn9u9UMmUsHesaHPS9H2SO8FCDL64GOsyKTWv0Q7WP6yA18WJpqHyFVwdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			//$text = $event['source']['userId'];
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			
			if(substr($text,0, 1) == '#'){
				$text = trim(substr($text, 1));
				
				$text =str_replace(' ', '', $text);

					$ch = curl_init('http://www.police4.go.th/phonebook/linebot.php?name='.$text);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
					curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
					$result = curl_exec($ch);

					
					$value = "";
					
						$str2 = explode("||",$result);

						foreach ($str2 as  $val) {
							 $value .= $val."\r\n\r\n";
						}
					

				// Build message to reply back
				$value = trim($value);
				$messages = [
					'type' => 'text',
					'text' => $value
				];

				// Make a POST Request to Messaging API to reply to sender
				$url = 'https://api.line.me/v2/bot/message/reply';
				$data = [
					'replyToken' => $replyToken,
					'messages' => [$messages],
				];
				$post = json_encode($data);
				$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$result = curl_exec($ch);
				curl_close($ch);

				echo $result . "\r\n";
			}//if #	
		}
	}
}
echo "OK";
?>
