<?php if (isset($_SESSION['alert'])): ?>
    <div class="container py-3">
        <div class="alert alert-<?php echo $_SESSION['alert']['type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $_SESSION['alert']['icon'] ?? 'info-circle'; ?>"></i>
            <?php echo $_SESSION['alert']['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
