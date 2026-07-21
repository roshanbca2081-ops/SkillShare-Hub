<?php
$assetBase = file_exists('assets/js/main.js') ? 'assets/' : (file_exists('../assets/js/main.js') ? '../assets/' : '../../assets/');
?>
<footer class="footer">
  <div class="container text-center">
    <span class="site-logo" aria-hidden="true" style="margin-bottom:10px;"></span>
    <p>&copy; 2026 ShareSkill Hub. Share, Learn, Grow.</p>
  </div>
</footer>
<script src="<?php echo $assetBase; ?>js/main.js"></script>
<script src="<?php echo $assetBase; ?>js/app.js"></script>
</body>
</html>
