<?php

$title = dl_lang_encs("Hug cancelled", "Objetí odvoláno");

dl_page_begin();

if ($lang === "cs") {?>
<h2>Objetí odvoláno</h2>
<p>Veškeré odkazy související s objetím byly zneplatněny.</p>
<p>Můžete přejít na <a href="/<?php echo $lang?>/">hlavní stranu</a> a obejmout třeba někoho jiného.</p>
<?php } else { ?>
<h2>Hug Cancelled</h2>
<p>All links associated with this hug have been deactivated.</p>
<p>You can return to the <a href="/<?php echo $lang?>/">home page</a> and send a hug to someone else instead.</p>
<?php
}

dl_page_end();
