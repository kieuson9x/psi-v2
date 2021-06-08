<?php

require_once 'core.php';
require_once 'Inventory.php';
require_once 'EmployeeSale.php';

$data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inventoryModel = new Inventory();

    $data = [
        'months' => data_get($_POST, 'months'),
        'product_id' => (int) data_get($_POST, 'product_id'),
        'year' => (int) data_get($_POST, 'year'),
        'number_of_imported_goods' => data_get($_POST, 'number_of_imported_goods'),
        // 'number_of_remaining_goods' => data_get($_POST, 'number_of_remaining_goods'),
    ];

    $updateStatus = true;
    $createStatus = true;
    foreach ($data['months'] as $month) {
        $inventory = $inventoryModel->findInventory($data['product_id'], $month, $data['year']);

        $data['month'] = $month;

        if ($inventory) {
            $updateStatus = $inventoryModel->updateInventory($inventory->id, $data);
        } else {
            $createStatus = $inventoryModel->createInventory($data);
        }
    }

    syncYear($inventoryModel, $data);

    if ($updateStatus || $createStatus) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    // if ($inventoryModel->updateOrCreateInventory($data)) {
    //     echo json_encode(['success' => true]);
    // } else {
    //     die("Something went wrong, please try again!");
    // }
}

function syncYear($inventoryModel, $data)
{
    $month = $data['month'];
    $year = $data['year'];

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

        $employeeSaleModel = new EmployeeSale();

        $totalSales = $employeeSaleModel->getTotalSalesByProduct($data['product_id'], $month, $data['year']);
        $totalProductSales = $totalSales->total_product_sales ?? 0;

        $previousInventory = $inventoryModel->findInventory($data['product_id'], $previousMonth, $previousYear);
        $currentInventory = $inventoryModel->findInventory($data['product_id'], $month, $year);

        $numberOfPreviousInventory = data_get($previousInventory, 'number_of_remaining_goods', 0);
        $inventoryModel->syncRemainingGoods($data['product_id'], $month, $year, $totalProductSales, $currentInventory->number_of_imported_goods, $numberOfPreviousInventory);
    }
}