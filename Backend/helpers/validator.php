<?php

/**
 * Validator Helper
 */

if (!function_exists('validateEmail')) {
    function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validateRequired')) {
    function validateRequired($value)
    {
        return !empty(trim((string)$value));
    }
}

if (!function_exists('validateMinLength')) {
    function validateMinLength($value, $min = 6)
    {
        return strlen((string)$value) >= $min;
    }
}

if (!function_exists('validateRegistrationInput')) {
    function validateRegistrationInput($name, $email, $password, $confirm_password, $role = 'fresher')
    {
        $errors = [];
        if (!validateRequired($name)) {
            $errors['name'] = 'Name is required.';
        }
        if (!validateEmail($email)) {
            $errors['email'] = 'Valid email is required.';
        }
        if (!validateMinLength($password, 6)) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
        return $errors;
    }
}

if (!function_exists('validateUserInput')) {
    function validateUserInput($name, $email, $password, $confirm_password, $role, $status)
    {
        $errors = validateRegistrationInput($name, $email, $password, $confirm_password, $role);
        return $errors;
    }
}
