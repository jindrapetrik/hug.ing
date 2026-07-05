<?php

$senderKey = dl_get("key");

$db = dl_db_connect();
$s = $db->prepare("SELECT recipientKey, infoDisplayed, viewCount, acceptCount, returnedCount, message, author FROM hug WHERE senderKey = ?");
if ($s === false)
{
    dl_exit("Cannot get hug id");
}
if (!$s->execute([$senderKey]))
{
    dl_exit("Cannot get hug id");
}
if (($row = $s->fetch(PDO::FETCH_ASSOC)) === false)
{
    http_response_code(404);
    dl_exit("404 Hug not found", "404 Objetí nenalezeno");
}


$s = $db->prepare("UPDATE hug SET infoDisplayed = 1 WHERE senderKey = ?");
$s->execute([$senderKey]);


$recipientKey = $row["recipientKey"];
$viewCount = $row["viewCount"];
$acceptCount = $row["acceptCount"];
$returnedCount = $row["returnedCount"];
$message = $row["message"];
$name = $row["author"];

$recipientLink = "$dl_server/h/$recipientKey";
$senderLink = "$dl_server/$lang/sent?key=$senderKey";
$undoLink = "$dl_server/$lang/undo?key=$senderKey";


$statuses = [];
if ($acceptCount == 0 && $returnedCount == 0 && $viewCount == 0) {
    $statuses[] = dl_lang_encs('Hug was created.', 'Objetí bylo vytvořeno.');
}
if ($viewCount > 0) {
    $cnt = $viewCount > 1 ? ' (' . $viewCount . '×)' : '';
    $statuses[] = dl_lang_encs('Hug arrived.', 'Objetí dorazilo.') . $cnt;    
}
if ($acceptCount > 0) {
    $cnt = $acceptCount > 1 ? ' (' . $acceptCount . '×)' : '';
    $statuses[] = dl_lang_encs('Hug was accepted.', 'Objetí bylo přijato.') . $cnt;    
}
if ($returnedCount > 0) {
    $cnt = $returnedCount > 1 ? ' (' . $returnedCount . '×)' : '';
    $statuses[] = dl_lang_encs('Hug was returned.', 'Objetí bylo opětováno.') . $cnt;        
}
     


$title = dl_lang_encs("Link created", "Odkaz vytvořen");
dl_page_begin('<meta name="robots" value="noindex,nofollow">');

if ($lang === "cs") {
?>
<h2>Stránka pro odesílatele objetí</h2>
<p>
    Odkaz <span class="copy-link" id="aRecipient" href="<?php echo $recipientLink; ?>"><?php echo $recipientLink; ?></span>
    <button class="copy" id="btnCopyRecipient" aria-label="Kopírovat odkaz pro příjemce" title="Kopírovat odkaz pro příjemce">📋</button> zkopíruj a pošli kamarádovi, kterého chceš obejmout.<br>
</p>
<p>
    Druhý (soukromý) odkaz je pro tebe jako odesílatele: <a id="aSender" href="<?php echo $senderLink; ?>"><?php echo $senderLink; ?></a>
    <button class="copy" id="btnCopySender" aria-label="Kopírovat odkaz pro odesílatele" title="Kopírovat odkaz pro odesílatele">📋</button>.<br>
    Jde o tuto aktuální stránku, na které se nacházíš. Tu si ulož, aby se ti neztratila. Nikomu jinému ji neposílej.<br>
</p>
<h3>Stav objetí</h3>
<p>
    <?php
        echo implode("<br>" , $statuses);
    ?>
</p>
<?php if (!empty($name) || !empty($message)) {?>
<h3>Detaily objetí</h3>
<p>
    <strong>Odesílatel:</strong> <?php echo dl_escape($name); ?><br>
    <strong>Vzkaz:</strong><br>
    <?php echo nl2br(dl_escape($message)); ?>
</p>
<?php } ?>
<h3>Odvolání objetí</h3>
<p>
    Pokud myslíš, že jsi se spletl, a chceš objetí vzít zpět, můžeš ho <a href="<?php echo $undoLink; ?>" onclick="return confirm('Opravdu vzít zpět objetí?\nObjetí bude nenávratně ztraceno!\nVšechny související odkazy přestanou platit.');">odvolat</a>.
</p>    
<?php } else { ?>
<h2>Sender's Page</h2>

<p>
    Copy the recipient's link
    <span class="copy-link" id="aRecipient" href="<?php echo $recipientLink; ?>"><?php echo $recipientLink; ?></span>
    <button class="copy" id="btnCopyRecipient" aria-label="Copy recipient link" title="Copy recipient link">📋</button>
    and send it to the person you'd like to hug.<br>
</p>

<p>
    The second (private) link is for you as the sender:
    <a id="aSender" href="<?php echo $senderLink; ?>"><?php echo $senderLink; ?></a>
    <button class="copy" id="btnCopySender" aria-label="Copy sender link" title="Copy sender link">📋</button>.<br>
    This is the page you're currently viewing. Save it somewhere safe so you don't lose it. Don't share it with anyone else.<br>
</p>

<h3>Hug Status</h3>

<p>
    <?php
        echo implode("<br>", $statuses);
    ?>
</p>

<?php if (!empty($name) || !empty($message)) {?>
<h3>Hug Details</h3>

<p>
    <strong>Sender:</strong> <?php echo dl_escape($name); ?><br>
    <strong>Message:</strong><br>
    <?php echo dl_escape($message); ?>
</p>
<?php } ?>

<h3>Cancel the Hug</h3>

<p>
    If you sent this hug by mistake or changed your mind, you can
    <a href="<?php echo $undoLink; ?>" onclick="return confirm('Are you sure you want to cancel this hug?\nThis action cannot be undone.\nAll related links will stop working.');">cancel it</a>.
</p>
<?php
}
?>

<script>
const recipientLink = document.getElementById("aRecipient");
const recipientButton = document.getElementById("btnCopyRecipient");
const senderLink = document.getElementById("aSender");
const senderButton = document.getElementById("btnCopySender");
const langFailedCopy = "<?php echo dl_lang_encs("Copying link failed.", "Nepodařilo se zkopírovat odkaz.")?>";

recipientButton.addEventListener("click", async () => {
    try {
        await navigator.clipboard.writeText(recipientLink.innerHTML);

        recipientButton.textContent = "✅";
        setTimeout(() => {
            recipientButton.textContent = "📋";
        }, 1500);
    } catch (err) {
        alert(langFailedCopy);
    }
});

senderButton.addEventListener("click", async () => {
    try {
        await navigator.clipboard.writeText(senderLink.href);

        senderButton.textContent = "✅";
        setTimeout(() => {
            senderButton.textContent = "📋";
        }, 1500);
    } catch (err) {
        alert(langFailedCopy);
    }
});
</script>
<?php
dl_page_end();

