<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointments extends Doctor_Controller
{
    public function index()
    {
        $this->render('doctor/appointments/index', array(
            'page_title' => 'My Appointments',
            'active_menu' => 'appointments'
        ));
    }

    public function ajax_list()
    {
        $filters = $this->list_filters();
        $limit = (int) $this->input->post('length');
        $offset = (int) $this->input->post('start');
        $draw = (int) $this->input->post('draw');

        if ($limit <= 0) {
            $limit = 25;
        }

        $appointments = $this->Doctor_model->get_appointments($this->doctor_user_id, $filters, $limit, $offset);
        $records_total = $this->Doctor_model->count_appointments($this->doctor_user_id);
        $records_filtered = $this->Doctor_model->count_appointments($this->doctor_user_id, $filters);

        return $this->json_response(array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_filtered,
            'data' => $this->appointment_rows($appointments)
        ));
    }

    public function view($appointment_id)
    {
        $appointment = $this->Doctor_model->get_appointment($this->doctor_user_id, $appointment_id);
        if (!$appointment) {
            show_404();
        }

        $this->render('doctor/appointments/view', array(
            'page_title' => 'Appointment Details',
            'active_menu' => 'appointments',
            'appointment' => $appointment
        ));
    }

    protected function list_filters()
    {
        $search = $this->input->post('search');

        return array(
            'search' => is_array($search) && isset($search['value']) ? $search['value'] : $this->input->post('search_text'),
            'status' => $this->input->post('status'),
            'date_from' => $this->input->post('date_from'),
            'date_to' => $this->input->post('date_to')
        );
    }

    protected function appointment_rows($appointments)
    {
        $rows = array();

        foreach ($appointments as $appointment) {
            $rows[] = array(
                'appointment_id' => (int) $appointment->appointment_id,
                'appointment_date' => html_escape($appointment->appointment_date),
                'appointment_time' => html_escape($appointment->appointment_time),
                'patient_name' => html_escape($appointment->patient_name),
                'patient_mobile' => html_escape($appointment->patient_mobile),
                'reason' => html_escape($appointment->reason),
                'status' => html_escape($appointment->status),
                'actions' => $this->load->view('doctor/appointments/_actions', array(
                    'appointment' => $appointment
                ), true)
            );
        }

        return $rows;
    }
}