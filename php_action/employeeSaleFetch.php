<?php

require_once 'core.php';
require_once 'EmployeeSale.php';
require_once 'Agency.php';

$data = [];
$year = (int) ($_GET['year'] ?? date('Y'));
$data['year'] = $year;

$employeeId = $_SESSION['user_id'] ?? null;

if ($employeeId) {
    $employeeSaleModel = new EmployeeSale();
    $agencyModel = new Agency();

    if (!isset($_SESSION['agencyOptions'])) {
        $instance = new Agency();
        $options = $instance->getAgencyOptions($employeeId);
        $_SESSION['agencyOptions'] = $options;
    }

    $year = $data['year'] ?? date('Y');
    $agencies = array_column($agencyModel->getAgenciesByAMS($employeeId), 'id');

    $agencySales = $employeeSaleModel->getAgencySales($agencies, $year);

    $data['agency_sales'] = $agencySales;
    $data['agencyOptions'] = $_SESSION['agencyOptions'];
}

echo json_encode($data);
