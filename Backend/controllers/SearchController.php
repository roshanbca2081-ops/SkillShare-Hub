<?php

/**
 * Search Controller
 */

require_once __DIR__ . '/../models/Search.php';

class SearchController
{
    private $searchModel;

    public function __construct()
    {
        $this->searchModel = new Search();
    }

    public function search()
    {
        $q = $_GET['q'] ?? ($_GET['query'] ?? '');
        if (empty(trim($q))) {
            sendError('Search query parameter q is required.', 400);
        }
        $results = $this->searchModel->globalSearch($q);
        sendSuccess($results);
    }
}
