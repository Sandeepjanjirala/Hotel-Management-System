<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lab_requests extends Doctor_Controller
{
    public function create($appointment_id)
    {
        $appointment = $this->Doctor_model->get_appointment($this->doctor_user_id, $appointment_id);
        if (!$appointment) {
            show_404();
        }

        $this->render('doctor/lab_requests/create', array(
            'page_title' => 'Create Lab Request',
            'active_menu' => 'appointments',
            'appointment' => $appointment
        ));
    }

    public function store()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'required|integer');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required|in_list[routine,urgent,stat]');
        $this->form_validation->set_rules('clinical_notes', 'Clinical notes', 'trim');

        $appointment_id = (int) $this->input->post('appointment_id');
        $appointment = $this->Doctor_model->get_appointment($this->doctor_user_id, $appointment_id);

        if (!$appointment) {
            show_404();
        }

        if ($this->form_validation->run() === false) {
            return $this->render('doctor/lab_requests/create', array(
                'page_title' => 'Create Lab Request',
                'active_menu' => 'appointments',
                'appointment' => $appointment
            ));
        }

        $lab_request_id = $this->Doctor_model->create_lab_request(
            $this->doctor_user_id,
            $appointment_id,
            $this->input->post(array('priority', 'clinical_notes')),
            $this->input->post('tests') ?: array()
        );

        if (!$lab_request_id) {
            $this->session->set_flashdata('error', 'Unable to create lab request for this appointment.');
            redirect('doctor/lab-requests/create/' . $appointment_id);
        }

        $this->session->set_flashdata('success', 'Lab request created successfully.');
        redirect('doctor/appointments/view/' . $appointment_id);
    }
}