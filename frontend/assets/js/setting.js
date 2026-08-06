/* =============================================
   SkillShare Hub - Settings Page JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Toggle switches
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            this.parentElement.classList.toggle('on', this.checked);
            // Save preference (demo)
            const key = 'ssh_' + (this.id || Math.random().toString(36).slice(2));
            localStorage.setItem(key, this.checked ? '1' : '0');
        });
        // Restore from localStorage
        const key = 'ssh_' + (cb.id || '');
        if (key !== 'ssh_' && localStorage.getItem(key) !== null) {
            cb.checked = localStorage.getItem(key) === '1';
            cb.parentElement.classList.toggle('on', cb.checked);
        }
    });

    // Theme toggle (dark/light)
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('change', function () {
            document.documentElement.setAttribute('data-theme', this.checked ? 'dark' : 'light');
            localStorage.setItem('ssh_theme', this.checked ? 'dark' : 'light');
        });
        if (localStorage.getItem('ssh_theme') === 'dark') {
            themeToggle.checked = true;
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }

    // Danger zone confirmation
    const dangerForms = document.querySelectorAll('form[data-danger]');
    dangerForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const confirmText = this.getAttribute('data-danger') || 'Are you sure you want to continue?';
            if (!window.confirm(confirmText)) {
                e.preventDefault();
            }
        });
    });

    // Account deactivation slider text
    const deactivateBtn = document.querySelector('.deactivate-account');
    if (deactivateBtn) {
        deactivateBtn.addEventListener('click', function () {
            const input = document.getElementById('deactivateConfirm');
            if (input && input.value.toLowerCase() === 'deactivate') {
                if (window.confirm('Final confirmation: deactivate your account?')) {
                    // Demo action
                }
            } else {
                window.alert('Please type "deactivate" to confirm.');
            }
        });
    }

    // Notification preference selects auto-save
    document.querySelectorAll('.settings-section select').forEach(function (select) {
        select.addEventListener('change', function () {
            const saved = document.createElement('div');
            saved.className = 'alert alert-success';
            saved.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:2000;box-shadow:var(--shadow-lg);animation:fadeInDown .4s ease;';
            saved.innerHTML = '<i class="fa-solid fa-check"></i> Preference saved successfully!';
            document.body.appendChild(saved);
            setTimeout(function () { saved.remove(); }, 2500);
        });
    });
});
