<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShareSkill Hub</title>
  <?php
    $assetBase = file_exists('assets/css/style.css') ? 'assets/' : (file_exists('../assets/css/style.css') ? '../assets/' : '../../assets/');
  ?>
  <link rel="stylesheet" href="<?php echo $assetBase; ?>css/style.css" />
</head>
<body>
