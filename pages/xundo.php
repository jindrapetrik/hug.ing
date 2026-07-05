<?php

$senderKey = dl_get("key");
$db = dl_db_connect();
$s = $db->prepare("DELETE FROM hug WHERE senderKey = ?");
if ($s === false)
{
    http_response_code(500);
    dl_exit("Cannot delete hug");
}
if (!$s->execute([$senderKey]))
{
    http_response_code(500);
    dl_exit("Cannot delete hug");
}
if ($s->rowCount() < 1)
{
    http_response_code(404);
    dl_exit("Hug not found");
}

http_response_code(302);
header("Location: $dl_server/$lang/undone");
exit;


