<?php
// Streams the Google Drive video through this server so <video> can autoplay/loop it.
$fileId = '1tPcQUEvHuzTeDDDpc4h0uN24ylpHeEUK';
$url = 'https://drive.usercontent.google.com/download?id=' . $fileId . '&export=download&confirm=t';

$headers = [];
if (!empty($_SERVER['HTTP_RANGE'])) {
    $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
}

$sent = false;
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$sent) {
        $name = strtolower(strtok($line, ':'));
        if (preg_match('#^HTTP/\S+ (\d{3})#', $line, $m)) {
            http_response_code((int)$m[1]);
        } elseif (in_array($name, ['content-type', 'content-length', 'content-range', 'accept-ranges'])) {
            header(trim($line));
        }
        return strlen($line);
    },
    CURLOPT_WRITEFUNCTION  => function ($ch, $data) {
        echo $data;
        flush();
        return strlen($data);
    },
]);
set_time_limit(0);
curl_exec($ch);
curl_close($ch);
