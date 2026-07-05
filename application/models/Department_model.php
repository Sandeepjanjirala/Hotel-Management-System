<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model
{
    protected $table = 'departments';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db
            ->select('id, department_name, description, status, created_at, updated_at')
            ->from($this->table)
            ->order_by('id', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('id, department_name, description, status, created_at, updated_at')
            ->from($this->table)
            ->where('id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function exists_by_name($department_name, $exclude_id = NULL)
    {
        $this->db
            ->from($this->table)
            ->where('department_name', $department_name);

        if ($exclude_id !== NULL) {
            $this->db->where('id !=', (int) $exclude_id);
        }

        return $this->db->count_all_results() > 0;
    }

    public function insert(array $data)
    {
        $now = date('Y-m-d H:i:s');

        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        $data['status'] = 1;

        $inserted = $this->db->insert($this->table, $data);

        if (!$inserted) {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $department = $this->get_by_id((int) $id);

        if (empty($department)) {
            return FALSE;
        }

        $new_status = ((int) $department['status'] === 1) ? 0 : 1;

        $updated = $this->db
            ->where('id', (int) $id)
            ->update($this->table, array(
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ));

        if (!$updated) {
            return FALSE;
        }

        return $this->get_by_id((int) $id);
    }
}
