<?php

require_once 'core.php';
require_once 'Inventory.php';
require_once 'EmployeeSale.php';

$data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeSaleModel = new EmployeeSale();

    $data = [
        'months' => data_get($_POST, 'months'),
        'product_id' => (int) data_get($_POST, 'product_id'),
        'year' => (int) data_get($_POST, 'year'),
        'number_of_sale_goods' => data_get($_POST, 'number_of_sale_goods'),
        'agency_id' =>  data_get($_POST, 'agency_id')
        // 'number_of_remaining_goods' => data_get($_POST, 'number_of_remaining_goods'),
    ];

    $updateStatus = true;
    $createStatus = true;
    foreach ($data['months'] as $month) {
        $agencySale = $employeeSaleModel->findAgencySale($data['agency_id'], $data['product_id'], $month, $data['year']);

        $data['month'] = $month;

        if ($agencySale) {
            $updateStatus = $employeeSaleModel->updateAgencySale($agencySale->id, $data);
        } else {
            $createStatus = $employeeSaleModel->createAgencySale($data['agency_id'], $data);
        }
    }

    syncYear($employeeSaleModel, $data);

    if ($updateStatus || $createStatus) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
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
