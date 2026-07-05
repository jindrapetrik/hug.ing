<?php

$recipientKey = dl_get("key");
$time = dl_get("time");
$token = dl_get("token");
$returned = dl_get("returned") === "1";

$tokenType = $returned ? "return" : "accept";
$expectedToken = md5("$recipientKey|$time|$tokenType|$dl_token_salt");

if ($expectedToken !== $token)
{
    http_response_code(400);
    dl_exit("Invalid token", "Neplatný token");
}

$db = dl_db_connect();
$s = $db->prepare("SELECT author, message FROM hug WHERE recipientKey = ?");
if ($s === false)
{
    dl_exit("Cannot access hug");
}
if (!$s->execute([$recipientKey]))
{
    dl_exit("Cannot load hug");
}

if (($row = $s->fetch(PDO::FETCH_ASSOC)) === false)
{
    http_response_code(404);
    dl_exit("Hug not found", "Objetí nenalezeno");
}

$name = $row["author"];
$message = $row["message"];


$video = '<video
    class="hug"
    autoplay
    muted
    loop
    playsinline
    width="600" height="600" title="' . dl_lang_encs("Hug animation", "Animace objetí") . '">
    <source src="/bear.mp4" type="video/mp4">
</video>';

$returnTime = time();
$returnToken = md5("$recipientKey|$returnTime|return|$dl_token_salt");

if ($returned)
{
    $title = dl_lang_encs("Hug accepted and hugged back", "Objetí přijato a opětováno");
    
}
else
{
    $title = dl_lang_encs("Hug accepted", "Objetí přijato");
}
dl_page_begin('<meta name="robots" value="noindex,nofollow">');
echo '<div class="hug-accepted">';
if ($lang === "cs")
{
    if ($returned)
    {
        echo '<h2>Přijato a opětováno objetí</h2>';
    }
    else 
    {
        echo '<h2>Přijato objetí</h2>';
    }
    
    echo '<div class="content">'
    . $video
    . '<div class="text">';
    if (!empty($message))
    {
        echo '<div class="name-sending">';
        echo dl_escape($name) .' vám posílá objetí se vzkazem:';
        echo '</div>';
        echo '<div class="message">';
        echo nl2br(dl_escape($message));
        echo '</div>';
    }
    else 
    {
        echo '<div class="name-sending">';
        echo dl_escape($name) .' vám posílá objetí.';
        echo '</div>';
    }            
} 
else 
{
    if ($returned)
    {
        echo '<h2>Received hug and hugged back</h2>';
    }
    else
    {
        echo '<h2>Received hug</h2>';
    }
    echo '<div class="content">'
            . $video
            . '<div class="text">';
    if (!empty($message))
    {
        echo '<div class="name-sending">';
        echo dl_escape($name) .' is sending you a hug with a message:';
        echo '</div>';
        echo '<div class="message">';
        echo nl2br(dl_escape($message));
        echo '</div>';
    }
    else 
    {
        echo '<div class="name-sending">';
        echo dl_escape($name) .' is sending you a hug.';
        echo '</div>';
    }
}
    if ($returned)
    {
        echo '<div class="returned">' . dl_lang_encs("You hugged the person back!", "Objetí bylo vámi opětováno!") . '</div>';
    }
    else
    {    
        echo '<div class="return">';
        echo dl_lang_encs('Do you want to hug the person back?', 'Chcete objetí opětovat?');
        echo '<form action="' . "$dl_server/$lang/xreturn". '" method="POST">';
        echo '<input type="hidden" name="key" value="' . $recipientKey . '" />';
        echo '<input type="hidden" name="time" value="' . $returnTime . '" />';
        echo '<input type="hidden" name="token" value="' . $returnToken . '" />';            
        echo '<input type="submit" class="button" value="' . dl_lang_encs("Hug back", "Opětovat") . '"/>';
        echo '</form>';        
        echo '</div>';
    }
    
    echo '<div class="own-hug">';
    if (!$returned)
    {
        echo dl_lang_encs("or", "nebo") . "<br>";
    }
    echo '<a href="'."$dl_server/$lang/".'">' . dl_lang_encs("Send my own hug to someone else", "Poslat vlastní objetí někomu jinému") . '</a>';
    echo '</div>';
?>
</div>
</div>
<?php
dl_page_end();
