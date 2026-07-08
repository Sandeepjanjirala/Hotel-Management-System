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
            ->select('id, name, email')
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

        $has_profile = $this->db
            ->from($this->table)
            ->where('user_id', (int) $user_id)
            ->count_all_results() > 0;

        return !$has_profile;
    }

    public function department_exists($department_id)
    {
        return $this->db
            ->from('departments')
            ->where('id', (int) $department_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function create_profile($data)
    {
        if (!isset($data['user_id']) || !isset($data['department_id'])) {
            return FALSE;
        }

        $now = date('Y-m-d H:i:s');

        $doctor_data = array(
            'user_id' => (int) $data['user_id'],
            'department_id' => (int) $data['department_id'],
            'specialization' => isset($data['specialization']) ? $data['specialization'] : '',
            'qualification' => isset($data['qualification']) ? $data['qualification'] : '',
            'consultation_fee' => isset($data['consultation_fee']) ? $data['consultation_fee'] : 0.00,
            'available_days' => isset($data['available_days']) ? $data['available_days'] : '',
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
            'created_at' => $now,
            'updated_at' => $now
        );

        $this->db->trans_start();

        $inserted = $this->db->insert($this->table, $doctor_data);
        
        if (!$inserted) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $doctor_id = $this->db->insert_id();

        $updated = $this->db
            ->where('id', (int) $data['user_id'])
            ->update('users', array(
                'department_id' => (int) $data['department_id'],
                'updated_at' => $now
            ));

        if (!$updated) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $doctor_id;
    }

public function update_profile($id, $data)
{
    // DEBUG: Log the data being passed
    log_message('debug', 'Updating doctor ID: ' . $id);
    log_message('debug', 'Update data: ' . print_r($data, true));
    
    $doctor = $this->get_by_id((int) $id);

    if (empty($doctor)) {
        log_message('debug', 'Doctor not found for update');
        return FALSE;
    }

    $now = date('Y-m-d H:i:s');

    $doctor_data = array(
        'department_id' => (int) $data['department_id'],
        'specialization' => $data['specialization'],
        'qualification' => $data['qualification'],
        'consultation_fee' => $data['consultation_fee'],
        'available_days' => $data['available_days'],
        'status' => (int) $data['status'],
        'updated_at' => $now
    );

    $this->db->trans_start();

    $updated = $this->db
        ->where('id', (int) $id)
        ->update($this->table, $doctor_data);

    log_message('debug', 'Doctor update result: ' . ($updated ? 'TRUE' : 'FALSE'));

    if (!$updated) {
        $this->db->trans_rollback();
        return FALSE;
    }

    $updated_user = $this->db
        ->where('id', (int) $doctor['user_id'])
        ->update('users', array(
            'department_id' => (int) $data['department_id'],
            'updated_at' => $now
        ));

    log_message('debug', 'User update result: ' . ($updated_user ? 'TRUE' : 'FALSE'));

    if (!$updated_user) {
        $this->db->trans_rollback();
        return FALSE;
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
}

    // FIXED: Toggle status method
    public function toggle_status($id)
    {
        // Get the current doctor data
        $doctor = $this->get_by_id((int) $id);

        if (empty($doctor)) {
            return FALSE;
        }

        // Toggle the status
        $current_status = (int) $doctor['status'];
        $new_status = ($current_status === 1) ? 0 : 1;

        // Update the status
        $updated = $this->db
            ->where('id', (int) $id)
            ->update($this->table, array(
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ));

        if (!$updated) {
            return FALSE;
        }

        // Return the updated doctor data
        return $this->get_by_id((int) $id);
    }
}