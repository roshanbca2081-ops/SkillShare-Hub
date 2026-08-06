/* =============================================
   SkillShare Hub - Form Validation JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Validation rules
    const validators = {
        required: function (value) {
            return value.trim().length > 0;
        },
        email: function (value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },
        phone: function (value) {
            return /^[0-9+\-\s()]{7,15}$/.test(value);
        },
        minLength: function (value, len) {
            return value.trim().length >= len;
        },
        password: function (value) {
            // At least 8 chars, one letter, one number
            return value.length >= 8 && /[a-zA-Z]/.test(value) && /\d/.test(value);
        },
        match: function (value, confirmEl) {
            return value === confirmEl.value;
        },
        url: function (value) {
            return /^(https?:\/\/)?([\w-]+\.)+[\w-]+/.test(value);
        }
    };

    // Show error message
    function showError(field, message) {
        const group = field.closest('.form-group') || field.parentElement;
        let error = group.querySelector('.error-message');
        if (!error) {
            error = document.createElement('span');
            error.className = 'error-message';
            group.appendChild(error);
        }
        error.textContent = message;
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
    }

    function clearError(field) {
        const group = field.closest('.form-group') || field.parentElement;
        const error = group.querySelector('.error-message');
        if (error) error.textContent = '';
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    }

    // Validate a single field based on data attributes
    function validateField(field) {
        const value = field.value;
        const rules = field.dataset.validate || '';
        const rulesList = rules.split('|').filter(Boolean);
        for (const rule of rulesList) {
            const [name, param] = rule.split(':');
            if (name === 'required' && !validators.required(value)) {
                showError(field, 'This field is required.');
                return false;
            }
            if (name === 'email' && value && !validators.email(value)) {
                showError(field, 'Please enter a valid email address.');
                return false;
            }
            if (name === 'phone' && value && !validators.phone(value)) {
                showError(field, 'Please enter a valid phone number.');
                return false;
            }
            if (name === 'minLength' && !validators.minLength(value, parseInt(param, 10))) {
                showError(field, 'Minimum ' + param + ' characters required.');
                return false;
            }
            if (name === 'password' && value && !validators.password(value)) {
                showError(field, 'Password must be 8+ chars with a letter and number.');
                return false;
            }
            if (name === 'match') {
                const target = document.getElementById(param);
                if (target && !validators.match(value, target)) {
                    showError(field, 'Passwords do not match.');
                    return false;
                }
            }
        }
        clearError(field);
        return true;
    }

    // Attach to forms
    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        const fields = form.querySelectorAll('[data-validate]');

        // Live validation on blur
        fields.forEach(function (field) {
            field.addEventListener('blur', function () {
                if (field.value) validateField(field);
            });
            field.addEventListener('input', function () {
                if (field.classList.contains('is-invalid')) validateField(field);
            });
        });

        // Validate on submit
        form.addEventListener('submit', function (e) {
            let valid = true;
            fields.forEach(function (field) {
                if (!validateField(field)) valid = false;
            });
            if (!valid) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    });
});
