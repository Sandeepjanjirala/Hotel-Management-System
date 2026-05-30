<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_model extends CI_Model
{
    protected $tables = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->config->load('doctor_panel', true);

        $config = $this->config->item('doctor_panel');
        $this->tables = isset($config['tables']) ? $config['tables'] : array();
    }

    public function get_doctor_by_user_id($doctor_user_id)
    {
        return $this->db
            ->select('d.*, u.name AS user_name, u.email AS user_email, dep.department_name AS department')
            ->from($this->table('doctors') . ' d')
            ->join($this->table('users') . ' u', 'u.id = d.user_id', 'left')
            ->join('departments dep', 'dep.id = d.department_id', 'left')
            ->where('d.user_id', (int) $doctor_user_id)
            ->limit(1)
            ->get()
            ->row();
    }

    public function get_dashboard_counts($doctor_user_id)
    {
        return array(
            'today_appointments' => $this->count_appointments($doctor_user_id, array('date' => date('Y-m-d'))),
            'upcoming_appointments' => $this->count_appointments($doctor_user_id, array('date_from' => date('Y-m-d'))),
            'assigned_patients' => $this->count_assigned_patients($doctor_user_id),
            'prescriptions' => $this->count_prescriptions($doctor_user_id),
            'lab_requests' => $this->count_lab_requests($doctor_user_id)
        );
    }

    public function get_appointments($doctor_user_id, $filters = array(), $limit = null, $offset = null)
    {
        $this->appointment_scope($doctor_user_id);
        $this->appointment_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result();
    }

    public function count_appointments($doctor_user_id, $filters = array())
    {
        $this->appointment_scope($doctor_user_id, false);
        $this->appointment_filters($filters);

        return (int) $this->db->count_all_results();
    }

    public function get_appointment($doctor_user_id, $appointment_id)
    {
        $this->appointment_scope($doctor_user_id);

        return $this->db
            ->where('a.id', (int) $appointment_id)
            ->limit(1)
            ->get()
            ->row();
    }

    public function get_patient_appointments($doctor_user_id, $patient_id, $limit = 50, $offset = 0)
    {
        $this->appointment_scope($doctor_user_id);

        return $this->db
            ->where('a.patient_id', (int) $patient_id)
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->result();
    }

    public function get_assigned_patients($doctor_user_id, $filters = array(), $limit = null, $offset = null)
    {
        $this->patient_scope($doctor_user_id);
        $this->patient_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->group_by('p.id')
            ->order_by('latest_appointment_at', 'DESC')
            ->get()
            ->result();
    }

    public function count_assigned_patients($doctor_user_id, $filters = array())
    {
        $this->db
            ->select('COUNT(DISTINCT p.id) AS aggregate', false)
            ->from($this->table('patients') . ' p')
            ->join($this->table('appointments') . ' a', 'a.patient_id = p.id', 'inner')
            ->where('a.doctor_id', (int) $doctor_user_id);

        $this->patient_filters($filters);

        $row = $this->db->get()->row();
        return $row ? (int) $row->aggregate : 0;
    }

    public function get_assigned_patient($doctor_user_id, $patient_id)
    {
        $this->patient_scope($doctor_user_id);

        return $this->db
            ->where('p.id', (int) $patient_id)
            ->group_by('p.id')
            ->limit(1)
            ->get()
            ->row();
    }

    public function count_prescriptions($doctor_user_id)
    {
        return (int) $this->db
            ->from($this->table('prescriptions') . ' pr')
            ->where('pr.doctor_id', (int) $doctor_user_id)
            ->count_all_results();
    }

    public function count_lab_requests($doctor_user_id)
    {
        return (int) $this->db
            ->from($this->table('lab_requests') . ' lr')
            ->where('lr.doctor_id', (int) $doctor_user_id)
            ->count_all_results();
    }

    public function create_prescription($doctor_user_id, $appointment_id, $data, $items = array())
    {
        $appointment = $this->get_appointment($doctor_user_id, $appointment_id);
        if (!$appointment) {
            return false;
        }

        $this->db->trans_start();

        $payload = array(
            'appointment_id' => (int) $appointment->appointment_id,
            'doctor_id' => (int) $doctor_user_id,
            'patient_id' => (int) $appointment->patient_id,
            'symptoms' => !empty($data['symptoms']) ? $data['symptoms'] : 'Not specified',
            'diagnosis' => isset($data['diagnosis']) ? $data['diagnosis'] : '',
            'notes' => isset($data['notes']) ? $data['notes'] : null,
            'prescription_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table('prescriptions'), $payload);
        $prescription_id = $this->db->insert_id();

        foreach ($items as $item) {
            if (empty($item['medicine_id'])) {
                continue;
            }

            $this->db->insert($this->table('prescription_items'), array(
                'prescription_id' => (int) $prescription_id,
                'medicine_id' => (int) $item['medicine_id'],
                'dosage' => isset($item['dosage']) ? $item['dosage'] : '',
                'days' => !empty($item['days']) ? (int) $item['days'] : 1,
                'morning' => !empty($item['morning']) ? 1 : 0,
                'afternoon' => !empty($item['afternoon']) ? 1 : 0,
                'night' => !empty($item['night']) ? 1 : 0,
                'instructions' => isset($item['instructions']) ? $item['instructions'] : null,
                'created_at' => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $prescription_id : false;
    }

    public function create_lab_request($doctor_user_id, $appointment_id, $data, $tests = array())
    {
        $appointment = $this->get_appointment($doctor_user_id, $appointment_id);
        if (!$appointment) {
            return false;
        }

        $this->db->trans_start();

        $payload = array(
            'appointment_id' => (int) $appointment->appointment_id,
            'doctor_id' => (int) $doctor_user_id,
            'patient_id' => (int) $appointment->patient_id,
            'request_code' => $this->generate_lab_request_code(),
            'request_date' => date('Y-m-d'),
            'status' => 'pending',
            'requested_by' => (int) $doctor_user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table('lab_requests'), $payload);
        $lab_request_id = $this->db->insert_id();

        foreach ($tests as $test) {
            $lab_test_id = $this->resolve_lab_test_id($test);
            if (!$lab_test_id) {
                continue;
            }

            $this->db->insert($this->table('lab_request_tests'), array(
                'lab_request_id' => (int) $lab_request_id,
                'lab_test_id' => (int) $lab_test_id,
                'result_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $lab_request_id : false;
    }

    protected function appointment_scope($doctor_user_id, $with_select = true)
    {
        if ($with_select) {
            $this->db->select(
                'a.id AS appointment_id, a.appointment_code, a.appointment_date, a.appointment_time, a.status, a.reason, ' .
                'a.doctor_id, a.patient_id, p.full_name AS patient_name, p.phone AS patient_mobile, ' .
                'p.gender AS patient_gender, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS patient_age, u.name AS doctor_name'
            );
        }

        $this->db
            ->from($this->table('appointments') . ' a')
            ->join($this->table('users') . ' u', 'u.id = a.doctor_id', 'inner')
            ->join($this->table('patients') . ' p', 'p.id = a.patient_id', 'left')
            ->where('a.doctor_id', (int) $doctor_user_id);
    }

    protected function appointment_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('a.status', $filters['status']);
        }

        if (!empty($filters['date'])) {
            $this->db->where('a.appointment_date', $filters['date']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('a.appointment_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('a.appointment_date <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('a.appointment_code', $search)
                ->or_like('a.reason', $search)
                ->group_end();
        }
    }

    protected function patient_scope($doctor_user_id)
    {
        $this->db
            ->select(
                'p.*, p.full_name AS name, p.phone AS mobile, TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age, ' .
                'MAX(a.appointment_date) AS latest_appointment_at, COUNT(a.id) AS appointment_count'
            )
            ->from($this->table('patients') . ' p')
            ->join($this->table('appointments') . ' a', 'a.patient_id = p.id', 'inner')
            ->where('a.doctor_id', (int) $doctor_user_id);
    }

    protected function patient_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('p.email', $search)
                ->group_end();
        }
    }

    protected function generate_lab_request_code()
    {
        $prefix = 'LAB-' . date('Y') . '-';
        $last = $this->db
            ->select('request_code')
            ->from($this->table('lab_requests'))
            ->like('request_code', $prefix, 'after')
            ->order_by('request_code', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        $next = $last ? ((int) substr($last->request_code, strlen($prefix)) + 1) : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    protected function resolve_lab_test_id($test)
    {
        if (is_array($test) && !empty($test['lab_test_id'])) {
            return (int) $test['lab_test_id'];
        }

        if (is_array($test) && !empty($test['test_name'])) {
            $test = $test['test_name'];
        }

        if (is_numeric($test)) {
            return (int) $test;
        }

        $test_name = trim((string) $test);
        if ($test_name === '') {
            return null;
        }

        $row = $this->db
            ->select('id')
            ->from('lab_tests')
            ->group_start()
                ->where('test_name', $test_name)
                ->or_where('test_code', $test_name)
            ->group_end()
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row();

        return $row ? (int) $row->id : null;
    }

    protected function table($key)
    {
        return isset($this->tables[$key]) ? $this->tables[$key] : $key;
    }
}