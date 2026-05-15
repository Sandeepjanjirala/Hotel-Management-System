<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescription_model extends CI_Model
{
    protected $prescriptions_table = 'prescriptions';
    protected $items_table = 'prescription_items';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array(), $role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('pr.id, pr.appointment_id, pr.patient_id, pr.doctor_id, pr.symptoms, pr.diagnosis, pr.prescription_date, pr.created_at')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->select('COUNT(pi.id) AS medicine_count')
            ->from($this->prescriptions_table . ' pr')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pr.patient_id', 'inner')
            ->join('users u', 'u.id = pr.doctor_id', 'inner')
            ->join($this->items_table . ' pi', 'pi.prescription_id = pr.id', 'left');

        if ($role === 'doctor') {
            $this->db->where('pr.doctor_id', (int) $user_id);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $this->db
                ->group_start()
                ->like('a.appointment_code', $search)
                ->or_like('p.patient_code', $search)
                ->or_like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('u.name', $search)
                ->or_like('pr.diagnosis', $search)
                ->group_end();
        }

        if (!empty($filters['prescription_date'])) {
            $this->db->where('pr.prescription_date', $filters['prescription_date']);
        }

        return $this->db
            ->group_by('pr.id')
            ->order_by('pr.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_eligible_appointments($role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join($this->prescriptions_table . ' pr', 'pr.appointment_id = a.id', 'left')
            ->where('a.status !=', 'cancelled')
            ->where('pr.id IS NULL', NULL, FALSE);

        if ($role === 'doctor') {
            $this->db->where('a.doctor_id', (int) $user_id);
        }

        return $this->db
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_appointment_details($appointment_id, $role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.reason, a.status')
            ->select('p.patient_code, p.full_name AS patient_name, p.gender, p.phone AS patient_phone, p.blood_group')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('d.department_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->where('a.id', (int) $appointment_id)
            ->where('a.status !=', 'cancelled')
            ->limit(1);

        if ($role === 'doctor') {
            $this->db->where('a.doctor_id', (int) $user_id);
        }

        return $this->db->get()->row_array();
    }

    public function get_active_medicines()
    {
        return $this->db
            ->select('id, medicine_name, generic_name, unit, stock_quantity, unit_price')
            ->from('medicines')
            ->where('status', 1)
            ->order_by('medicine_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function medicine_exists($medicine_id)
    {
        return $this->db
            ->from('medicines')
            ->where('id', (int) $medicine_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function appointment_has_prescription($appointment_id)
    {
        return $this->db
            ->from($this->prescriptions_table)
            ->where('appointment_id', (int) $appointment_id)
            ->count_all_results() > 0;
    }

    public function create_prescription(array $prescription_data, array $items)
    {
        $now = date('Y-m-d H:i:s');

        $prescription_data['created_at'] = $now;
        $prescription_data['updated_at'] = $now;

        $this->db->trans_start();

        $this->db->insert($this->prescriptions_table, $prescription_data);
        $prescription_id = $this->db->insert_id();

        foreach ($items as $item) {
            $item['prescription_id'] = $prescription_id;
            $item['created_at'] = $now;
            $this->db->insert($this->items_table, $item);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $prescription_id;
    }
}
