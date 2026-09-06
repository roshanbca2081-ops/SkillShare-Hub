// ============================================
// Frontend Validation Library
// ============================================

class FormValidator {
    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.options = {
            validateOnChange: true,
            validateOnBlur: true,
            showErrors: true,
            ...options
        };
        this.rules = {};
        this.messages = {};
        this.errors = {};
        
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        // Add validation rules
        this.addRules();
        
        // Add event listeners
        if (this.options.validateOnChange) {
            this.form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', () => this.validateField(input));
            });
        }
        
        if (this.options.validateOnBlur) {
            this.form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
            });
        }
        
        // Form submit validation
        this.form.addEventListener('submit', (e) => {
            if (!this.validateAll()) {
                e.preventDefault();
            }
        });
    }
    
    addRules() {
        // Override this in child class or use setRules()
    }
    
    setRules(rules, messages = {}) {
        this.rules = rules;
        this.messages = messages;
    }
    
    validateField(input) {
        const name = input.name;
        const rules = this.rules[name] || [];
        const value = input.value;
        let isValid = true;
        
        // Clear previous errors
        this.clearError(input);
        
        for (const rule of rules) {
            const result = this.applyRule(rule, value, input);
            if (!result.valid) {
                this.showError(input, result.message);
                isValid = false;
                break;
            }
        }
        
        return isValid;
    }
    
    validateAll() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    applyRule(rule, value, input) {
        const [ruleName, params] = this.parseRule(rule);
        
        switch (ruleName) {
            case 'required':
                return this.ruleRequired(value, input);
            case 'email':
                return this.ruleEmail(value);
            case 'min':
                return this.ruleMin(value, params);
            case 'max':
                return this.ruleMax(value, params);
            case 'minLength':
                return this.ruleMinLength(value, params);
            case 'maxLength':
                return this.ruleMaxLength(value, params);
            case 'numeric':
                return this.ruleNumeric(value);
            case 'integer':
                return this.ruleInteger(value);
            case 'phone':
                return this.rulePhone(value);
            case 'url':
                return this.ruleUrl(value);
            case 'date':
                return this.ruleDate(value);
            case 'futureDate':
                return this.ruleFutureDate(value);
            case 'pastDate':
                return this.rulePastDate(value);
            case 'password':
                return this.rulePassword(value);
            case 'confirmed':
                return this.ruleConfirmed(value, input);
            case 'in':
                return this.ruleIn(value, params);
            default:
                return { valid: true, message: '' };
        }
    }
    
    parseRule(rule) {
        if (typeof rule === 'string') {
            const parts = rule.split(':');
            return [parts[0], parts.slice(1)];
        }
        return ['', []];
    }
    
    getMessage(ruleName, field) {
        const fieldName = field.dataset.label || field.name;
        const messages = {
            required: `${fieldName} is required.`,
            email: `Please enter a valid email address.`,
            min: `${fieldName} must be at least ${field.dataset.min || 0}.`,
            max: `${fieldName} must not exceed ${field.dataset.max || 99999}.`,
            minLength: `${fieldName} must be at least ${field.dataset.minLength || 0} characters.`,
            maxLength: `${fieldName} must not exceed ${field.dataset.maxLength || 255} characters.`,
            numeric: `${fieldName} must be a number.`,
            integer: `${fieldName} must be an integer.`,
            phone: `Please enter a valid phone number.`,
            url: `Please enter a valid URL.`,
            date: `Please enter a valid date.`,
            futureDate: `The date must be in the future.`,
            pastDate: `The date must be in the past.`,
            password: `Password must be at least 8 characters with uppercase, lowercase, number, and special character.`,
            confirmed: `Passwords do not match.`,
            in: `Invalid selection.`
        };
        return this.messages[ruleName] || messages[ruleName] || `Invalid ${fieldName}.`;
    }
    
    // Rule implementations
    ruleRequired(value, input) {
        const valid = value !== null && value !== undefined && value.toString().trim() !== '';
        return { valid, message: this.getMessage('required', input) };
    }
    
    ruleEmail(value) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const valid = regex.test(value);
        return { valid, message: 'Please enter a valid email address.' };
    }
    
    ruleMin(value, params) {
        const min = parseFloat(params[0] || 0);
        const valid = parseFloat(value) >= min;
        return { valid, message: `Must be at least ${min}.` };
    }
    
    ruleMax(value, params) {
        const max = parseFloat(params[0] || 99999);
        const valid = parseFloat(value) <= max;
        return { valid, message: `Must not exceed ${max}.` };
    }
    
    ruleMinLength(value, params) {
        const min = parseInt(params[0] || 0);
        const valid = value.length >= min;
        return { valid, message: `Must be at least ${min} characters.` };
    }
    
    ruleMaxLength(value, params) {
        const max = parseInt(params[0] || 255);
        const valid = value.length <= max;
        return { valid, message: `Must not exceed ${max} characters.` };
    }
    
    ruleNumeric(value) {
        const valid = !isNaN(value) && !isNaN(parseFloat(value));
        return { valid, message: 'Must be a number.' };
    }
    
    ruleInteger(value) {
        const valid = Number.isInteger(parseFloat(value));
        return { valid, message: 'Must be an integer.' };
    }
    
    rulePhone(value) {
        const regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
        const valid = regex.test(value);
        return { valid, message: 'Please enter a valid phone number.' };
    }
    
    ruleUrl(value) {
        try {
            new URL(value);
            return { valid: true, message: '' };
        } catch {
            return { valid: false, message: 'Please enter a valid URL.' };
        }
    }
    
    ruleDate(value) {
        const date = new Date(value);
        const valid = !isNaN(date.getTime());
        return { valid, message: 'Please enter a valid date.' };
    }
    
    ruleFutureDate(value) {
        const date = new Date(value);
        const valid = !isNaN(date.getTime()) && date.getTime() > Date.now();
        return { valid, message: 'Date must be in the future.' };
    }
    
    rulePastDate(value) {
        const date = new Date(value);
        const valid = !isNaN(date.getTime()) && date.getTime() < Date.now();
        return { valid, message: 'Date must be in the past.' };
    }
    
    rulePassword(value) {
        const valid = value.length >= 8 &&
                      /[A-Z]/.test(value) &&
                      /[a-z]/.test(value) &&
                      /[0-9]/.test(value) &&
                      /[^A-Za-z0-9]/.test(value);
        return { valid, message: this.getMessage('password', {}) };
    }
    
    ruleConfirmed(value, input) {
        const fieldName = input.dataset.confirm || input.name.replace('_confirmation', '');
        const confirmInput = this.form.querySelector(`[name="${fieldName}"]`);
        const valid = confirmInput && value === confirmInput.value;
        return { valid, message: 'Passwords do not match.' };
    }
    
    ruleIn(value, params) {
        const allowed = params.length > 0 ? params[0].split(',') : [];
        const valid = allowed.includes(value);
        return { valid, message: 'Invalid selection.' };
    }
    
    // Error handling
    showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        } else {
            const div = document.createElement('div');
            div.className = 'invalid-feedback';
            div.textContent = message;
            input.parentNode.insertBefore(div, input.nextSibling);
        }
    }
    
    clearError(input) {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
        }
    }
}

