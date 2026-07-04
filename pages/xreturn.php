<?php

$recipientKey = dl_post("key");
$time = dl_post("time");
$token = dl_post("token");

$expectedToken = md5("$recipientKey|$time|return|$dl_token_salt");

if ($expectedToken !== $token)
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
$s = $db->prepare("UPDATE hug SET returnedCount = returnedCount + 1 WHERE recipientKey = ?");
if ($s !== false)
{
    $s->execute([$recipientKey]);
    if ($s->rowCount() == 0)
    {
        http_response_code(404);        
        dl_exit("Hug not found", "Objetí nenalezeno");
    }
}

$returnedToken = md5("$recipientKey|$time|return|$dl_token_salt");

http_response_code(303);
header("Location: $dl_server/$lang/accepted?key=$recipientKey&time=$time&token=$returnedToken&returned=1");
exit;