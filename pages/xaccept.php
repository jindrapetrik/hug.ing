<?php

$recipientKey = dl_post("key");
$time = (int) dl_post("time");
$token = dl_post("token");

if (!dl_token_valid($recipientKey, $time, null, $token))
{
    http_response_code(400);
    dl_exit("Invalid token", "Neplatný token");
}

$currentTime = time();

if ($currentTime > $time + $dl_token_lifetime)
{
    dl_exit("Expired token", "Expirovaný token");
}

$db = dl_db_connect();
$s = $db->prepare("UPDATE hug SET acceptCount = acceptCount + 1 WHERE recipientKey = ?");
if ($s !== false)
{
    $s->execute([$recipientKey]);
    if ($s->rowCount() == 0)
    {
        http_response_code(404);        
        dl_exit("Hug not found", "Objetí nenalezeno");
    }
}

$acceptedToken = dl_create_token($recipientKey, $time, "accept");

http_response_code(303);
header("Location: $dl_server/$lang/accepted?key=$recipientKey&time=$time&token=$acceptedToken");
exit;
