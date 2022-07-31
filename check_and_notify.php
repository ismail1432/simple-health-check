<?php

require_once 'config.php';

function sendNotif(string $message): void
{
	if (isSlackActive()) {
		notifySlack($message);
	}
	if (isDiscordActive()) {
		notifyDiscord($message);
	}
}

function notifySlack(string $message): void
{
	$curl = curl_init();
	curl_setopt_array(
		$curl,
		[
			CURLOPT_URL => getSlackUrl(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				'text' => ":x: $message",
				'channel' => getSlackChannel()
			]),
			CURLOPT_HTTPHEADER => [
				sprintf('Authorization: Bearer %s', getSlackToken()),
				"Content-type: application/json",
			]
		]
	);

	curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		logError($err);
	}
}

function notifyDiscord(string $message): void
{
	$curl = curl_init();
	curl_setopt_array(
		$curl,
		[
			CURLOPT_URL => getDiscordUrl(),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				'username' => 'Simple Health Check BOT',
				'avatar_url' => 'https://picsum.photos/40/40',
				'tts' => false,
				'embeds' => [
					[
						'type' => 'rich',
						'title' => 'Simple Health Check',
						'description' => $message,
						'timestamp' => date('c', strtotime('now')),
						'color' => hexdec('BB2124'),
						'fields' => []
					]
				]
			]),
			CURLOPT_HTTPHEADER => [
				"Content-type: application/json",
			]
		]
	);

	curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		logError($err);
	}
}

function isServerError($statusCode): bool
{
	return $statusCode >= 500 && $statusCode < 600;
}

function logError(string $content): void
{
	file_put_contents('log/log_' . date("j.n.Y") . '.log', $content, FILE_APPEND);
}

foreach (getEndpointToCheck() as $endpoint) {
	$curlHandle = curl_init($endpoint);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($curlHandle);

	if (curl_errno($curlHandle) || !$response) {
		$message = sprintf("Unable to call %s, error: %s", $endpoint, curl_error($curlHandle));
		logError($message);
		sendNotif($message);
	} elseif (isServerError($statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE))) {
		sendNotif(sprintf("%s seems down with status code %s", $endpoint, $statusCode));
	}
	curl_close($curlHandle);
}
