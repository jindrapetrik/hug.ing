<?php 

$get = $_GET;
unset($get["lang"]);
unset($get["page"]);   
unset($get["shortLink"]);
$params = "";
if (!empty($get))
{
    $params = "?" . http_build_query($get);
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php 
  
  if (!dl_production())
  {
      echo "DEV - ";
  }
  echo dl_lang_encs("Virtual hug.ing", "Virtuální objetí");
  
  if (!empty($title))
  {
      echo ' - ' . dl_escape($title);
  }
  
  ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playpen+Sans:wght@100..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/style.css?<?php echo filemtime("style.css");?>">
  <link rel="icon" href="/favicon.ico?<?php echo filemtime("favicon.ico");?>" type="image/x-icon">
  <meta name="author" content="Jindra Petřík">
  <meta name="theme-color" value="#ff5555">
  <?php echo $meta; 
  
  
  if (dl_admin())
  {
      //Testing new styles
      ?>
  <style>

  </style>    
      <?php
  }
  
  ?>
</head>
<body>

  <header>
      <div class="container">
      <a href="./">          
          <h1><img src="/logo.svg" valign="middle" alt="hug.ing logo" width="244" height="100" title="<?php echo dl_lang_encs("Virtual hug.ing", "Virtuální objetí"); ?>">
          <?php
          if (!dl_production())
          {
              echo '<span id="dev-info">DEV</span>';
          }
          ?></h1>             
      </a>
          <span class="panel">    
            <?php if ($lang === "cs") {?>
            <a class="lang-switch" href="/en/<?php if ($page !== "main") {echo $page . $params;} ?>">english</a>
            <?php } else { ?>
            <a class="lang-switch" href="/cs/<?php if ($page !== "main") {echo $page . $params;} ?>">česky</a>
            <?php } ?>
            &nbsp;
            |
            &nbsp;
            <span id="theme-toggle">dark</span>
            <script>
                const langDark = "<?php echo dl_lang_encs("dark", "temné");?>";
                const langLight = "<?php echo dl_lang_encs("light", "světlé");?>";
            </script>
          </span>
      </div>
  </header>

  <main>
      <div class="container">