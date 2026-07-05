<?php

    $sendTime = time();
    $sendToken = dl_create_token("", $sendTime, "send");

    $title = dl_lang_encs("Send hug", "Poslat objetí");
    dl_page_begin();    
?>
<form action="xsend" method="POST" class="hug">
    <input type="hidden" name="time" value="<?php echo $sendTime; ?>">
    <input type="hidden" name="token" value="<?php echo $sendToken; ?>">
    <fieldset>
        <legend><?php echo dl_lang_encs("Send a Virtual hug", "Poslat virtuální objetí"); ?></legend>
        <div class="field"><label for="txaMessage"><?php echo dl_lang_encs("Message:", "Vzkaz:"); ?> (<?php echo dl_lang_encs("optional", "volitelné");?>)</label><br>
            <div id="txaMessageWrapper">
            <textarea rows="5" cols="60" id="txaMessage" name="message"></textarea>
            <div id="counter" title="<?php echo dl_lang_encs("Character counter", "Počítadlo znaků"); ?>"><span id="currentChars">0</span>/<span id="maxChars">1000</span></div>
            </div>
        </div>
        <div class="field"><label for="inpName"><?php echo dl_lang_encs("Sender name:", "Jméno odesílatele:"); ?> (<?php echo dl_lang_encs("optional", "volitelné");?>)</label><br><input type="text" name="name" id="inpName" maxlength="50" size="30"/>
            <div class="comment"><?php echo dl_lang_encs("It is displayed before accepting the hug.", "Zobrazí se před přijetím obejmutí."); ?></div>
        </div>
        <div>
            <input id="btnCreateLink" type="submit" class="button" name="send" value="<?php echo dl_lang_encs("Create link*", "Vytvoř odkaz*"); ?>"/>
        </div>
        
    </fieldset>
</form>
<script>
const txaMessage = document.getElementById("txaMessage");
const currentChars = document.getElementById("currentChars");
const counter = document.getElementById("counter");

txaMessage.addEventListener("input", () => {
    let v = txaMessage.value;
    v = v.replace("\r", "");
    let len = v.length;
    currentChars.textContent = len;
    
    if (len > 1000) {
        counter.classList.add("over");
        btnCreateLink.disabled = true;
    } else {
        counter.classList.remove("over");
        btnCreateLink.disabled = false;
    }
});
</script>
<div class="hug-legend">
    <?php if ($lang === "cs") {?>
        <p>
            *Co se stane po kliknutí na <strong>Vytvoř odkaz</strong>:
        </p>
        <ul>
            <li>Vygeneruje se unikátní krátký odkaz pro příjemce (A), a druhý odkaz pro tebe jako odesílatele (B).</li>
            <li>Odkaz pro příjemce (A) mu pošleš zprávou (například na sociální sítí, emailem apod.)</li>
            <li>Po otevření odkazu se člověk může rozhodnout, zda objetí od tebe přijme, pokud ano, zobrazí se vzkaz a krátká animace.</li>
            <li>Na stejném odkazu také může kliknout, že chce objetí opětovat.</li>
            <li>Pod odkazem pro odesílatele (B) můžeš vidět stav přijetí. Lze na něm také objetí odvolat, kdybys změnil názor.<br>Tenhle odkaz je jen pro tebe, neztrať ho!</li>
        </ul>        
    <?php } else { ?>
        <p> *What happens after you click <strong>Create Link</strong>: </p> 
        <ul> 
            <li>A unique short link for the recipient (A) and a separate private link for you as the sender (B) will be generated.</li>
            <li>Send the recipient's link (A) in a message, such as via social media, email, or any messaging app.</li>
            <li>When the recipient opens the link, they can choose whether to accept your hug. If they do, your message and a short animation will be displayed.</li>
            <li>They can also choose to send a hug back using the same page.</li>
            <li>Using your private sender link (B), you can check the status of your hug. You can also cancel it if you change your mind.<br>This link is for you only—don't lose it!</li> </ul>
    <?php } ?>
</div>
<?php
dl_page_end();