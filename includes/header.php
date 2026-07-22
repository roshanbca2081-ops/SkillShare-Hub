<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShareSkill Hub - Learn, Share & Grow Together</title>
  <?php
    if (!isset($assetBase)) {
        $assetBase = file_exists('assets/css/style.css') ? 'assets/' : (file_exists('../assets/css/style.css') ? '../assets/' : '../../assets/');
    }
  ?>
  <link rel="icon" type="image/png" href="<?php echo $assetBase; ?>img/shareskill-favicon.png" />
  <link rel="apple-touch-icon" href="<?php echo $assetBase; ?>img/shareskill-favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-7O+o0fV4Y5QKK0eMF4wX6sNHEX3GibJFeHzk3Q5fHfnwVckPgzQFluXw5KZkVqW3cEYBie2E4LA9wMvP9x+fRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/app.css" />
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/premium-ui.css" />
</head>
<body>
<div class="site-loader" data-site-loader aria-hidden="true">
  <span class="site-logo site-logo--auth"></span>
  <span style="position:absolute;bottom:20%;color:var(--primary);font-weight:800;font-size:.9rem;animation:pulse 1.5s ease-in-out infinite;">Loading...</span>
</div>
<div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
