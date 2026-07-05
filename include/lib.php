<?php

$page_started = false;
$page_finished = false;

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false); // pro starší IE
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

function dl_page_begin(string $meta = "")
{
    global $page_started, $page_finished, $title, $lang, $page;
    if ($page_started)
    {
        return;
    }    
    include __DIR__ . "/page_begin.php";
    $page_started = true;
}

function dl_page_end()
{
    global $page_finished, $title, $lang, $page, $dl_version, $dl_version_date;
    if ($page_finished)
    {
        return;
    }   
    include __DIR__ . "/page_end.php";
    $page_finished = true;
}

function dl_get($name, $defaultValue = "")
{
    $ret =  $_GET[$name] ?? $defaultValue;
    if (!is_string($ret))
    {
        return $defaultValue;
    }
    return $ret;
}

function dl_post($name, $defaultValue = "")
{
    $ret =  $_POST[$name] ?? $defaultValue;
    if (!is_string($ret))
    {
        return $defaultValue;
    }
    return $ret;
}

function dl_detectLanguage(array $supported, string $default = 'en'): string
{
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return $default;
    }

    $langs = [];

    foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $part) {
        if (preg_match('/^([a-z-]+)(?:;q=([0-9.]+))?$/i', trim($part), $m)) {
            $code = strtolower($m[1]);
            $q = isset($m[2]) ? (float) $m[2] : 1.0;
            $langs[$code] = $q;
        }
    }

    arsort($langs);

    foreach ($langs as $code => $q) {
        $short = substr($code, 0, 2);
        if (in_array($short, $supported, true)) {
            return $short;
        }
    }

    return $default;
}

function dl_lang_encs(?string $msg_en, ?string $msg_cs = null )
{
    global $lang;
    if ($lang === "cs" && !empty($msg_cs))
    {
        return $msg_cs;
    }
    return $msg_en;    
}

function dl_exit(string $msg = "", ?string $msg_cs = null)
{
    global $page_started, $page_finished, $lang;
    
    
    if ($page_finished)
    {
        exit;
    }
    if (!$page_started)
    {
        dl_page_begin();
    }
    echo dl_lang_encs($msg, $msg_cs);
    dl_page_end();
    exit;
}

function dl_require_admin()
{
    global $dl_admin_ip;
    if (!dl_admin())
    {
        dl_exit("Under construction - there will be something in the future, so come back soon to check.", "Tady se teprve něco tvoří. Vrať se sem v budoucnu, až to zprovozníme.");
    }
}

function dl_admin()
{
    global $dl_admin_ip;
    return $_SERVER["REMOTE_ADDR"] === $dl_admin_ip;
}

function dl_production()
{
    global $dl_production;
    return $dl_production;
}

function dl_db_connect(): \PDO
{
    global $dl_pdo,$db_server,$db_user,$db_password,$db_database;
    ini_set('mysql.connect_timeout', 300);
    ini_set('default_socket_timeout', 300);
    $ex = null;
    $ok = false;
   
    try 
    {
       $dl_pdo = new PDO("mysql:dbname=" . $db_database . ";host=" . $db_server, $db_user, $db_password);
       $dl_pdo->query("SET NAMES UTF8MB4");
       $dl_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
       $ok = true;
    } 
    catch (PDOException $ex) 
    {
        $ok = false;
    }
    if($ok && !is_object($dl_pdo))
    {
        $ok = false;
    }
    if (!$ok)
    {
        dl_exit("Database is not working right now, please come back later.");
    }
    return $dl_pdo;
}

function dl_escape(?string $txt): string
{
    return htmlspecialchars((string) $txt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}