// ============================================
// Email Validation
// ============================================

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ============================================
// Phone Validation
// ============================================

function isValidPhone(phone) {
    return /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(phone);
}

// ============================================
// Password Strength Checker
// ============================================

function checkPasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    return score;
}

function getPasswordStrengthLabel(score) {
    const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    return labels[Math.min(score, 4)] || 'Very Weak';
}

function getPasswordStrengthColor(score) {
    const colors = ['#dc3545', '#dc3545', '#ffc107', '#17a2b8', '#28a745'];
    return colors[Math.min(score, 4)] || '#dc3545';
}

// ============================================
// URL Validation
// ============================================

function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

// ============================================
// Date Validation
// ============================================

function isValidDate(dateStr) {
    const date = new Date(dateStr);
    return !isNaN(date.getTime());
}

function isFutureDate(dateStr) {
    const date = new Date(dateStr);
    return !isNaN(date.getTime()) && date.getTime() > Date.now();
}

function isPastDate(dateStr) {
    const date = new Date(dateStr);
    return !isNaN(date.getTime()) && date.getTime() < Date.now();
}

// ============================================
// File Validation
// ============================================

function isValidFileType(file, allowedTypes) {
    const ext = file.name.split('.').pop().toLowerCase();
    return allowedTypes.includes(ext);
}

function isValidFileSize(file, maxSize) {
    return file.size <= maxSize;
}

function getFileSizeString(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// ============================================
// Sanitization
// ============================================

function sanitizeInput(value) {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}