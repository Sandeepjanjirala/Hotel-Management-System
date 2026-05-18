<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lab_model extends CI_Model
{
    protected $tests_table = 'lab_tests';
    protected $requests_table = 'patient_lab_tests';
    protected $items_table = 'patient_lab_test_items';

    public function get_tests($filters = array())
    {
        $this->db->select('*')->from($this->tests_table);

        if (!empty($filters['search'])) {
            $this->db
                ->group_start()
                ->like('test_name', $filters['search'])
                ->or_like('test_code', $filters['search'])
                ->or_like('category', $filters['search'])
                ->group_end();
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('status', (int) $filters['status']);
        }

        return $this->db->order_by('id', 'DESC')->get()->result_array();
    }

    public function get_active_tests()
    {
        return $this->db
            ->select('id, test_name, test_code, category, price, normal_range')
            ->from($this->tests_table)
            ->where('status', 1)
            ->order_by('test_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_test_by_id($id)
    {
        return $this->db
            ->select('*')
            ->from($this->tests_table)
            ->where('id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function test_code_exists($test_code, $exclude_id = NULL)
    {
        $this->db->from($this->tests_table)->where('test_code', $test_code);

        if ($exclude_id !== NULL) {
            $this->db->where('id !=', (int) $exclude_id);
        }

        return $this->db->count_all_results() > 0;
    }

    public function insert_test(array $data)
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!$this->db->insert($this->tests_table, $data)) {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    public function update_test($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $id)
            ->update($this->tests_table, $data);
    }

    public function toggle_test_status($id)
    {
        $test = $this->get_test_by_id((int) $id);

        if (empty($test)) {
            return FALSE;
        }

        $new_status = ((int) $test['status'] === 1) ? 0 : 1;

        $updated = $this->db
            ->where('id', (int) $id)
            ->update($this->tests_table, array(
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ));

        return $updated ? $this->get_test_by_id((int) $id) : FALSE;
    }

    public function get_lab_counters($role = NULL, $user_id = NULL)
    {
        return array(
            'active_tests' => $this->count_active_tests(),
            'pending_requests' => $this->count_requests_by_status('pending', $role, $user_id),
            'processing_requests' => $this->count_requests_by_status('processing', $role, $user_id),
            'completed_requests' => $this->count_requests_by_status('completed', $role, $user_id)
        );
    }

    public function count_active_tests()
    {
        return (int) $this->db
            ->from($this->tests_table)
            ->where('status', 1)
            ->count_all_results();
    }

    public function count_requests_by_status($status, $role = NULL, $user_id = NULL)
    {
        $this->db->from($this->requests_table)->where('status', $status);

        if ($role === 'doctor') {
            $this->db->where('doctor_id', (int) $user_id);
        }

        return (int) $this->db->count_all_results();
    }

    public function get_appointments_for_request($role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->where('a.status !=', 'cancelled');

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
            ->select('u.name AS doctor_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->where('a.id', (int) $appointment_id)
            ->where('a.status !=', 'cancelled')
            ->limit(1);

        if ($role === 'doctor') {
            $this->db->where('a.doctor_id', (int) $user_id);
        }

        return $this->db->get()->row_array();
    }

    public function generate_request_code()
    {
        $year = date('Y');
        $prefix = 'LAB-' . $year . '-';

        $last_request = $this->db
            ->select('request_code')
            ->from($this->requests_table)
            ->like('request_code', $prefix, 'after')
            ->order_by('request_code', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next_number = 1;

        if (!empty($last_request)) {
            $next_number = (int) substr($last_request['request_code'], strlen($prefix)) + 1;
        }

        do {
            $request_code = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
            $next_number++;
        } while ($this->request_code_exists($request_code));

        return $request_code;
    }

    public function request_code_exists($request_code)
    {
        return $this->db
            ->from($this->requests_table)
            ->where('request_code', $request_code)
            ->count_all_results() > 0;
    }

    public function active_test_exists($lab_test_id)
    {
        return $this->db
            ->from($this->tests_table)
            ->where('id', (int) $lab_test_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function create_lab_request(array $request_data, array $lab_test_ids)
    {
        $now = date('Y-m-d H:i:s');

        $request_data['created_at'] = $now;
        $request_data['updated_at'] = $now;

        $this->db->trans_begin();
        $this->db->insert($this->requests_table, $request_data);
        $lab_request_id = $this->db->insert_id();

        if (!$lab_request_id) {
            $this->db->trans_rollback();
            return FALSE;
        }

        foreach ($lab_test_ids as $lab_test_id) {
            $this->db->insert($this->items_table, array(
                'lab_request_id' => $lab_request_id,
                'lab_test_id' => (int) $lab_test_id,
                'result_status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ));

            if ($this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $lab_request_id;
    }

    public function get_requests($filters = array(), $role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('lr.id, lr.request_code, lr.request_date, lr.status, lr.created_at')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->select('COUNT(lri.id) AS test_count')
            ->from($this->requests_table . ' lr')
            ->join('appointments a', 'a.id = lr.appointment_id', 'inner')
            ->join('patients p', 'p.id = lr.patient_id', 'inner')
            ->join('users u', 'u.id = lr.doctor_id', 'inner')
            ->join($this->items_table . ' lri', 'lri.lab_request_id = lr.id', 'left');

        if ($role === 'doctor') {
            $this->db->where('lr.doctor_id', (int) $user_id);
        }

        if (!empty($filters['search'])) {
            $this->db
                ->group_start()
                ->like('lr.request_code', $filters['search'])
                ->or_like('a.appointment_code', $filters['search'])
                ->or_like('p.patient_code', $filters['search'])
                ->or_like('p.full_name', $filters['search'])
                ->or_like('p.phone', $filters['search'])
                ->or_like('u.name', $filters['search'])
                ->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('lr.status', $filters['status']);
        }

        if (!empty($filters['request_date'])) {
            $this->db->where('lr.request_date', $filters['request_date']);
        }

        return $this->db
            ->group_by('lr.id')
            ->order_by('lr.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_request_by_id($id, $role = NULL, $user_id = NULL)
    {
        $this->db
            ->select('lr.id, lr.patient_id, lr.appointment_id, lr.doctor_id, lr.request_code, lr.request_date, lr.status, lr.created_at')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone, p.gender, p.blood_group')
            ->select('u.name AS doctor_name')
            ->from($this->requests_table . ' lr')
            ->join('appointments a', 'a.id = lr.appointment_id', 'inner')
            ->join('patients p', 'p.id = lr.patient_id', 'inner')
            ->join('users u', 'u.id = lr.doctor_id', 'inner')
            ->where('lr.id', (int) $id)
            ->limit(1);

        if ($role === 'doctor') {
            $this->db->where('lr.doctor_id', (int) $user_id);
        }

        return $this->db->get()->row_array();
    }

    public function get_request_items($lab_request_id)
    {
        return $this->db
            ->select('lri.id, lri.lab_request_id, lri.lab_test_id, lri.result_value, lri.remarks, lri.result_status')
            ->select('lt.test_name, lt.test_code, lt.category, lt.price, lt.normal_range')
            ->from($this->items_table . ' lri')
            ->join($this->tests_table . ' lt', 'lt.id = lri.lab_test_id', 'inner')
            ->where('lri.lab_request_id', (int) $lab_request_id)
            ->order_by('lri.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function update_request_status($id, $status)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->requests_table, array(
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function save_results($lab_request_id, array $items, $request_status)
    {
        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        foreach ($items as $item) {
            $this->db
                ->where('id', (int) $item['id'])
                ->where('lab_request_id', (int) $lab_request_id)
                ->update($this->items_table, array(
                    'result_value' => $item['result_value'],
                    'remarks' => $item['remarks'],
                    'result_status' => $item['result_status'],
                    'updated_at' => $now
                ));

            if ($this->db->affected_rows() < 0) {
                $this->db->trans_rollback();
                return FALSE;
            }
        }

        $this->db
            ->where('id', (int) $lab_request_id)
            ->update($this->requests_table, array(
                'status' => $request_status,
                'updated_at' => $now
            ));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }
}
