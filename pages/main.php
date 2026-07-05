<?php

dl_page_begin('<meta name="description" content="Page for sending virtual hugs to your friends.">

<meta property="og:type" content="website">
<meta property="og:title" content="hug.ing - send virtual hugs">
<meta property="og:description" content="Page for sending virtual hugs to your friends.">
<meta property="og:image" content="' . "$dl_server/logo.png". '">
<meta property="og:url" content="' . "$dl_server". '">
<meta property="og:site_name" content="hug.ing - send virtual hugs">');        
?>
<div class="main">
<p>
<?php if ($lang==="cs") { ?>    
Někdy stačí vědět, že si na nás někdo vzpomněl.<br />
Pošli virtuální objetí člověku, na kterém ti záleží.<br />
Přidej krátký vzkaz, sdílej odkaz a daruj někomu malý okamžik radosti. 🤗<br />
<?php } else { ?>
Sometimes, all it takes is knowing that someone is thinking of you.<br />
Send a virtual hug to someone you care about. <br />
Add a short message, share the link, and give someone a small moment of warmth and joy. 🤗<br />
</p>
<?php } ?>
<p>
    <a href="send" class="button"><?php echo dl_lang_encs("Begin", "Začít");?></a>
</p>
</div>
<?php
dl_page_end();