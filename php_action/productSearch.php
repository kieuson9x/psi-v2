<?php

require_once 'core.php';
require_once 'Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = strval($_POST['query'] ?? '');
    $productResult = [];

    $search_param = "%{$keyword}%";

    $productModel = new Product();
    $products = $productModel->searchProducts($search_param);

    $productOptions = array_map(function ($item) {
        return [
            'id' => $item->id,
            'text' => $item->name
        ];
    }, $products);

    echo json_encode($productOptions);
}
