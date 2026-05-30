<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_reports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role(array('admin'));
        $this->load->model('Report_model');
    }

    public function index()
    {
        $data['title'] = 'Reports & Analytics';
        $data['active_menu'] = 'reports';

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/reports/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function revenue_analytics()
    {
        $filters = $this->get_date_filters();

        $this->json_response(array(
            'status' => true,
            'data' => $this->Report_model->get_revenue_analytics($filters['start_date'], $filters['end_date'])
        ));
    }

    public function appointment_analytics()
    {
        $filters = $this->get_date_filters();

        $this->json_response(array(
            'status' => true,
            'data' => $this->Report_model->get_appointment_analytics($filters['start_date'], $filters['end_date'])
        ));
    }

    public function patient_analytics()
    {
        $filters = $this->get_date_filters();

        $this->json_response(array(
            'status' => true,
            'data' => $this->Report_model->get_patient_analytics($filters['start_date'], $filters['end_date'])
        ));
    }

    public function doctor_analytics()
    {
        $filters = $this->get_date_filters();

        $this->json_response(array(
            'status' => true,
            'data' => $this->Report_model->get_doctor_analytics($filters['start_date'], $filters['end_date'])
        ));
    }

    public function lab_analytics()
    {
        $filters = $this->get_date_filters();

        $this->json_response(array(
            'status' => true,
            'data' => $this->Report_model->get_lab_analytics($filters['start_date'], $filters['end_date'])
        ));
    }

    protected function get_date_filters()
    {
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);

        if (!$this->is_valid_date($start_date)) {
            $start_date = date('Y-m-01');
        }

        if (!$this->is_valid_date($end_date)) {
            $end_date = date('Y-m-d');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            $temp = $start_date;
            $start_date = $end_date;
            $end_date = $temp;
        }

        return array(
            'start_date' => $start_date,
            'end_date' => $end_date
        );
    }

    protected function is_valid_date($date)
    {
        if (!$date) {
            return false;
        }

        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}