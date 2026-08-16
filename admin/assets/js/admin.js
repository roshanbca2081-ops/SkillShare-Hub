/**
 * Admin Panel JavaScript
 */
(function() {
    'use strict';

    // API helper
    function adminApiFetch(url, options) {
        options = options || {};
        options.headers = Object.assign({'Content-Type': 'application/json'}, options.headers || {});
        if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
            options.body = JSON.stringify(options.body);
        }
        return fetch(url, options).then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        }).catch(function(err) {
            AdminPanel.showToast('Error', err.message || 'Connection error', 'error', 5000);
            return {success: false, message: err.message};
        });
    }

    // Form submission helper
    function adminSubmitForm(formId, url, successMsg) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(form);
            var data = {};
            formData.forEach(function(value, key) { data[key] = value; });
            adminApiFetch(url, {method: 'POST', body: data}).then(function(res) {
                if (res.success) {
                    AdminPanel.showToast('Success', successMsg || 'Saved successfully', 'success');
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    } else {
                        location.reload();
                    }
                } else {
                    AdminPanel.showToast('Error', res.message || 'Failed', 'error');
                }
            });
        });
    }

    // Search helper
    function adminSetupSearch(inputId, callback) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var timeout;
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                callback(input.value);
            }, 300);
        });
    }

    // Confirm delete helper
    function adminConfirmDelete(id, deleteUrl, msg) {
        if (AdminPanel.confirm(msg || 'Are you sure you want to delete this item?')) {
            adminApiFetch(deleteUrl + '?id=' + id, {method: 'POST'}).then(function(res) {
                if (res.success) {
                    AdminPanel.showToast('Deleted', 'Item deleted successfully', 'success');
                    location.reload();
                } else {
                    AdminPanel.showToast('Error', res.message || 'Failed', 'error');
                }
            });
        }
    }

    // Status toggle helper
    function adminToggleStatus(id, statusUrl) {
        var status = prompt('Enter new status (active, inactive, pending):');
        if (status && ['active', 'inactive', 'pending'].includes(status)) {
            adminApiFetch(statusUrl + '?id=' + id, {
                method: 'POST',
                body: {status: status}
            }).then(function(res) {
                if (res.success) {
                    AdminPanel.showToast('Success', 'Status updated', 'success');
                    location.reload();
                } else {
                    AdminPanel.showToast('Error', res.message || 'Failed', 'error');
                }
            });
        }
    }

    // Modal helper
    function adminOpenModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    }

    function adminCloseModal(modalId) {
        var modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('admin-modal-overlay')) {
            e.target.classList.remove('active');
        }
    });

    // Expose utilities
    window.AdminUtils = {
        apiFetch: adminApiFetch,
        submitForm: adminSubmitForm,
        setupSearch: adminSetupSearch,
        confirmDelete: adminConfirmDelete,
        toggleStatus: adminToggleStatus,
        openModal: adminOpenModal,
        closeModal: adminCloseModal
    };

})();
