<?php

require_once 'core.php';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'months' => $this->data_get($_POST, 'months'),
        'product_id' => (int) $this->data_get($_POST, 'product_id'),
        'year' => (int) $this->data_get($_POST, 'year'),
        'number_of_imported_goods' => $this->data_get($_POST, 'number_of_imported_goods'),
        // 'number_of_remaining_goods' => $this->data_get($_POST, 'number_of_remaining_goods'),
    ];

    $updateStatus = true;
    $createStatus = true;

    foreach ($data['months'] as $month) {
        $inventory = findInventory($data['product_id'], $month, $data['year']);
        $data['month'] = $month;

        if ($inventory) {
            $updateStatus = updateInventory($inventory->id, $data);
        } else {
            $createStatus = createInventory($data);
        }
    }

    $this->syncYear($data);

    if ($updateStatus || $createStatus) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} // /if $_POST

function syncYear($db, $data)
{
    $month = $data['month'];
    $year = $data['year'];

    for ($i = 0; $i < 12; $i++) {
        $month = $i + 1;
        $date = date("{$year}-${month}-1");

        $previousMonth = (int) date('m', strtotime('-1 months', strtotime($date)));
        $previousYear = (int) date('Y', strtotime('-1 months', strtotime($date)));
        $inventory = findInventory($db, $data['product_id'], $month, $data['year']);

        if (!$inventory) {
            $newData = $data;
            $newData['month'] = $month;
            $newData['number_of_imported_goods'] = null;

            // createInventory($db, $newData);
            $inventory = findInventory($db, $data['product_id'], $month, $data['year']);
        }

        $totalSales = $this->employeeSaleModel->getTotalSalesByProduct($data['product_id'], $month, $data['year']);
        $totalProductSales = $totalSales->total_product_sales ?? 0;

        $previousInventory = findInventory($db, $data['product_id'], $previousMonth, $previousYear);
        $currentInventory = findInventory($db, $data['product_id'], $month, $year);

        $numberOfPreviousInventory = data_get($previousInventory, 'number_of_remaining_goods', 0);
        syncRemainingGoods($db, $data['product_id'], $month, $year, $totalProductSales, $currentInventory->number_of_imported_goods, $numberOfPreviousInventory);
    }
}

function findInventory($db, $productId, $month, $year)
{
    $db->query('SELECT * FROM inventories WHERE product_id = :product_id and month = :month and year = :year');

    $db->bind(':product_id', $productId);
    $db->bind(':month', $month);
    $db->bind(':year', $year);

    $row = $db->single();

    return $row;
}

function updateInventory($db, $inventoryId, $data)
{
    $query = "UPDATE inventories SET `number_of_imported_goods` = :number_of_imported_goods WHERE `id` = :inventory_id";

    $db->query($query);
    $db->bind(':inventory_id', (int) $inventoryId);
    $db->bind(':number_of_imported_goods', data_get($data, 'number_of_imported_goods'));

    return $db->execute();
}

function createInventory($db, $data)
{
    $query = "INSERT INTO inventories (`product_id`, `month`, `year`, `number_of_imported_goods`)
                     VALUES(:product_id, :month, :year, :number_of_imported_goods)";

    $db->query($query);
    $db->bind(':product_id', data_get($data, 'product_id'));
    $db->bind(':month', data_get($data, 'month'));
    $db->bind(':year', data_get($data, 'year'));
    $db->bind(':number_of_imported_goods', data_get($data, 'number_of_imported_goods'));
    // $db->bind(':number_of_remaining_goods', data_get($data, 'number_of_remaining_goods'));

    return $db->execute();
}

function syncRemainingGoods($db, $productId, $month, $year, $numberOfSales, $numberOfPurchases, $numberOfPreviousInventory)
{
    $query = "UPDATE inventories
                        SET `number_of_remaining_goods` = :number_of_remaining_goods, `number_of_sale_goods` = :number_of_sale_goods
                        WHERE product_id = :product_id and month = :month and year = :year";

    $numberOfInventory = $numberOfPreviousInventory + $numberOfPurchases - $numberOfSales;

    $db->query($query);

    $db->bind(':number_of_sale_goods', $numberOfSales);
    $db->bind(':number_of_remaining_goods', $numberOfInventory);
    $db->bind(':product_id', $productId);
    $db->bind(':month', $month);
    $db->bind(':year', $year);

    return $db->execute();
}

function getTotalSalesByProduct($productId, $month, $year)
{
    $this->db->query('SELECT SUM(`number_of_sale_goods`) as total_product_sales FROM agency_sales WHERE product_id = :product_id and month = :month and year = :year');

    $this->db->bind(':product_id', $productId);
    $this->db->bind(':month', $month);
    $this->db->bind(':year', $year);

    $row = $this->db->single();

    return $row;
}
