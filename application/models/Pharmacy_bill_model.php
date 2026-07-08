<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_bill_model extends CI_Model
{
    protected $bills_table = 'pharmacy_bills';
    protected $items_table = 'pharmacy_bill_items';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = array())
    {
        $this->db
            ->select('pb.id, pb.bill_code, pb.prescription_id, pb.patient_id, pb.doctor_id, pb.bill_date, pb.total_amount, pb.created_at')
            ->select('pr.prescription_date')
            ->select('a.appointment_code')
            ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
            ->select('u.name AS doctor_name')
            ->select('COUNT(pbi.id) AS item_count')
            ->from($this->bills_table . ' pb')
            ->join('prescriptions pr', 'pr.id = pb.prescription_id', 'inner')
            ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
            ->join('patients p', 'p.id = pb.patient_id', 'inner')
            ->join('users u', 'u.id = pb.doctor_id', 'inner')
            ->join($this->items_table . ' pbi', 'pbi.pharmacy_bill_id = pb.id', 'left');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $this->db
                ->group_start()
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

        return $this->db
            ->group_by('pb.id')
            ->order_by('pb.id', 'DESC')
            ->get()
            ->result_array();
    }

public function get_unbilled_prescriptions()
{
    $this->db
        ->select('pr.id, pr.prescription_date, pr.diagnosis')
        ->select('a.appointment_code, a.appointment_date, a.appointment_time')
        ->select('p.patient_code, p.full_name AS patient_name, p.phone AS patient_phone')
        ->select('u.name AS doctor_name')
        ->from('prescriptions pr')
        ->join('appointments a', 'a.id = pr.appointment_id', 'inner')
        ->join('patients p', 'p.id = pr.patient_id', 'inner')
        ->join('users u', 'u.id = pr.doctor_id', 'inner')
        ->join($this->bills_table . ' pb', 'pb.prescription_id = pr.id', 'left')
        ->where('pb.id IS NULL', NULL, FALSE)
        ->order_by('pr.id', 'DESC');

    return $this->db->get()->result_array();
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
            ->join($this->bills_table . ' pb', 'pb.prescription_id = pr.id', 'left')
            ->where('pr.id', (int) $prescription_id)
            ->where('pb.id IS NULL', NULL, FALSE)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_prescription_items_for_billing($prescription_id)
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

            if ($frequency_count <= 0) {
                $frequency_count = 1;
            }

            $row['suggested_quantity'] = (int) $row['days'] * $frequency_count;
            $row['suggested_line_total'] = number_format((float) $row['suggested_quantity'] * (float) $row['unit_price'], 2, '.', '');
        }

        return $rows;
    }

    public function prescription_has_bill($prescription_id)
    {
        return $this->db
            ->from($this->bills_table)
            ->where('prescription_id', (int) $prescription_id)
            ->count_all_results() > 0;
    }

    public function generate_bill_code()
    {
        $year = date('Y');
        $prefix = 'PHB-' . $year . '-';

        $last_bill = $this->db
            ->select('bill_code')
            ->from($this->bills_table)
            ->like('bill_code', $prefix, 'after')
            ->order_by('bill_code', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        $next_number = 1;

        if (!empty($last_bill)) {
            $last_number = (int) substr($last_bill['bill_code'], strlen($prefix));
            $next_number = $last_number + 1;
        }

        do {
            $bill_code = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
            $next_number++;
        } while ($this->bill_code_exists($bill_code));

        return $bill_code;
    }

    public function bill_code_exists($bill_code)
    {
        return $this->db
            ->from($this->bills_table)
            ->where('bill_code', $bill_code)
            ->count_all_results() > 0;
    }

    public function create_bill(array $bill_data, array $items)
    {
        $now = date('Y-m-d H:i:s');

        $bill_data['created_at'] = $now;
        $bill_data['updated_at'] = $now;

        $this->db->trans_begin();

        $this->db->insert($this->bills_table, $bill_data);
        $bill_id = $this->db->insert_id();

        if (!$bill_id) {
            $this->db->trans_rollback();
            return FALSE;
        }

        foreach ($items as $item) {
            // Deduct stock
            $this->db
                ->set('stock_quantity', 'stock_quantity - ' . (int) $item['quantity'], FALSE)
                ->where('id', (int) $item['medicine_id'])
                ->where('stock_quantity >=', (int) $item['quantity'])
                ->update('medicines');

            if ($this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return FALSE;
            }

            $item['pharmacy_bill_id'] = $bill_id;
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

        return $bill_id;
    }
}