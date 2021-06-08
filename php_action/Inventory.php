<?php

require_once 'core.php';
require_once 'db_connect.php';

class Inventory
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function getInventories($year)
    {
        $db = $this->db;

        $db->query("SELECT DISTINCT t1.product_id
                            FROM inventories as t1
                            WHERE DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31'
                            ");

        $results = $this->db->resultSet();

        $newResults = array_map(function ($item) use ($db, $year) {
            $db->query("SELECT p.product_code, p. name, p.model, p.business_unit_id, p.industry_id, p.product_type_id,
                                 t1.product_id, t1.month, t1.year, t1.number_of_imported_goods, t1.number_of_remaining_goods, t1.number_of_sale_goods
                            FROM inventories as t1
                            JOIN products as p
                            ON p.id = t1.product_id
                            WHERE t1.product_id = {$item->product_id} and DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31'");
            return $db->resultSet();
        }, $results);

        return $newResults;
    }

    public function findInventory($productId, $month, $year)
    {
        $this->db->query('SELECT * FROM inventories WHERE product_id = :product_id and month = :month and year = :year');

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);

        $row = $this->db->single();

        return $row;
    }

    public function updateInventory($inventoryId, $data)
    {
        $db = $this->db;

        $query = "UPDATE inventories SET `number_of_imported_goods` = :number_of_imported_goods WHERE `id` = :inventory_id";

        $db->query($query);
        $db->bind(':inventory_id', (int) $inventoryId);
        $db->bind(':number_of_imported_goods', data_get($data, 'number_of_imported_goods'));

        return $db->execute();
    }

    public function createInventory($data)
    {
        $db = $this->db;

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

    public function syncRemainingGoods($productId, $month, $year, $numberOfSales, $numberOfPurchases, $numberOfPreviousInventory)
    {
        $db = $this->db;

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
}
