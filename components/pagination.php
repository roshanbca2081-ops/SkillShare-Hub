<?php
// Pagination Component
// Set $current_page and $total_pages before including
$current_page = isset($current_page) ? (int)$current_page : 1;
$total_pages = isset($total_pages) ? (int)$total_pages : 1;
$base_url = isset($base_url) ? $base_url : '?page=';
?>
<?php if ($total_pages > 1): ?>
<nav class="pagination-wrap reveal" aria-label="Pagination">
    <ul class="pagination">
        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url . ($current_page - 1); ?>" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $base_url . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $base_url . ($current_page + 1); ?>" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>

<style>
    .pagination-wrap { display: flex; justify-content: center; margin-top: var(--spacing-8); }
    .pagination { display: flex; gap: 0.4rem; }
    .page-item { list-style: none; }
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: var(--border-radius-md);
        background: #fff;
        color: var(--gray-700);
        font-weight: 600;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-fast);
    }
    .page-link:hover { background: var(--primary-soft); color: var(--primary); }
    .page-item.active .page-link { background: var(--gradient-primary); color: #fff; box-shadow: var(--shadow-primary); }
    .page-item.disabled .page-link { opacity: 0.4; pointer-events: none; }
</style>
<?php endif; ?>
