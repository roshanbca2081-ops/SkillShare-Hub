<?php
// ============================================
// Complete Validation System
// ============================================

class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    // Required field validation
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->addError($field, $message ?? "The $field field is required.");
        }
        return $this;
    }
    
    // Email validation
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->addError($field, $message ?? "Please enter a valid email address.");
            }
        }
        return $this;
    }
    
    // Min length validation
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (strlen($this->data[$field]) < $min) {
                $this->addError($field, $message ?? "The $field must be at least $min characters.");
            }
        }
        return $this;
    }
    
    // Max length validation
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (strlen($this->data[$field]) > $max) {
                $this->addError($field, $message ?? "The $field must not exceed $max characters.");
            }
        }
        return $this;
    }
    
    // Numeric validation
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->addError($field, $message ?? "The $field must be a number.");
            }
        }
        return $this;
    }
    
    // Integer validation
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->addError($field, $message ?? "The $field must be an integer.");
            }
        }
        return $this;
    }
    
    // Float validation
    public function float($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_FLOAT)) {
                $this->addError($field, $message ?? "The $field must be a decimal number.");
            }
        }
        return $this;
    }
    
    // URL validation
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->addError($field, $message ?? "Please enter a valid URL.");
            }
        }
        return $this;
    }
    
    // Phone validation
    public function phone($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $this->data[$field])) {
                $this->addError($field, $message ?? "Please enter a valid phone number.");
            }
        }
        return $this;
    }
    
    // Date validation
    public function date($field, $format = 'Y-m-d', $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $d = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->addError($field, $message ?? "Please enter a valid date.");
            }
        }
        return $this;
    }
    
    // Future date validation
    public function futureDate($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = strtotime($this->data[$field]);
            if ($date <= time()) {
                $this->addError($field, $message ?? "The date must be in the future.");
            }
        }
        return $this;
    }
    
    // Past date validation
    public function pastDate($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = strtotime($this->data[$field]);
            if ($date >= time()) {
                $this->addError($field, $message ?? "The date must be in the past.");
            }
        }
        return $this;
    }
    
    // Confirmation validation (password confirmation)
    public function confirmed($field, $confirmationField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$confirmationField])) {
            if ($this->data[$field] !== $this->data[$confirmationField]) {
                $this->addError($field, $message ?? "The $field confirmation does not match.");
            }
        }
        return $this;
    }
    
    // Unique validation (check in database)
    public function unique($field, $table, $column, $message = null, $excludeId = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            global $pdo;
            $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
            $params = [$this->data[$field]];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $this->addError($field, $message ?? "The $field has already been taken.");
            }
        }
        return $this;
    }
    
    // Exists validation (check if exists in database)
    public function exists($field, $table, $column, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            global $pdo;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
            $stmt->execute([$this->data[$field]]);
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $this->addError($field, $message ?? "The selected $field does not exist.");
            }
        }
        return $this;
    }
    
    // Range validation (for numbers)
    public function range($field, $min, $max, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $value = $this->data[$field];
            if ($value < $min || $value > $max) {
                $this->addError($field, $message ?? "The $field must be between $min and $max.");
            }
        }
        return $this;
    }
    
    // In array validation
    public function inArray($field, $allowed, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!in_array($this->data[$field], $allowed)) {
                $this->addError($field, $message ?? "The selected $field is invalid.");
            }
        }
        return $this;
    }
    
    // File validation
    public function file($field, $types = [], $maxSize = 5242880, $message = null) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES[$field];
            
            // Check file type
            if (!empty($types)) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $types)) {
                    $this->addError($field, $message ?? "File type not allowed. Allowed types: " . implode(', ', $types));
                    return $this;
                }
            }
            
            // Check file size
            if ($file['size'] > $maxSize) {
                $maxSizeMB = $maxSize / 1048576;
                $this->addError($field, $message ?? "File size must be less than {$maxSizeMB}MB.");
                return $this;
            }
            
            // Check upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $this->addError($field, $message ?? "File upload failed. Error code: " . $file['error']);
            }
        }
        return $this;
    }
    
    // Password strength validation
    public function password($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $password = $this->data[$field];
            $errors = [];
            
            if (strlen($password) < 8) {
                $errors[] = 'at least 8 characters';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'at least one uppercase letter';
            }
            if (!preg_match('/[a-z]/', $password)) {
                $errors[] = 'at least one lowercase letter';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'at least one number';
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                $errors[] = 'at least one special character';
            }
            
            if (!empty($errors)) {
                $this->addError($field, $message ?? "Password must contain: " . implode(', ', $errors));
            }
        }
        return $this;
    }
    
    // Custom validation
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->addError($field, $message ?? "The $field is invalid.");
            }
        }
        return $this;
    }
    
    // Add custom error
    public function addError($field, $message) {
        $this->errors[$field][] = $message;
    }
    
    // Check if validation passes
    public function passes() {
        return empty($this->errors);
    }
    
    // Check if validation fails
    public function fails() {
        return !$this->passes();
    }
    
    // Get all errors
    public function errors() {
        return $this->errors;
    }
    
    // Get first error for a field
    public function firstError($field) {
        return isset($this->errors[$field]) ? $this->errors[$field][0] : null;
    }
    
    // Get all errors as string
    public function errorsString($separator = '<br>') {
        $messages = [];
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }
        return implode($separator, $messages);
    }
    
    // Get validated data
    public function validated() {
        return $this->data;
    }
}