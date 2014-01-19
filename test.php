<?php
print '<h1>Testing</h1>';

$c = curl_init('https://www.facebook.com/groups/120696471425768/permalink/255749264587154/');
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//curl_setopt(... other options you want...)

$html = curl_exec($c);

if (curl_error($c))
    die(curl_error($c));

// Get the status code
$status = curl_getinfo($c, CURLINFO_HTTP_CODE);

curl_close($c);
?>