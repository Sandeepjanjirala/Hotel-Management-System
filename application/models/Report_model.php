<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{
    public function get_revenue_analytics($start_date, $end_date)
    {
        return array(
            'cards' => array(
                'today_revenue' => $this->sum_billing_revenue(date('Y-m-d'), date('Y-m-d')),
                'monthly_revenue' => $this->sum_billing_revenue(date('Y-m-01'), date('Y-m-d')),
                'paid_invoices' => $this->count_invoices_by_status('paid', $start_date, $end_date),
                'unpaid_invoices' => $this->count_invoices_by_status('unpaid', $start_date, $end_date),
                'filtered_revenue' => $this->sum_billing_revenue($start_date, $end_date)
            ),
            'daily_chart' => $this->get_daily_revenue_chart($start_date, $end_date),
            'monthly_chart' => $this->get_monthly_revenue_chart()
        );
    }

    public function get_appointment_analytics($start_date, $end_date)
    {
        return array(
            'cards' => array(
                'total' => $this->count_appointments(null, $start_date, $end_date),
                'scheduled' => $this->count_appointments('scheduled', $start_date, $end_date),
                'completed' => $this->count_appointments('completed', $start_date, $end_date),
                'cancelled' => $this->count_appointments('cancelled', $start_date, $end_date)
            ),
            'doctor_wise' => $this->get_doctor_wise_appointments($start_date, $end_date)
        );
    }

    public function get_patient_analytics($start_date, $end_date)
    {
        return array(
            'cards' => array(
                'new_patients' => $this->count_new_patients($start_date, $end_date),
                'repeat_patients' => $this->count_repeat_patients($start_date, $end_date)
            ),
            'gender_stats' => $this->get_gender_statistics($start_date, $end_date)
        );
    }

    public function get_doctor_analytics($start_date, $end_date)
    {
        return array(
            'top_doctors' => $this->get_top_doctors($start_date, $end_date),
            'revenue_by_doctor' => $this->get_revenue_by_doctor($start_date, $end_date)
        );
    }

    public function get_lab_analytics($start_date, $end_date)
    {
        return array(
            'cards' => array(
                'lab_revenue' => $this->get_lab_revenue($start_date, $end_date),
                'pending_requests' => $this->count_lab_requests('pending', $start_date, $end_date),
                'processing_requests' => $this->count_lab_requests('processing', $start_date, $end_date),
                'completed_requests' => $this->count_lab_requests('completed', $start_date, $end_date)
            ),
            'top_tests' => $this->get_top_lab_tests($start_date, $end_date)
        );
    }

    private function sum_billing_revenue($start_date, $end_date)
    {
        $this->db->select('COALESCE(SUM(grand_total), 0) AS total', false);
        $this->db->from('billing');
        $this->db->where('payment_status', 'paid');
        $this->db->where('invoice_date >=', $start_date);
        $this->db->where('invoice_date <=', $end_date);

        $row = $this->db->get()->row();
        return $row ? (float) $row->total : 0;
    }

    private function count_invoices_by_status($status, $start_date, $end_date)
    {
        $this->db->from('billing');
        $this->db->where('payment_status', $status);
        $this->db->where('invoice_date >=', $start_date);
        $this->db->where('invoice_date <=', $end_date);

        return (int) $this->db->count_all_results();
    }

    private function get_daily_revenue_chart($start_date, $end_date)
    {
        $this->db->select('invoice_date, COALESCE(SUM(grand_total), 0) AS total', false);
        $this->db->from('billing');
        $this->db->where('payment_status', 'paid');
        $this->db->where('invoice_date >=', $start_date);
        $this->db->where('invoice_date <=', $end_date);
        $this->db->group_by('invoice_date');
        $this->db->order_by('invoice_date', 'ASC');

        return $this->db->get()->result_array();
    }

    private function get_monthly_revenue_chart()
    {
        $this->db->select('DATE_FORMAT(invoice_date, "%Y-%m") AS month, COALESCE(SUM(grand_total), 0) AS total', false);
        $this->db->from('billing');
        $this->db->where('payment_status', 'paid');
        $this->db->where('invoice_date >=', date('Y-m-01', strtotime('-11 months')));
        $this->db->where('invoice_date <=', date('Y-m-d'));
        $this->db->group_by('DATE_FORMAT(invoice_date, "%Y-%m")', false);
        $this->db->order_by('month', 'ASC');

        return $this->db->get()->result_array();
    }

    private function count_appointments($status, $start_date, $end_date)
    {
        $this->db->from('appointments');
        $this->db->where('appointment_date >=', $start_date);
        $this->db->where('appointment_date <=', $end_date);

        if ($status !== null) {
            $this->db->where('status', $status);
        }

        return (int) $this->db->count_all_results();
    }

    private function get_doctor_wise_appointments($start_date, $end_date)
    {
        $this->db->select('u.name AS doctor_name');
        $this->db->select('COUNT(a.id) AS total_appointments', false);
        $this->db->select('SUM(CASE WHEN a.status = "completed" THEN 1 ELSE 0 END) AS completed_count', false);
        $this->db->select('SUM(CASE WHEN a.status = "cancelled" THEN 1 ELSE 0 END) AS cancelled_count', false);
        $this->db->from('appointments a');
        $this->db->join('users u', 'u.id = a.doctor_id', 'left');
        $this->db->where('a.appointment_date >=', $start_date);
        $this->db->where('a.appointment_date <=', $end_date);
        $this->db->group_by(array('a.doctor_id', 'u.name'));
        $this->db->order_by('total_appointments', 'DESC');

        return $this->db->get()->result_array();
    }

    private function count_new_patients($start_date, $end_date)
    {
        $this->db->from('patients');
        $this->db->where('DATE(created_at) >=', $start_date);
        $this->db->where('DATE(created_at) <=', $end_date);

        return (int) $this->db->count_all_results();
    }

    private function count_repeat_patients($start_date, $end_date)
    {
        $this->db->select('patient_id');
        $this->db->from('appointments');
        $this->db->where('appointment_date >=', $start_date);
        $this->db->where('appointment_date <=', $end_date);
        $this->db->group_by('patient_id');
        $this->db->having('COUNT(id) >', 1);

        return (int) $this->db->get()->num_rows();
    }

    private function get_gender_statistics($start_date, $end_date)
    {
        $this->db->select('gender, COUNT(id) AS total', false);
        $this->db->from('patients');
        $this->db->where('DATE(created_at) >=', $start_date);
        $this->db->where('DATE(created_at) <=', $end_date);
        $this->db->group_by('gender');

        return $this->db->get()->result_array();
    }

    private function get_top_doctors($start_date, $end_date)
    {
        $this->db->select('u.name AS doctor_name, d.specialization');
        $this->db->select('COUNT(a.id) AS appointments_handled', false);
        $this->db->from('appointments a');
        $this->db->join('users u', 'u.id = a.doctor_id', 'left');
        $this->db->join('doctors d', 'd.user_id = u.id', 'left');
        $this->db->where('a.status', 'completed');
        $this->db->where('a.appointment_date >=', $start_date);
        $this->db->where('a.appointment_date <=', $end_date);
        $this->db->group_by(array('a.doctor_id', 'u.name', 'd.specialization'));
        $this->db->order_by('appointments_handled', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result_array();
    }

    private function get_revenue_by_doctor($start_date, $end_date)
    {
        $this->db->select('u.name AS doctor_name');
        $this->db->select('COALESCE(SUM(b.grand_total), 0) AS revenue', false);
        $this->db->from('billing b');
        $this->db->join('appointments a', 'a.id = b.appointment_id', 'left');
        $this->db->join('users u', 'u.id = a.doctor_id', 'left');
        $this->db->where('b.payment_status', 'paid');
        $this->db->where('b.invoice_date >=', $start_date);
        $this->db->where('b.invoice_date <=', $end_date);
        $this->db->group_by(array('a.doctor_id', 'u.name'));
        $this->db->order_by('revenue', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result_array();
    }

    private function get_lab_revenue($start_date, $end_date)
    {
        $this->db->select('COALESCE(SUM(lt.price), 0) AS total', false);
        $this->db->from('patient_lab_tests plt');
        $this->db->join('patient_lab_test_items plti', 'plti.lab_request_id = plt.id', 'inner');
        $this->db->join('lab_tests lt', 'lt.id = plti.lab_test_id', 'inner');
        $this->db->where('plt.status', 'completed');
        $this->db->where('plt.request_date >=', $start_date);
        $this->db->where('plt.request_date <=', $end_date);

        $row = $this->db->get()->row();
        return $row ? (float) $row->total : 0;
    }

    private function count_lab_requests($status, $start_date, $end_date)
    {
        $this->db->from('patient_lab_tests');
        $this->db->where('status', $status);
        $this->db->where('request_date >=', $start_date);
        $this->db->where('request_date <=', $end_date);

        return (int) $this->db->count_all_results();
    }

    private function get_top_lab_tests($start_date, $end_date)
    {
        $this->db->select('lt.test_name, lt.test_code, lt.category');
        $this->db->select('COUNT(plti.id) AS total_requests', false);
        $this->db->select('COALESCE(SUM(lt.price), 0) AS total_value', false);
        $this->db->from('patient_lab_test_items plti');
        $this->db->join('patient_lab_tests plt', 'plt.id = plti.lab_request_id', 'inner');
        $this->db->join('lab_tests lt', 'lt.id = plti.lab_test_id', 'inner');
        $this->db->where('plt.request_date >=', $start_date);
        $this->db->where('plt.request_date <=', $end_date);
        $this->db->group_by(array('plti.lab_test_id', 'lt.test_name', 'lt.test_code', 'lt.category'));
        $this->db->order_by('total_requests', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result_array();
    }
}