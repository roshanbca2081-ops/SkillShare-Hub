<?php
/**
 * Path Helper System for ShareSkill Hub
 * Centralized path resolution to fix all broken asset links
 */

function asset_url($path = '') {
    $basePath = get_base_path();
    return $basePath . 'assets/' . ltrim($path, '/');
}

function css_url($file) {
    return asset_url('css/' . ltrim($file, '/'));
}

function js_url($file) {
    return asset_url('js/' . ltrim($file, '/'));
}

function img_url($file) {
    return asset_url('img/' . ltrim($file, '/'));
}

function upload_url($file) {
    return asset_url('uploads/' . ltrim($file, '/'));
}

function get_base_path() {
    // Determine base path relative to current script
    static $basePath = null;
    if ($basePath !== null) return $basePath;

    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptDir === '/' || $scriptDir === '\\') {
        $basePath = '';
    } else {
        $basePath = rtrim($scriptDir, '/') . '/';
    }
    return $basePath;
}

function page_url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

function get_asset_base() {
    static $assetBase = null;
    if ($assetBase !== null) return $assetBase;

    $depth = get_depth_from_root();
    if ($depth === 0) {
        $assetBase = 'assets/';
    } else {
        $assetBase = str_repeat('../', $depth) . 'assets/';
    }
    return $assetBase;
}

function get_depth_from_root() {
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($currentDir === '/' || $currentDir === '\\') return 0;

    $parts = explode('/', trim($currentDir, '/'));
    $depth = 0;
    foreach ($parts as $part) {
        if ($part !== '' && !in_array($part, ['assets', 'includes', 'config', 'controllers', 'models', 'api', 'database', 'storage', 'modules'])) {
            $depth++;
        }
    }
    return $depth;
}

function root_relative_path() {
    $depth = get_depth_from_root();
    return $depth ? str_repeat('../', $depth) : './';
}

