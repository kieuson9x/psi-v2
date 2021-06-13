<?php

require_once 'core.php';
require_once 'Inventory.php';
require_once 'EmployeeSale.php';

$data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $employeeSaleModel = new EmployeeSale();
    $data = [
        'month' => (int) data_get($_POST, 'name'),
        'value' => (int) trim(data_get($_POST, 'value')),
        'product_id' => (int) trim(data_get($_POST, 'pk')),
        'year' => (int) trim(data_get($_POST, 'year')),
        'state' => trim(data_get($_POST, 'state')),
        'agency_id' => data_get($_POST, 'agency_id')
    ];

    if ($data['state'] === 'sale') {
        $data['number_of_sale_goods'] = $data['value'];
    }

    // if ($data['state'] === 'inventory') {
    //     $data['number_of_remaining_goods'] = $data['value'];
    // }

    $agencySale = $employeeSaleModel->findAgencySale($data['agency_id'], $data['product_id'], $data['month'], $data['year']);

    if ($agencySale) {
        $updateStatus = $employeeSaleModel->updateAgencySale($agencySale->id, $data);
    } else {
        $createStatus = $employeeSaleModel->createAgencySale($data['agency_id'], $data);
    }

    syncYear($employeeSaleModel, $data);
    echo json_encode(['success' => true]);

    // if ($this->employeeSaleModel->updateOrcreateEmployeeSale($data)) {
    //     echo json_encode(['success' => true]);
    // } else {
    //     die("Something went wrong, please try again!");
    // }
}

function syncYear($employeeSaleModel, $data)
{
    $month = $data['month'];
    $year = $data['year'];
    $inventoryModel = new Inventory();


    for ($i = 0; $i < 12; $i++) {
        $month = $i + 1;
        $date = date("{$year}-${month}-1");

        $previousMonth = (int) date('m', strtotime('-1 months', strtotime($date)));
        $previousYear = (int) date('Y', strtotime('-1 months', strtotime($date)));
        $inventory = $inventoryModel->findInventory($data['product_id'], $month, $data['year']);

        if (!$inventory) {
            $newData = $data;
            $newData['month'] = $month;
            $newData['number_of_imported_goods'] = null;

            $inventoryModel->createInventory($newData);
            $inventory = $inventoryModel->findInventory($data['product_id'], $month, $data['year']);
        }

        $totalSales = $employeeSaleModel->getTotalSalesByProduct($data['product_id'], $month, $data['year']);
        $totalProductSales = $totalSales->total_product_sales ?? 0;

        $previousInventory = $inventoryModel->findInventory($data['product_id'], $previousMonth, $previousYear);
        $currentInventory = $inventoryModel->findInventory($data['product_id'], $month, $year);

        $numberOfPreviousInventory = data_get($previousInventory, 'number_of_remaining_goods', 0);
        $inventoryModel->syncRemainingGoods($data['product_id'], $month, $year, $totalProductSales, $currentInventory->number_of_imported_goods, $numberOfPreviousInventory);
    }
}
