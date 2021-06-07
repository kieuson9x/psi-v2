<?php

require_once 'core.php';

$data = [];
$year = (int) ($_GET['year'] ?? date('Y'));
$data['year'] = $year;

if ($_SESSION['user_id']) {
    $year = $data['year'] ?? date('Y');

    $sql = "SELECT DISTINCT t1.product_id
            FROM inventories as t1
            WHERE DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31'
            ";
    $statement = $connect->prepare($sql);
    $statement->execute();

    $results = $statement->fetchAll(PDO::FETCH_OBJ);

    $newResults = array_map(function ($item) use ($connect, $year) {
        $sql = "SELECT p.product_code, p. name, p.model, p.business_unit_id, p.industry_id, p.product_type_id,
                             t1.product_id, t1.month, t1.year, t1.number_of_imported_goods, t1.number_of_remaining_goods, t1.number_of_sale_goods
                        FROM inventories as t1
                        JOIN products as p
                        ON p.id = t1.product_id
                        WHERE t1.product_id = {$item->product_id} and DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31'";
        $statement = $connect->prepare($sql);
        $statement->execute();
        return  $statement->fetchAll(PDO::FETCH_OBJ);
    }, $results);

    $data['inventories'] = $newResults ?? [];

    echo json_encode($data);
}
