<?php
$assetBase = file_exists('assets/js/main.js') ? 'assets/' : (file_exists('../assets/js/main.js') ? '../assets/' : '../../assets/');
?>
<footer class="footer">
  <div class="container text-center">
    <p>&copy; 2026 ShareSkill Hub. Share, Learn, Grow.</p>
  </div>
</footer>
<script src="<?php echo $assetBase; ?>js/main.js"></script>
</body>
</html>
