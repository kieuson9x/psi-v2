<?php

require_once 'core.php';
require_once 'db_connect.php';

class EmployeeSale
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAgencySales($agencyIds, $year)
    {
        $agencyIds = implode(",", $agencyIds);
        $db = $this->db;

        $db->query("SELECT DISTINCT t1.product_id, t1.agency_id
                            FROM agency_sales as t1
                            WHERE (DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31') AND `agency_id` IN ($agencyIds)
                            ");

        $results = $this->db->resultSet();

        $newResults = array_map(function ($item) use ($db, $year, $agencyIds) {
            $db->query("SELECT p.product_code, p.name, p.model, p.business_unit_id, p.industry_id, p.product_type_id,
                                 t1.product_id, t1.month, t1.year, t1.number_of_sale_goods, t1.agency_id
                            FROM agency_sales as t1
                            JOIN products as p
                            ON p.id = t1.product_id
                            WHERE t1.product_id = {$item->product_id} AND t1.agency_id = {$item->agency_id}
                                AND (DATE(CONCAT(`year`, '-', `month`, '-01')) BETWEEN '{$year}-01-01' AND '{$year}-12-31' )
                                AND `agency_id` IN ($agencyIds)");
            return $db->resultSet();
        }, $results);

        return $newResults;
    }

    public function findAgencySale($agencyId, $productId, $month, $year)
    {
        $this->db->query('SELECT * FROM agency_sales WHERE product_id = :product_id and month = :month and year = :year and agency_id = :agency_id');

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);
        $this->db->bind(':agency_id', $agencyId);

        $row = $this->db->single();

        return $row;
    }

    public function getTotalSalesByProduct($productId, $month, $year)
    {
        $this->db->query('SELECT SUM(`number_of_sale_goods`) as total_product_sales FROM agency_sales WHERE product_id = :product_id and month = :month and year = :year');

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':month', $month);
        $this->db->bind(':year', $year);

        $row = $this->db->single();

        return $row;
    }

    public function updateAgencySale($agencySaleId, $data)
    {
        $db = $this->db;

        $query = "UPDATE agency_sales SET `number_of_sale_goods` = :number_of_sale_goods WHERE `id` = :agency_sale_id";

        $db->query($query);
        $db->bind(':agency_sale_id', (int) $agencySaleId);
        $db->bind(':number_of_sale_goods', data_get($data, 'number_of_sale_goods'));

        return $db->execute();
    }

    public function createAgencySale($agencyId, $data)
    {
        $db = $this->db;

        $query = "INSERT INTO agency_sales (`agency_id`, `product_id`, `month`, `year`, `number_of_sale_goods`)
                     VALUES(:agency_id, :product_id, :month, :year, :number_of_sale_goods)";

        $db->query($query);
        $db->bind(':product_id', data_get($data, 'product_id'));
        $db->bind(':agency_id', $agencyId);
        $db->bind(':month', data_get($data, 'month'));
        $db->bind(':year', data_get($data, 'year'));
        $db->bind(':number_of_sale_goods', data_get($data, 'number_of_sale_goods'));
        // $db->bind(':number_of_remaining_goods', data_get($data, 'number_of_remaining_goods'));

        return $db->execute();
    }
}
