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

function dl_create_token(string $recipientKey, int $time, ?string $tokenType = null): string
{
    global $dl_token_salt;
    
    $payload = $recipientKey . "|" . $time;
    if ($tokenType !== null)
    {
        $payload .= "|" . $tokenType;
    }
    
    return hash_hmac("sha256", $payload, $dl_token_salt);
}

function dl_token_valid(string $recipientKey, int $time, ?string $tokenType, string $token): bool
{
    return hash_equals(dl_create_token($recipientKey, $time, $tokenType), $token);
}

function dl_verify_recaptcha(?string $expectedAction = null): bool
{
    global $dl_recaptcha_secret_key;
    global $dl_recaptcha_expected_hostname;
    global $dl_recaptcha_min_score;

    if (empty($dl_recaptcha_secret_key)) {
        return !dl_production();
    }

    $token = dl_post("g-recaptcha-response");
    if ($token === "") 
    {
        return false;
    }

    $payload = http_build_query([
        "secret" => $dl_recaptcha_secret_key,
        "response" => $token,
        "remoteip" => $_SERVER["REMOTE_ADDR"] ?? "",
    ]);

    $context = stream_context_create([
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "content" => $payload,
            "timeout" => 5,
        ],
    ]);

    $response = @file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify",
        false,
        $context
    );

    if ($response === false)
    {
        error_log("reCAPTCHA verification request failed");
        return false;
    }

    $result = json_decode($response, true);
    if (!is_array($result) || empty($result["success"]))
    {
        error_log("reCAPTCHA failed: " . $response);
        return false;
    }

    if (!empty($dl_recaptcha_expected_hostname)
            && ($result["hostname"] ?? "") !== $dl_recaptcha_expected_hostname) 
    {
        return false;
    }

    // reCAPTCHA v3 only
    if ($expectedAction !== null && ($result["action"] ?? "") !== $expectedAction) 
    {
        return false;
    }

    if (isset($result["score"]) && $result["score"] < $dl_recaptcha_min_score) 
    {
        return false;
    }

    return true;
}