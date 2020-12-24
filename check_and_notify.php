<?php

require_once 'config.php';
require_once 'utils.php';

const ENDPOINT_TO_CHECK = [];

foreach (ENDPOINT_TO_CHECK as $endpoint) {
    $curlHandle = curl_init($endpoint);
    curl_exec($curlHandle);

    if (curl_errno($curlHandle)) {
        log($error = curl_error($curlHandle));
        notifySlack([sprintf(':x: %', $endpoint) => sprintf("Unable to call %s, %s", $endpoint, $error)]);
    } elseif (isServerError($statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE))) {
        notifySlack([sprintf(':x: %', $endpoint) => $statusCode]);
    }
    curl_close($curlHandle);
}
