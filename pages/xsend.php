<?php

function generateRandomString(int $length = 8): string
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $maxIndex = strlen($characters) - 1;
    $result = '';

    for ($i = 0; $i < $length; $i++) {
        $result .= $characters[random_int(0, $maxIndex)];
    }

    return $result;
}


$time = (int) dl_post("time");
$token = dl_post("token");

if (!dl_token_valid("", $time, "send", $token))
{
    http_response_code(400);
    dl_exit("Invalid token", "Neplatný token");
}

$currentTime = time();

if ($currentTime > $time + $dl_token_lifetime)
{
    dl_exit("Expired token", "Expirovaný token");
}

if (!dl_verify_recaptcha("send_hug")) 
{
    http_response_code(400);
    dl_exit(
        "Verification failed. Please try again.",
        "Ověření se nezdařilo. Zkuste to prosím znovu."
    );
}

$db = dl_db_connect();

$author = dl_post("name");

if (mb_strlen($author) > 50)
{
    dl_exit("Name is too long", "Jméno je příliš dlouhé");
}

$message = dl_post("message");

$message = str_replace("\r", "", $message);
if (mb_strlen($message) > 1000)
{
    dl_exit("Message is too long", "Zpráva je příliš dlouhá");
}

do
{
    $recipientKey = generateRandomString(8);
    $senderKey = generateRandomString(12);


    $s = $db->prepare("SELECT 1 FROM hug WHERE recipientKey = ? OR senderKey = ?");
    if ($s === false)
    {
        dl_exit("Cannot create hug", "Nelze vytvořit objetí");
    }
    if (!$s->execute([$recipientKey, $senderKey]))
    {
        dl_exit("Cannot create hug", "Nelze vytvořit objetí");
    }
    $exists = $s->fetch(PDO::FETCH_ASSOC) !== false;

} while($exists);

$s = $db->prepare("INSERT INTO hug(author, message, dateCreated, recipientKey, senderKey)"
        . " VALUES(?,?,NOW(),?,?)");
if ($s === false)
{
    dl_exit("Cannot insert new hug.");
}
$s->execute([$author, $message, $recipientKey, $senderKey]);
if ($s === false)
{
    dl_exit("Cannot insert new hug.");
}

http_response_code(303);
header("Location: $dl_server/$lang/sent?key=$senderKey");
exit;
