
<?php

require_once 'core.php';
require_once 'db_connect.php';

class Agency
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAgenciesByAMS($empId)
    {
        $this->db->query('SELECT id, name, province FROM agencies WHERE employee_id = :employee_id');

        $this->db->bind(':employee_id', $empId);

        $results = $this->db->resultSet();

        return $results;
    }

    public function searchAgencyByAMS($employeeId, $searchParam)
    {
        $this->db->query('SELECT id, name, province FROM agencies
                            WHERE (name LIKE :searchParam OR province LIKE :searchParam) and employee_id = :employee_id
                            ORDER BY created_at ASC');
        $this->db->bind(':searchParam', $searchParam);
        $this->db->bind(':employee_id', $employeeId);

        $results = $this->db->resultSet();

        return $results;
    }

    public function getAgencyOptions($employeeId)
    {
        $this->db->query('SELECT id, name FROM agencies WHERE employee_id = :employee_id');
        $this->db->bind(':employee_id', $employeeId);

        $results = $this->db->resultSet();

        return array_map(function ($value) {
            return [
                'value' => $value->id,
                'title' => $value->name,
            ];
        }, $results);
    }
}
