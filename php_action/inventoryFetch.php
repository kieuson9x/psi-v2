<?php

require_once 'core.php';
require_once 'Inventory.php';

$data = [];
$year = (int) ($_GET['year'] ?? date('Y'));
$data['year'] = $year;

if ($_SESSION['user_id']) {
    $year = $data['year'] ?? date('Y');

    $inventoryModel = new Inventory;
    $inventories = $inventoryModel->getInventories($year);

    $data['inventories'] = $inventories;

    echo json_encode($data);
}
