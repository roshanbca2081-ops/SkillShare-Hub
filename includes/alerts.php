<?php if (isset($_SESSION['alert'])): ?>
    <div class="container py-3">
        <div class="alert alert-<?php echo htmlspecialchars((string)($_SESSION['alert']['type'] ?? 'info'), ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo htmlspecialchars((string)($_SESSION['alert']['icon'] ?? 'info-circle'), ENT_QUOTES, 'UTF-8'); ?>"></i>
            <?php echo htmlspecialchars((string)($_SESSION['alert']['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
