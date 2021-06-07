<?php

require_once 'core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = strval($_POST['query'] ?? '');

    $productResult = [];

    $search_param = "%{$keyword}%";
    if (empty($keyword)) {
        $sql = 'SELECT id, product_code, name, model FROM products ORDER BY created_at ASC LIMIT 100';
    } else {
        $sql = 'SELECT id, product_code, name, model FROM products
                WHERE product_code LIKE :searchParam OR name LIKE :searchParam OR model LIKE :searchParam
                ORDER BY created_at ASC';
    }

    $statement = $connect->prepare($sql);
    $statement->execute(['searchParam' => $search_param]);

    $products = $statement->fetchAll(PDO::FETCH_OBJ);

    $productOptions = array_map(function ($item) {
        return [
            'id' => $item->id,
            'text' => $item->name
        ];
    }, $products);

    echo json_encode($productOptions);
} // /if $_POST