<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receptionist_model extends CI_Model
{
    public function dashboard_counts()
    {
        return array(
            'total_patients' => $this->count_patients(),
            'today_appointments' => $this->count_appointments(array('appointment_date' => date('Y-m-d'))),
            'pending_bills' => $this->count_bills(array('payment_status' => 'unpaid')),
            'pharmacy_bills' => $this->count_pharmacy_bills()
        );
    }

    public function todays_appointments($limit = 10)
    {
        return $this->get_appointments(array('appointment_date' => date('Y-m-d')), $limit, 0);
    }

    public function recent_patients($limit = 8)
    {
        return $this->db
            ->select('id, patient_code, full_name, phone, email, created_at')
            ->from('patients')
            ->order_by('created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function pending_payment_summary()
    {
        $row = $this->db
            ->select('COUNT(id) AS total_bills, COALESCE(SUM(total_amount), 0) AS total_amount', false)
            ->from('billing')
            ->where('payment_status !=', 'paid')
            ->get()
            ->row_array();

        return $row ?: array('total_bills' => 0, 'total_amount' => 0);
    }

    public function get_patients($filters = array(), $limit = null, $offset = 0)
    {
        $this->patient_scope();
        $this->patient_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();
    }

    public function count_patients($filters = array())
    {
        $this->db->from('patients');
        $this->patient_filters($filters);

        return (int) $this->db->count_all_results();
    }

    public function get_patient($patient_id)
    {
        $this->patient_scope();

        return $this->db
            ->where('id', (int) $patient_id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function create_patient(array $data)
    {
        $now = date('Y-m-d H:i:s');
        $data['patient_code'] = $this->generate_patient_code();
        $data['status'] = 1;
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!$this->db->insert('patients', $data)) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function update_patient($patient_id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $patient_id)
            ->update('patients', $data);
    }

    public function get_departments()
    {
        return $this->db
            ->select('id, department_name')
            ->from('departments')
            ->where('status', 1)
            ->order_by('department_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_doctors($department_id = null)
    {
        $this->db
            ->select('u.id, u.name, u.email, u.department_id, d.department_name')
            ->from('users u')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->where('u.role', 'doctor')
            ->where('u.status', 1);

        if ($department_id !== null && $department_id !== '') {
            $this->db->where('u.department_id', (int) $department_id);
        }

        return $this->db
            ->order_by('u.name', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_appointments($filters = array(), $limit = null, $offset = 0)
    {
        $this->appointment_scope();
        $this->appointment_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result_array();
    }

    public function count_appointments($filters = array())
    {
        $this->db
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner');

        $this->appointment_filters($filters);

        return (int) $this->db->count_all_results();
    }

    public function get_appointment($appointment_id)
    {
        $this->appointment_scope();

        return $this->db
            ->where('a.id', (int) $appointment_id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function create_appointment(array $data, $booked_by)
    {
        if ($this->appointment_slot_taken($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $data['appointment_code'] = $this->generate_appointment_code();
        $data['booked_by'] = (int) $booked_by;
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!$this->db->insert('appointments', $data)) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function update_appointment($appointment_id, array $data)
    {
        if ($this->appointment_slot_taken($data['doctor_id'], $data['appointment_date'], $data['appointment_time'], $appointment_id)) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $appointment_id)
            ->update('appointments', $data);
    }

    public function cancel_appointment($appointment_id)
    {
        return $this->db
            ->where('id', (int) $appointment_id)
            ->update('appointments', array(
                'status' => 'cancelled',
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function get_available_slots($doctor_id, $date, $appointment_id = null)
    {
        $slots = array();
        $start = strtotime($date . ' 09:00:00');
        $end = strtotime($date . ' 17:00:00');

        for ($time = $start; $time <= $end; $time += 1800) {
            $slot = date('H:i:s', $time);
            if (!$this->appointment_slot_taken($doctor_id, $date, $slot, $appointment_id)) {
                $slots[] = array(
                    'value' => $slot,
                    'label' => date('h:i A', $time)
                );
            }
        }

        return $slots;
    }

    public function get_bills($filters = array(), $limit = null, $offset = 0)
    {
        $this->billing_scope();
        $this->billing_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->order_by('b.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function count_bills($filters = array())
    {
        $this->db
            ->from('billing b')
            ->join('appointments a', 'a.id = b.appointment_id', 'inner')
            ->join('patients p', 'p.id = b.patient_id', 'inner')
            ->join('users u', 'u.id = b.doctor_id', 'inner');

        $this->billing_filters($filters);

        return (int) $this->db->count_all_results();
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
            ->join('billing b', 'b.appointment_id = a.id', 'left')
            ->where('a.status !=', 'cancelled')
            ->where('b.id IS NULL', null, false)
            ->order_by('a.appointment_date', 'DESC')
            ->order_by('a.appointment_time', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_invoice_preview($appointment_id)
    {
        $appointment = $this->get_billable_appointment($appointment_id);
        if (!$appointment) {
            return false;
        }

        $consultation_fee = (float) $appointment['consultation_fee'];
        $pharmacy_total = 0.00;
        $lab_total = 0.00;
        $items = array();

        $items[] = array(
            'item_type' => 'consultation',
            'reference_id' => (int) $appointment['doctor_profile_id'],
            'item_name' => 'Consultation Fee - Dr. ' . $appointment['doctor_name'],
            'quantity' => 1,
            'unit_price' => number_format($consultation_fee, 2, '.', ''),
            'line_total' => number_format($consultation_fee, 2, '.', '')
        );

        $pharmacy = $this->get_pharmacy_charge($appointment_id);
        if ($pharmacy) {
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

        foreach ($this->get_lab_charge_items($appointment_id) as $lab_item) {
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

    public function create_invoice($appointment_id, $invoice_date, $payment_status, $payment_method, $generated_by)
    {
        $preview = $this->get_invoice_preview($appointment_id);
        if (!$preview) {
            return false;
        }

        $invoice_no = $this->generate_invoice_no();
        $payment_method = ($payment_status === 'unpaid') ? null : $payment_method;

        $billing_data = array(
            'invoice_no' => $invoice_no,
            'appointment_id' => (int) $preview['appointment']['id'],
            'patient_id' => (int) $preview['appointment']['patient_id'],
            'doctor_id' => (int) $preview['appointment']['doctor_id'],
            'invoice_date' => $invoice_date,
            'consultation_fee' => $preview['consultation_fee'],
            'pharmacy_total' => $preview['pharmacy_total'],
            'lab_total' => $preview['lab_total'],
            'total_amount' => $preview['total_amount'],
            'payment_status' => $payment_status,
            'payment_method' => $payment_method,
            'paid_at' => ($payment_status === 'paid') ? date('Y-m-d H:i:s') : null,
            'generated_by' => (int) $generated_by,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db->insert('billing', $billing_data);
        $billing_id = $this->db->insert_id();

        foreach ($preview['items'] as $item) {
            $item['billing_id'] = $billing_id;
            $item['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('billing_items', $item);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $billing_id : false;
    }

    public function get_invoice($billing_id)
    {
        $this->billing_scope();

        return $this->db
            ->where('b.id', (int) $billing_id)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_invoice_items($billing_id)
    {
        return $this->db
            ->select('id, item_type, item_name, quantity, unit_price, line_total')
            ->from('billing_items')
            ->where('billing_id', (int) $billing_id)
            ->order_by('id', 'ASC')
            ->get()
            ->result_array();
    }

    public function mark_payment_status($billing_id, $payment_status, $payment_method = null)
    {
        return $this->db
            ->where('id', (int) $billing_id)
            ->update('billing', array(
                'payment_status' => $payment_status,
                'payment_method' => ($payment_status === 'unpaid') ? null : $payment_method,
                'paid_at' => ($payment_status === 'paid') ? date('Y-m-d H:i:s') : null,
                'updated_at' => date('Y-m-d H:i:s')
            ));
    }

    public function get_pharmacy_bills($filters = array(), $limit = null, $offset = 0)
    {
        $this->pharmacy_scope();
        $this->pharmacy_filters($filters);

        if ($limit !== null) {
            $this->db->limit((int) $limit, (int) $offset);
        }

        return $this->db
            ->group_by('pb.id')
            ->order_by('pb.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function count_pharmacy_bills($filters = array())
    {
        $this->db
            ->select('COUNT(DISTINCT pb.id) AS aggregate', false)
            ->from('pharmacy_bills pb')
            ->join('prescriptions pr', 'pr.id = pb.prescription_id', 'inner')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pb.patient_id', 'inner')
            ->join('users u', 'u.id = pb.doctor_id', 'inner');

        $this->pharmacy_filters($filters);
        $row = $this->db->get()->row_array();

        return $row ? (int) $row['aggregate'] : 0;
    }

    public function get_pharmacy_bill($bill_id)
    {
        $this->pharmacy_scope();

        return $this->db
            ->where('pb.id', (int) $bill_id)
            ->group_by('pb.id')
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_pharmacy_bill_items($bill_id)
    {
        return $this->db
            ->select('pbi.id, pbi.quantity, pbi.unit_price, pbi.line_total')
            ->select('m.medicine_name, m.generic_name, m.unit')
            ->from('pharmacy_bill_items pbi')
            ->join('medicines m', 'm.id = pbi.medicine_id', 'inner')
            ->where('pbi.pharmacy_bill_id', (int) $bill_id)
            ->order_by('pbi.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function get_unbilled_prescriptions()
    {
        return $this->db
            ->select('pr.id, pr.prescription_date, pr.diagnosis')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from('prescriptions pr')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pr.patient_id', 'inner')
            ->join('users u', 'u.id = pr.doctor_id', 'inner')
            ->join('pharmacy_bills pb', 'pb.prescription_id = pr.id', 'left')
            ->where('pb.id IS NULL', null, false)
            ->order_by('pr.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_prescription_details($prescription_id)
    {
        return $this->db
            ->select('pr.id, pr.appointment_id, pr.patient_id, pr.doctor_id, pr.prescription_date, pr.symptoms, pr.diagnosis, pr.notes')
            ->select('a.appointment_code, a.appointment_date, a.appointment_time')
            ->select('p.patient_code, p.full_name AS patient_name, p.gender, p.phone AS patient_phone, p.blood_group')
            ->select('u.name AS doctor_name')
            ->from('prescriptions pr')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pr.patient_id', 'inner')
            ->join('users u', 'u.id = pr.doctor_id', 'inner')
            ->join('pharmacy_bills pb', 'pb.prescription_id = pr.id', 'left')
            ->where('pr.id', (int) $prescription_id)
            ->where('pb.id IS NULL', null, false)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_prescription_items($prescription_id)
    {
        $rows = $this->db
            ->select('pi.id AS prescription_item_id, pi.medicine_id, pi.dosage, pi.days, pi.morning, pi.afternoon, pi.night, pi.instructions')
            ->select('m.medicine_name, m.generic_name, m.unit, m.stock_quantity, m.unit_price')
            ->from('prescription_items pi')
            ->join('medicines m', 'm.id = pi.medicine_id', 'inner')
            ->where('pi.prescription_id', (int) $prescription_id)
            ->where('m.status', 1)
            ->order_by('pi.id', 'ASC')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $frequency_count = (int) $row['morning'] + (int) $row['afternoon'] + (int) $row['night'];
            $frequency_count = $frequency_count > 0 ? $frequency_count : 1;
            $row['suggested_quantity'] = (int) $row['days'] * $frequency_count;
            $row['suggested_line_total'] = number_format($row['suggested_quantity'] * (float) $row['unit_price'], 2, '.', '');
        }

        return $rows;
    }

    public function create_pharmacy_bill($prescription_id, $bill_date, array $items, $generated_by)
    {
        $prescription = $this->get_prescription_details($prescription_id);
        if (!$prescription) {
            return false;
        }

        $source_items = $this->get_prescription_items($prescription_id);
        $source_map = array();
        foreach ($source_items as $source_item) {
            $source_map[(int) $source_item['prescription_item_id']] = $source_item;
        }

        $bill_items = array();
        $total_amount = 0;

        foreach ($items as $item) {
            $prescription_item_id = isset($item['prescription_item_id']) ? (int) $item['prescription_item_id'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

            if ($prescription_item_id <= 0 || $quantity <= 0 || !isset($source_map[$prescription_item_id])) {
                return false;
            }

            $source = $source_map[$prescription_item_id];
            if ($quantity > (int) $source['stock_quantity']) {
                return false;
            }

            $unit_price = (float) $source['unit_price'];
            $line_total = $quantity * $unit_price;
            $total_amount += $line_total;

            $bill_items[] = array(
                'prescription_item_id' => $prescription_item_id,
                'medicine_id' => (int) $source['medicine_id'],
                'quantity' => $quantity,
                'unit_price' => number_format($unit_price, 2, '.', ''),
                'line_total' => number_format($line_total, 2, '.', '')
            );
        }

        if ($total_amount <= 0) {
            return false;
        }

        $this->db->trans_start();
        $this->db->insert('pharmacy_bills', array(
            'bill_code' => $this->generate_pharmacy_bill_code(),
            'prescription_id' => (int) $prescription['id'],
            'patient_id' => (int) $prescription['patient_id'],
            'doctor_id' => (int) $prescription['doctor_id'],
            'bill_date' => $bill_date,
            'total_amount' => number_format($total_amount, 2, '.', ''),
            'generated_by' => (int) $generated_by,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ));
        $bill_id = $this->db->insert_id();

        foreach ($bill_items as $bill_item) {
            $this->db
                ->set('stock_quantity', 'stock_quantity - ' . (int) $bill_item['quantity'], false)
                ->where('id', (int) $bill_item['medicine_id'])
                ->where('stock_quantity >=', (int) $bill_item['quantity'])
                ->update('medicines');

            $bill_item['pharmacy_bill_id'] = $bill_id;
            $bill_item['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('pharmacy_bill_items', $bill_item);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $bill_id : false;
    }

    protected function patient_scope()
    {
        $this->db->select('id, patient_code, full_name, gender, dob, blood_group, phone, email, address, emergency_contact, status, created_at, updated_at')->from('patients');
    }

    protected function patient_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('patient_code', $search)
                ->or_like('full_name', $search)
                ->or_like('phone', $search)
                ->or_like('email', $search)
                ->group_end();
        }
    }

    protected function appointment_scope()
    {
        $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.reason, a.notes, a.status, a.created_at')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('d.department_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join('departments d', 'd.id = u.department_id', 'left');
    }

    protected function appointment_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
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

        if (!empty($filters['doctor_id'])) {
            $this->db->where('a.doctor_id', (int) $filters['doctor_id']);
        }
    }

    protected function billing_scope()
    {
        $this->db
            ->select('b.id, b.invoice_no, b.invoice_date, b.total_amount, b.payment_status, b.payment_method, b.paid_at, b.created_at')
            ->select('a.appointment_code')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->from('billing b')
            ->join('appointments a', 'a.id = b.appointment_id', 'inner')
            ->join('patients p', 'p.id = b.patient_id', 'inner')
            ->join('users u', 'u.id = b.doctor_id', 'inner');
    }

    protected function billing_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('b.invoice_no', $search)
                ->or_like('a.appointment_code', $search)
                ->or_like('p.patient_code', $search)
                ->or_like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('u.name', $search)
                ->group_end();
        }

        if (!empty($filters['invoice_date'])) {
            $this->db->where('b.invoice_date', $filters['invoice_date']);
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('b.payment_status', $filters['payment_status']);
        }
    }

    protected function pharmacy_scope()
    {
        $this->db
            ->select('pb.id, pb.bill_code, pb.prescription_id, pb.patient_id, pb.doctor_id, pb.bill_date, pb.total_amount, pb.created_at')
            ->select('pr.prescription_date')
            ->select('a.appointment_code')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->select('COUNT(pbi.id) AS item_count')
            ->from('pharmacy_bills pb')
            ->join('prescriptions pr', 'pr.id = pb.prescription_id', 'inner')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pb.patient_id', 'inner')
            ->join('users u', 'u.id = pb.doctor_id', 'inner')
            ->join('pharmacy_bill_items pbi', 'pbi.pharmacy_bill_id = pb.id', 'left');
    }

    protected function pharmacy_filters($filters)
    {
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('pb.bill_code', $search)
                ->or_like('a.appointment_code', $search)
                ->or_like('p.patient_code', $search)
                ->or_like('p.full_name', $search)
                ->or_like('p.phone', $search)
                ->or_like('u.name', $search)
                ->group_end();
        }

        if (!empty($filters['bill_date'])) {
            $this->db->where('pb.bill_date', $filters['bill_date']);
        }
    }

    protected function get_billable_appointment($appointment_id)
    {
        return $this->db
            ->select('a.id, a.appointment_code, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.status')
            ->select('p.patient_code, p.full_name AS patient_name, p.gender, p.phone AS patient_phone, p.email AS patient_email, p.address AS patient_address')
            ->select('u.name AS doctor_name, u.email AS doctor_email')
            ->select('d.id AS doctor_profile_id, COALESCE(d.consultation_fee, 0) AS consultation_fee', false)
            ->select('dept.department_name')
            ->from('appointments a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->join('users u', 'u.id = a.doctor_id', 'inner')
            ->join('doctors d', 'd.user_id = u.id', 'left')
            ->join('departments dept', 'dept.id = d.department_id', 'left')
            ->join('billing b', 'b.appointment_id = a.id', 'left')
            ->where('a.id', (int) $appointment_id)
            ->where('a.status !=', 'cancelled')
            ->where('b.id IS NULL', null, false)
            ->limit(1)
            ->get()
            ->row_array();
    }

    protected function get_pharmacy_charge($appointment_id)
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

    protected function get_lab_charge_items($appointment_id)
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

    protected function generate_patient_code()
    {
        return $this->generate_prefixed_code('patients', 'patient_code', 'PAT-' . date('Y') . '-');
    }

    protected function generate_appointment_code()
    {
        return $this->generate_prefixed_code('appointments', 'appointment_code', 'APT-' . date('Y') . '-');
    }

    protected function generate_invoice_no()
    {
        return $this->generate_prefixed_code('billing', 'invoice_no', 'INV-' . date('Y') . '-');
    }

    protected function generate_pharmacy_bill_code()
    {
        return $this->generate_prefixed_code('pharmacy_bills', 'bill_code', 'PHB-' . date('Y') . '-');
    }

    protected function generate_prefixed_code($table, $column, $prefix)
    {
        $last = $this->db
            ->select($column)
            ->from($table)
            ->like($column, $prefix, 'after')
            ->order_by($column, 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next = $last ? ((int) substr($last[$column], strlen($prefix)) + 1) : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    protected function appointment_slot_taken($doctor_id, $date, $time, $exclude_appointment_id = null)
    {
        $this->db
            ->from('appointments')
            ->where('doctor_id', (int) $doctor_id)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->where('status !=', 'cancelled');

        if ($exclude_appointment_id !== null) {
            $this->db->where('id !=', (int) $exclude_appointment_id);
        }

        return $this->db->count_all_results() > 0;
    }
}
