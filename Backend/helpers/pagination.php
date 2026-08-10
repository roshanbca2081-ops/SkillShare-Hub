<?php

/**
 * Pagination Helper
 */

if (!function_exists('paginate')) {
    function paginate($totalItems, $currentPage = 1, $perPage = 10)
    {
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'total_items'  => (int)$totalItems,
            'per_page'     => (int)$perPage,
            'current_page' => (int)$currentPage,
            'total_pages'  => $totalPages,
            'offset'       => $offset,
            'has_prev'     => $currentPage > 1,
            'has_next'     => $currentPage < $totalPages
        ];
    }
}
