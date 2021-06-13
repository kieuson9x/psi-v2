<?php

require_once 'core.php';
require_once 'EmployeeSale.php';
require_once 'Agency.php';

$data = [];

if ($_SESSION['user_id']) {
    $agencyModel = new Agency();

    $agencies = $agencyModel->getAgenciesByAMS($_SESSION['user_id']);

    $data = [
        'agencies' => $agencies,
    ];
}

echo json_encode($data);
