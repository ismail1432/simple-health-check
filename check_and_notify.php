<?php

require_once 'config.php';

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

function isServerError($statusCode): bool
{
    return $statusCode >= 500 && $statusCode < 600;
}

function logError(string $content): void
{
    file_put_contents('log/log_'.date("j.n.Y").'.log', $content, FILE_APPEND);
}

foreach (getEndpointToCheck() as $endpoint) {
    $curlHandle = curl_init($endpoint);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curlHandle);

    if (curl_errno($curlHandle) || !$response) {
        $message = sprintf("Unable to call %s, error: %s", $endpoint, curl_error($curlHandle));
        logError($message);
        notifySlack($message);
    } elseif (isServerError($statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE))) {
        notifySlack(sprintf("%s seems down with status code %s", $endpoint, $statusCode));
    }
    curl_close($curlHandle);
}
