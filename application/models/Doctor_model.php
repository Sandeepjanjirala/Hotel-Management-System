<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_model extends CI_Model
{
    protected $table = 'doctors';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db
            ->select('d.id, d.user_id, d.department_id, d.specialization, d.qualification, d.consultation_fee, d.available_days, d.status, d.created_at, d.updated_at')
            ->select('u.name AS doctor_name, u.email AS doctor_email, u.phone AS doctor_phone')
            ->select('dept.department_name')
            ->from($this->table . ' d')
            ->join('users u', 'u.id = d.user_id', 'inner')
            ->join('departments dept', 'dept.id = d.department_id', 'inner')
            ->order_by('d.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('d.id, d.user_id, d.department_id, d.specialization, d.qualification, d.consultation_fee, d.available_days, d.status, d.created_at, d.updated_at')
            ->select('u.name AS doctor_name, u.email AS doctor_email, u.phone AS doctor_phone')
            ->select('dept.department_name')
            ->from($this->table . ' d')
            ->join('users u', 'u.id = d.user_id', 'inner')
            ->join('departments dept', 'dept.id = d.department_id', 'inner')
            ->where('d.id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_unprofiled_doctor_users()
    {
        $profiled_rows = $this->db
            ->select('user_id')
            ->from($this->table)
            ->get()
            ->result_array();

        $profiled_user_ids = array();

        foreach ($profiled_rows as $row) {
            $profiled_user_ids[] = (int) $row['user_id'];
        }

        $this->db
            ->select('id, name, email, department_id')
            ->from('users')
            ->where('role', 'doctor')
            ->where('status', 1)
            ->order_by('name', 'ASC');

        if (!empty($profiled_user_ids)) {
            $this->db->where_not_in('id', $profiled_user_ids);
        }

        return $this->db->get()->result_array();
    }

    public function get_active_departments()
    {
        return $this->db
            ->select('id, department_name')
            ->from('departments')
            ->where('status', 1)
            ->order_by('department_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function user_is_available_doctor($user_id)
    {
        $is_doctor = $this->db
            ->from('users')
            ->where('id', (int) $user_id)
            ->where('role', 'doctor')
            ->where('status', 1)
            ->count_all_results() > 0;

        if (!$is_doctor) {
            return FALSE;
        }

        return $this->db
            ->from($this->table)
            ->where('user_id', (int) $user_id)
            ->count_all_results() === 0;
    }

    public function department_exists($department_id)
    {
        return $this->db
            ->from('departments')
            ->where('id', (int) $department_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function create_profile(array $data)
    {
        $now = date('Y-m-d H:i:s');

        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $this->db->trans_start();

        $this->db->insert($this->table, $data);

        $this->db
            ->where('id', (int) $data['user_id'])
            ->update('users', array(
                'department_id' => (int) $data['department_id'],
                'updated_at' => $now
            ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    public function update_profile($id, array $data)
    {
        $doctor = $this->get_by_id((int) $id);

        if (empty($doctor)) {
            return FALSE;
        }

        $now = date('Y-m-d H:i:s');

        $data['updated_at'] = $now;

        $this->db->trans_start();

        $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);

        $this->db
            ->where('id', (int) $doctor['user_id'])
            ->update('users', array(
                'department_id' => (int) $data['department_id'],
                'updated_at' => $now
            ));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function toggle_status($id)
    {
        $doctor = $this->get_by_id((int) $id);

        if (empty($doctor)) {
            return FALSE;
        }

        $new_status = ((int) $doctor['status'] === 1) ? 0 : 1;

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
