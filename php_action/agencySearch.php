<?php

require_once 'core.php';
require_once 'Agency.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agencyModel = new Agency();

    $keyword = strval($_POST['query'] ?? '');
    $productResult = [];

    $search_param = "%{$keyword}%";

    $agencies = $agencyModel->searchAgencyByAMS($_SESSION['user_id'], $search_param);

    $agenciesOptions = array_map(function ($item) {
        return [
            'id' => $item->id,
            'text' => $item->name . ' - ' . $item->province
        ];
    }, $agencies);

    echo json_encode($agenciesOptions);
}
