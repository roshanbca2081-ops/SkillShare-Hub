<?php
/**
 * Role-specific preferences stored per user without requiring settings-table schema changes.
 */
function getUserPreferences(PDO $pdo, $userId)
{
    $preferences = [];
    try {
        $keyPrefix = 'user_' . (int)$userId . '_pref_';
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_group = ? AND setting_key LIKE ?");
        $stmt->execute(['user:' . (int)$userId, $keyPrefix . '%']);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $preferences[substr($row['setting_key'], strlen($keyPrefix))] = $row['setting_value'];
        }
    } catch (Throwable $e) {
        error_log('User preferences lookup failed: ' . $e->getMessage());
    }
    return $preferences;
}

function saveUserPreferences(PDO $pdo, $userId, array $preferences)
{
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)");
    $group = 'user:' . (int)$userId;
    foreach ($preferences as $key => $value) {
        $stmt->execute(['user_' . (int)$userId . '_pref_' . $key, (string)$value, $group]);
    }
}

function getUserThemePreference(PDO $pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? AND setting_group = ? LIMIT 1");
        $stmt->execute(['user_' . (int)$userId . '_theme_preference', 'user:' . (int)$userId]);
        $theme = $stmt->fetchColumn();
        return in_array($theme, ['light', 'dark', 'system'], true) ? $theme : 'system';
    } catch (Throwable $e) {
        return 'system';
    }
}

function saveUserThemePreference(PDO $pdo, $userId, $theme)
{
    if (!in_array($theme, ['light', 'dark', 'system'], true)) {
        throw new InvalidArgumentException('Unsupported theme preference.');
    }
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)");
    $stmt->execute(['user_' . (int)$userId . '_theme_preference', $theme, 'user:' . (int)$userId]);
}
