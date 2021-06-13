<?php

require_once 'core.php';
require_once 'db_connect.php';

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function searchProducts($searchParam)
    {
        $this->db->query('SELECT id, product_code, name, model FROM products
                            WHERE product_code LIKE :searchParam OR name LIKE :searchParam OR model LIKE :searchParam
                            ORDER BY created_at ASC');
        $this->db->bind(':searchParam', $searchParam);

        $results = $this->db->resultSet();

        return $results;
    }
}
