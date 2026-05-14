<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_model extends CI_Model
{
    protected $table = 'appointments';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array())
    {
        $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.reason, a.notes, a.status, a.created_at')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('d.department_name')
            ->from($this->table . ' a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join('departments d', 'd.id = u.department_id', 'left');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $this->db
                ->group_start()
                ->like('a.appointment_code', $search)
                ->or_like('p.patient_code', $search)
                ->or_like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('u.name', $search)
                ->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('a.status', $filters['status']);
        }

        if (!empty($filters['appointment_date'])) {
            $this->db->where('a.appointment_date', $filters['appointment_date']);
        }

        return $this->db
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.reason, a.notes, a.status, a.booked_by, a.created_at, a.updated_at')
            ->select('p.patient_code, p.full_name AS patient_name')
            ->select('u.name AS doctor_name')
            ->from($this->table . ' a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->where('a.id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_active_patients()
    {
        return $this->db
            ->select('id, patient_code, full_name, phone')
            ->from('patients')
            ->where('status', 1)
            ->order_by('full_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_active_doctors()
    {
        return $this->db
            ->select('u.id, u.name, u.email, u.department_id, d.department_name')
            ->from('users u')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->where('u.role', 'doctor')
            ->where('u.status', 1)
            ->order_by('u.name', 'ASC')
            ->get()
            ->result_array();
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

    public function change_status($id, $status)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, array(
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function generate_appointment_code()
    {
        $year = date('Y');
        $prefix = 'APT-' . $year . '-';

        $last_appointment = $this->db
            ->select('appointment_code')
            ->from($this->table)
            ->like('appointment_code', $prefix, 'after')
            ->order_by('appointment_code', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next_number = 1;

        if (!empty($last_appointment)) {
            $last_number = (int) substr($last_appointment['appointment_code'], strlen($prefix));
            $next_number = $last_number + 1;
        }

        do {
            $appointment_code = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
            $next_number++;
        } while ($this->appointment_code_exists($appointment_code));

        return $appointment_code;
    }

    public function appointment_code_exists($appointment_code)
    {
        return $this->db
            ->from($this->table)
            ->where('appointment_code', $appointment_code)
            ->count_all_results() > 0;
    }

    public function patient_exists($patient_id)
    {
        return $this->db
            ->from('patients')
            ->where('id', (int) $patient_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function doctor_exists($doctor_id)
    {
        return $this->db
            ->from('users')
            ->where('id', (int) $doctor_id)
            ->where('role', 'doctor')
            ->where('status', 1)
            ->count_all_results() > 0;
    }
}
