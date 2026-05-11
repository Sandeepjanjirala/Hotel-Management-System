<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient_model extends CI_Model
{
    protected $table = 'patients';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($search = '')
    {
        $this->db
            ->select('id, patient_code, full_name, gender, dob, blood_group, phone, email, address, emergency_contact, status, created_at, updated_at')
            ->from($this->table);

        if ($search !== '') {
            $this->db
                ->group_start()
                ->like('patient_code', $search)
                ->or_like('full_name', $search)
                ->or_like('phone', $search)
                ->group_end();
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('id, patient_code, full_name, gender, dob, blood_group, phone, email, address, emergency_contact, status, created_at, updated_at')
            ->from($this->table)
            ->where('id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
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
        $patient = $this->get_by_id((int) $id);

        if (empty($patient)) {
            return FALSE;
        }

        $new_status = ((int) $patient['status'] === 1) ? 0 : 1;

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

    public function generate_patient_code()
    {
        $year = date('Y');
        $prefix = 'PAT-' . $year . '-';

        $last_patient = $this->db
            ->select('patient_code')
            ->from($this->table)
            ->like('patient_code', $prefix, 'after')
            ->order_by('patient_code', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next_number = 1;

        if (!empty($last_patient)) {
            $last_number = (int) substr($last_patient['patient_code'], strlen($prefix));
            $next_number = $last_number + 1;
        }

        do {
            $patient_code = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
            $next_number++;
        } while ($this->patient_code_exists($patient_code));

        return $patient_code;
    }

    public function patient_code_exists($patient_code)
    {
        return $this->db
            ->from($this->table)
            ->where('patient_code', $patient_code)
            ->count_all_results() > 0;
    }
}
