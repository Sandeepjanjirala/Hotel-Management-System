<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model
{
    protected $billing_table = 'billing';
    protected $items_table = 'billing_items';

    public function get_all($filters = array())
    {
        $this->db
            ->select('b.id, b.invoice_no, b.invoice_date, b.total_amount, b.payment_status, b.payment_method, b.created_at')
            ->select('a.appointment_code')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from($this->billing_table . ' b')
            ->join('appointments a', 'a.id = b.appointment_id', 'inner')
            ->join('patients p', 'p.id = b.patient_id', 'inner')
            ->join('users u', 'u.id = b.doctor_id', 'inner');

        if (!empty($filters['search'])) {
            $this->db
                ->group_start()
                ->like('b.invoice_no', $filters['search'])
                ->or_like('a.appointment_code', $filters['search'])
                ->or_like('p.patient_code', $filters['search'])
                ->or_like('p.full_name', $filters['search'])
                ->or_like('p.phone', $filters['search'])
                ->or_like('u.name', $filters['search'])
                ->group_end();
        }

        if (!empty($filters['invoice_date'])) {
            $this->db->where('b.invoice_date', $filters['invoice_date']);
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('b.payment_status', $filters['payment_status']);
        }

        return $this->db
            ->order_by('b.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_unbilled_appointments()
    {
        return $this->db
            ->select('a.id, a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join($this->billing_table . ' b', 'b.appointment_id = a.id', 'left')
            ->where('a.status !=', 'cancelled')
            ->where('b.id IS NULL', NULL, FALSE)
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_invoice_preview($appointment_id)
    {
        $appointment = $this->get_billable_appointment((int) $appointment_id);

        if (empty($appointment)) {
            return FALSE;
        }

        $items = array();
        $consultation_fee = (float) $appointment['consultation_fee'];
        $pharmacy_total = 0.00;
        $lab_total = 0.00;

        $items[] = array(
            'item_type' => 'consultation',
            'reference_id' => (int) $appointment['doctor_profile_id'],
            'item_name' => 'Consultation Fee - Dr. ' . $appointment['doctor_name'],
            'quantity' => 1,
            'unit_price' => number_format($consultation_fee, 2, '.', ''),
            'line_total' => number_format($consultation_fee, 2, '.', '')
        );

        $pharmacy = $this->get_pharmacy_charge((int) $appointment_id);

        if (!empty($pharmacy)) {
            $pharmacy_total = (float) $pharmacy['total_amount'];

            $items[] = array(
                'item_type' => 'pharmacy',
                'reference_id' => (int) $pharmacy['id'],
                'item_name' => 'Pharmacy Charges - ' . $pharmacy['bill_code'],
                'quantity' => 1,
                'unit_price' => number_format($pharmacy_total, 2, '.', ''),
                'line_total' => number_format($pharmacy_total, 2, '.', '')
            );
        }

        $lab_items = $this->get_lab_charge_items((int) $appointment_id);

        foreach ($lab_items as $lab_item) {
            $price = (float) $lab_item['price'];
            $lab_total += $price;

            $items[] = array(
                'item_type' => 'lab',
                'reference_id' => (int) $lab_item['lab_item_id'],
                'item_name' => 'Lab Test - ' . $lab_item['test_name'] . ' (' . $lab_item['test_code'] . ')',
                'quantity' => 1,
                'unit_price' => number_format($price, 2, '.', ''),
                'line_total' => number_format($price, 2, '.', '')
            );
        }

        $total_amount = $consultation_fee + $pharmacy_total + $lab_total;

        return array(
            'appointment' => $appointment,
            'items' => $items,
            'consultation_fee' => number_format($consultation_fee, 2, '.', ''),
            'pharmacy_total' => number_format($pharmacy_total, 2, '.', ''),
            'lab_total' => number_format($lab_total, 2, '.', ''),
            'total_amount' => number_format($total_amount, 2, '.', '')
        );
    }

    public function get_billable_appointment($appointment_id)
    {
        return $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.status')
            ->select('p.patient_code, p.full_name AS patient_name, p.gender, p.phone AS patient_phone, p.email AS patient_email, p.address AS patient_address')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('d.id AS doctor_profile_id, d.consultation_fee, dept.department_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join('doctors d', 'd.user_id = u.id', 'left')
            ->join('departments dept', 'dept.id = d.department_id', 'left')
            ->join($this->billing_table . ' b', 'b.appointment_id = a.id', 'left')
            ->where('a.id', (int) $appointment_id)
            ->where('a.status !=', 'cancelled')
            ->where('b.id IS NULL', NULL, FALSE)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_pharmacy_charge($appointment_id)
    {
        return $this->db
            ->select('pb.id, pb.bill_code, pb.total_amount')
            ->from('prescriptions pr')
            ->join('pharmacy_bills pb', 'pb.prescription_id = pr.id', 'inner')
            ->where('pr.appointment_id', (int) $appointment_id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_lab_charge_items($appointment_id)
    {
        return $this->db
            ->select('lri.id AS lab_item_id, lt.test_name, lt.test_code, lt.price')
            ->from('patient_lab_tests lr')
            ->join('patient_lab_test_items lri', 'lri.lab_request_id = lr.id', 'inner')
            ->join('lab_tests lt', 'lt.id = lri.lab_test_id', 'inner')
            ->where('lr.appointment_id', (int) $appointment_id)
            ->order_by('lri.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function invoice_exists_for_appointment($appointment_id)
    {
        return $this->db
            ->from($this->billing_table)
            ->where('appointment_id', (int) $appointment_id)
            ->count_all_results() > 0;
    }

    public function generate_invoice_no()
    {
        $year = date('Y');
        $prefix = 'INV-' . $year . '-';

        $last_invoice = $this->db
            ->select('invoice_no')
            ->from($this->billing_table)
            ->like('invoice_no', $prefix, 'after')
            ->order_by('invoice_no', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next_number = 1;

        if (!empty($last_invoice)) {
            $next_number = (int) substr($last_invoice['invoice_no'], strlen($prefix)) + 1;
        }

        do {
            $invoice_no = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
            $next_number++;
        } while ($this->invoice_no_exists($invoice_no));

        return $invoice_no;
    }

    public function invoice_no_exists($invoice_no)
    {
        return $this->db
            ->from($this->billing_table)
            ->where('invoice_no', $invoice_no)
            ->count_all_results() > 0;
    }

    public function create_invoice(array $billing_data, array $items)
    {
        $now = date('Y-m-d H:i:s');

        $billing_data['created_at'] = $now;
        $billing_data['updated_at'] = $now;

        $this->db->trans_begin();

        $this->db->insert($this->billing_table, $billing_data);
        $billing_id = $this->db->insert_id();

        if (!$billing_id) {
            $this->db->trans_rollback();
            return FALSE;
        }

        foreach ($items as $item) {
            $item['billing_id'] = $billing_id;
            $item['created_at'] = $now;

            $this->db->insert($this->items_table, $item);

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
        return $billing_id;
    }

    public function get_invoice($id)
    {
        return $this->db
            ->select('b.*')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.gender, p.phone AS patient_phone, p.email AS patient_email, p.address AS patient_address')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('dept.department_name')
            ->select('gb.name AS generated_by_name')
            ->from($this->billing_table . ' b')
            ->join('appointments a', 'a.id = b.appointment_id', 'inner')
            ->join('patients p', 'p.id = b.patient_id', 'inner')
            ->join('users u', 'u.id = b.doctor_id', 'inner')
            ->join('doctors d', 'd.user_id = u.id', 'left')
            ->join('departments dept', 'dept.id = d.department_id', 'left')
            ->join('users gb', 'gb.id = b.generated_by', 'inner')
            ->where('b.id', (int) $id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_invoice_items($billing_id)
    {
        return $this->db
            ->select('id, billing_id, item_type, reference_id, item_name, quantity, unit_price, line_total')
            ->from($this->items_table)
            ->where('billing_id', (int) $billing_id)
            ->order_by('id', 'ASC')
            ->get()
            ->result_array();
    }
}
