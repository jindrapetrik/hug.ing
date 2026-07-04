<?php
    $title = dl_lang_encs("Hug", "Objetí");

    $recipientKey = dl_get("key");        
    
    $db = dl_db_connect();
    
    $s = $db->prepare("SELECT message, author FROM hug WHERE recipientKey = ?");
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
        dl_exit("404 Hug not found", "404 Objetí nenalezeno");
    }
    
    $message = $row["message"];
    $name = $row["author"];
    
    $acceptTime = time();
    $acceptToken = md5("$recipientKey|$acceptTime|$dl_token_salt");
    
    if (empty($name))
    {
        $name = dl_lang_encs("Somebody", "Někdo");
    }
    
    $s = $db->prepare("UPDATE hug SET viewCount = viewCount + 1 WHERE recipientKey = ?");
    if ($s !== false)
    {
        $s->execute([$recipientKey]);
    }
    
    dl_page_begin('<meta name="description" content="' . $name . ' ' . "is sending you a virtual hug. Do you accept it? Will you hug back?" .'">

<meta property="og:type" content="website">
<meta property="og:title" content="' . $name . ' ' ."is sending you a virtual hug" . '">
<meta property="og:description" content="' . $name . ' ' . "is sending you a virtual hug. Do you accept it? Will you hug back?" . '">
<meta property="og:image" content="' . "$dl_server/logo.png". '">
<meta property="og:url" content="' . "$dl_server/h/$recipientKey". '">
<meta property="og:site_name" content="hug.ing - send virtual hugs">
<meta name="robots" value="noindex,nofollow">');
  
?>
<div class="hug-accept">
    <h2><?php echo dl_lang_encs("Hug", "Objetí"); ?></h2>
    <div>
    <span>
    <?php
    echo htmlspecialchars($name);
    echo " ";
    echo dl_lang_encs("is sending you a virtual hug.<br> Do you accept it?", "vám posílá virtuální objetí. <br>Přijmete ho?");
    ?>
    </span>
        <br>
    <span id="choices">
        <form action="<?php echo "$dl_server/$lang/xaccept"; ?>" method="POST" class="accept" id="accept-form">
                    <input type="submit" value="<?php echo dl_lang_encs("Yes, I accept", "Ano, přijímám");?>" class="button">
                    <input type="hidden" name="key" value="<?php echo $recipientKey; ?>"/>
                    <input type="hidden" name="time" value="<?php echo $acceptTime; ?>"/>
                    <input type="hidden" name="token" value="<?php echo $acceptToken; ?>"/>
        </form>
        <a href="#" id="not-now-a" class="button" onclick="notnow(); return false;"><?php echo dl_lang_encs("Not now", "Teď ne"); ?></a>    
    </span>
    <span class="hidden" id="not-now-response">
        <?php echo dl_lang_encs("Not now. Never mind, maybe next time.", "Teď ne. Nevadí, snad někdy příště."); ?>
    </span>
    </div>
</div>
    <script>
    function notnow()
    {
        const choicesP = document.getElementById("choices");
        const notNowResponse = document.getElementById("not-now-response");
        choicesP.style.display = "none";
        notNowResponse.style.display = "inline";        
    }    
    </script>
<?php

dl_page_end();