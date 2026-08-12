<?php
// Generic Modal Component
// Set $modal_id, $modal_title, $modal_body (HTML) before including
$modal_id = isset($modal_id) ? $modal_id : 'genericModal';
$modal_title = isset($modal_title) ? $modal_title : 'Modal Title';
?>
<div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $modal_title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($modal_body)): ?>
                    <?php echo $modal_body; ?>
                <?php else: ?>
                    <p>Modal content goes here.</p>
                <?php endif; ?>
            </div>
            <?php if (isset($modal_footer)): ?>
                <div class="modal-footer">
                    <?php echo $modal_footer; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .modal-content { border: none; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--gray-200); padding: var(--spacing-5); }
    .modal-header .modal-title { font-weight: 700; color: var(--gray-900); }
    .modal-body { padding: var(--spacing-5); }
    .modal-footer { border-top: 1px solid var(--gray-200); padding: var(--spacing-4) var(--spacing-5); }
</style>
