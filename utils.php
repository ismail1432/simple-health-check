<?php

function notifySlack(array $data): void
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, getSlackUrl());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    curl_exec($ch);
    if (curl_errno($ch)) {
        log(curl_error($ch));
    }
    curl_close($ch);
}

function isServerError($statusCode): bool
{
    return $statusCode >= 500 && $statusCode < 600;
}

function getSlackUrl(): string
{
    return SLACK_URL;
}

function log(string $content): void
{
    file_put_contents('log/log_'.date("j.n.Y").'.log', $content, FILE_APPEND);
}
