<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "/config.php";
include __DIR__ . "/ver.php";
include __DIR__ . "/include/lib.php";

if (dl_admin() || !dl_production())
{
    ini_set('display_errors', 1);
}

header("X-Frame-Options: DENY");
header("Referrer-Policy: same-origin");
header("Content-Security-Policy: " . 
        "default-src 'self'; " .
        "base-uri 'self'; " .
        "object-src 'none'; " .
        "frame-ancestors 'none'; " .
        "img-src 'self'; " . 
        "media-src 'self'; " . 
        "font-src 'self' https://fonts.gstatic.com; " . 
        "style-src 'self' https://fonts.googleapis.com; " . 
        "script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; " . 
        "frame-src https://www.google.com; " .
        "form-action 'self'; " .        
        "connect-src 'self' https://www.google.com; " .
        "upgrade-insecure-requests");

$lang = dl_get("lang", "auto");
$page = dl_get("page", "main");

if ($page === "")
{
    $page = "main";
}

if (!in_array($lang, ["cs", "en", "auto"]))
{
    $lang = "auto";
}

if ($lang === "auto") 
{
    header('Vary: Accept-Language');
    
    $lang = dl_detectLanguage(['cs', 'en'], 'en');
    
    if (dl_get("shortLink") != 1)
    {
        http_response_code(302);
    
        $get = $_GET;
        unset($get["lang"]);
        unset($get["page"]);   
        $params = "";
        if (!empty($get))
        {
            $params = "?" . http_build_query($get);
        }

        header('Location: ' . $dl_server . '/' . $lang . '/' . $page . $params);
        exit;
    }                  
}

header('Content-Language: ' . $lang);


if (!dl_admin() && !$dl_production)
{
    dl_exit("This is the DEVELOPMENT ENVIRONMENT, it is not available for regular users.", "Toto je VÝVOJOVÉ PROSTŘEDÍ, není dostupné pro obyčejné uživatele.");
}

if (!preg_match('/^[a-z0-9_]+$/', $page))
{
    http_response_code(404);
    dl_exit("404 page not found", "404 stránka nenalezena");
}

if (file_exists(__DIR__ . "/pages/$page.php"))
{
    include __DIR__ . "/pages/$page.php";
} 
else
{
    http_response_code(404);
    dl_exit("404 page not found", "404 stránka nenalezena");
}    