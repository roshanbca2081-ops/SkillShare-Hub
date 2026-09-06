// ============================================
// Modal JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Global modal functions
    window.modal = {
        show: showModal,
        hide: hideModal,
        setTitle: setModalTitle,
        setBody: setModalBody,
        setFooter: setModalFooter,
        setAction: setModalAction
    };
});

// ============================================
// Show Modal
// ============================================

function showModal(options = {}) {
    const modal = document.getElementById('globalModal');
    const modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
    
    if (options.title) setModalTitle(options.title);
    if (options.body) setModalBody(options.body);
    if (options.footer) setModalFooter(options.footer);
    if (options.action) setModalAction(options.action);
    
    modalInstance.show();
}

// ============================================
// Hide Modal
// ============================================

function hideModal() {
    const modal = document.getElementById('globalModal');
    const modalInstance = bootstrap.Modal.getInstance(modal);
    if (modalInstance) {
        modalInstance.hide();
    }
}

// ============================================
// Set Modal Title
// ============================================

function setModalTitle(title) {
    const titleEl = document.getElementById('modalTitle');
    if (titleEl) {
        titleEl.textContent = title;
    }
}

// ============================================
// Set Modal Body
// ============================================

function setModalBody(html) {
    const bodyEl = document.getElementById('modalBody');
    if (bodyEl) {
        bodyEl.innerHTML = html;
    }
}

// ============================================
// Set Modal Footer
// ============================================

function setModalFooter(html) {
    const footerEl = document.getElementById('modalFooter');
    if (footerEl) {
        footerEl.innerHTML = html;
    }
}

// ============================================
// Set Modal Action
// ============================================

function setModalAction(options = {}) {
    const actionBtn = document.getElementById('modalAction');
    if (!actionBtn) return;
    
    if (options.text) {
        actionBtn.textContent = options.text;
    }
    
    if (options.class) {
        actionBtn.className = options.class;
    }
    
    if (options.callback) {
        // Remove old listeners
        const newBtn = actionBtn.cloneNode(true);
        actionBtn.parentNode.replaceChild(newBtn, actionBtn);
        
        newBtn.addEventListener('click', function(e) {
            options.callback(e);
        });
    }
}

// ============================================
// Confirmation Modal
// ============================================

function confirmAction(title, message, onConfirm) {
    showModal({
        title: title || 'Confirm Action',
        body: message || 'Are you sure you want to proceed?',
        footer: `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
        `,
        action: {
            text: 'Confirm',
            class: 'btn btn-danger',
            callback: function() {
                if (onConfirm) onConfirm();
                hideModal();
            }
        }
    });
    
    // Fix for the action button
    document.getElementById('confirmBtn').addEventListener('click', function() {
        if (onConfirm) onConfirm();
        hideModal();
    });
}

// ============================================
// Form Modal
// ============================================

function showFormModal(title, formHtml, onSubmit) {
    showModal({
        title: title,
        body: formHtml,
        footer: `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitFormBtn">Submit</button>
        `,
        action: {
            text: 'Submit',
            class: 'btn btn-primary',
            callback: function() {
                const form = document.getElementById('modalForm');
                if (form && onSubmit) {
                    onSubmit(new FormData(form));
                }
            }
        }
    });
    
    document.getElementById('submitFormBtn').addEventListener('click', function() {
        const form = document.getElementById('modalForm');
        if (form && onSubmit) {
            onSubmit(new FormData(form));
        }
    });
}