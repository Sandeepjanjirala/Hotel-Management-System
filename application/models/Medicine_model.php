<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medicine_model extends CI_Model
{
    protected $table = 'medicines';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array())
    {
        $this->db
            ->select('id, medicine_name, generic_name, unit, stock_quantity, unit_price, description, status, created_at, updated_at')
            ->from($this->table);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $this->db
                ->group_start()
                ->like('medicine_name', $search)
                ->or_like('generic_name', $search)
                ->or_like('unit', $search)
                ->group_end();
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('status', (int) $filters['status']);
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('id, medicine_name, generic_name, unit, stock_quantity, unit_price, description, status, created_at, updated_at')
            ->from($this->table)
            ->where('id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function exists_by_name($medicine_name, $exclude_id = NULL)
    {
        $this->db
            ->from($this->table)
            ->where('medicine_name', $medicine_name);

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
        $medicine = $this->get_by_id((int) $id);

        if (empty($medicine)) {
            return FALSE;
        }

        $new_status = ((int) $medicine['status'] === 1) ? 0 : 1;

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
