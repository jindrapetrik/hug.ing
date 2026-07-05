<?php

header("Content-type: text/plain; charset=utf-8");
$senderKey = dl_get("key");

$db = dl_db_connect();

$s = $db->prepare("SELECT viewCount,acceptCount,returnedCount FROM hug WHERE senderKey = ?");
if ($s === false)
{    
    echo dl_lang_encs("Cannot load hug", "Hug nelze načíst.");
    exit;
}

if (!$s->execute([$senderKey]))
{
    echo dl_lang_encs("Cannot load hug", "Hug nelze načíst.");
    exit;
}

if (($row = $s->fetch(PDO::FETCH_ASSOC)) === false)
{
    echo dl_lang_encs("Hug not found", "Hug nenalezen.");
    exit;
}


echo dl_hug_status($row);
exit;
