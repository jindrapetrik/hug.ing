<?php

//Beginning part of the URL
$dl_server = "https://www.mysite.com";

//IP address of the administrator
$dl_admin_ip = "xxx";

//Salt used for hashing tokens
$dl_token_salt = "abcd";

//Lifetime of the accepting token
$dl_token_lifetime = 30 * 60; // 30 minutes


//Google reCaptcha site key
$dl_recaptcha_site_key = "";
//Google reCaptcha secret
$dl_recaptcha_secret = "";
//Expected host name for reCaptcha
$dl_recaptcha_expected_hostname = "www.mysite.com";
//Minimum score for Google reCaptcha
$dl_recaptcha_min_score = 0.5;

//Database server
$db_server = "";
//Database user
$db_user = "";
//Database password
$db_password = "";
//Database name
$db_database = "";

//Whether this is production or development environment. Set to true on production.
$dl_production = false;