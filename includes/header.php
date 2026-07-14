<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShareSkill Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <?php
    $assetBase = file_exists('assets/css/style.css') ? 'assets/' : (file_exists('../assets/css/style.css') ? '../assets/' : '../../assets/');
  ?>
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/style.css" />
</head>
<body>
