<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prescriptions extends Doctor_Controller
{
    public function create($appointment_id)
    {
        $appointment = $this->Doctor_model->get_appointment($this->doctor_user_id, $appointment_id);
        if (!$appointment) {
            show_404();
        }

        $this->render('doctor/prescriptions/create', array(
            'page_title' => 'Create Prescription',
            'active_menu' => 'appointments',
            'appointment' => $appointment
        ));
    }

    public function store()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'required|integer');
        $this->form_validation->set_rules('diagnosis', 'Diagnosis', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('notes', 'Notes', 'trim');
        $this->form_validation->set_rules('advice', 'Advice', 'trim');
        $this->form_validation->set_rules('follow_up_date', 'Follow-up date', 'trim');

        $appointment_id = (int) $this->input->post('appointment_id');
        $appointment = $this->Doctor_model->get_appointment($this->doctor_user_id, $appointment_id);

        if (!$appointment) {
            show_404();
        }

        if ($this->form_validation->run() === false) {
            return $this->render('doctor/prescriptions/create', array(
                'page_title' => 'Create Prescription',
                'active_menu' => 'appointments',
                'appointment' => $appointment
            ));
        }

        $prescription_id = $this->Doctor_model->create_prescription(
            $this->doctor_user_id,
            $appointment_id,
            $this->input->post(array('diagnosis', 'notes', 'advice', 'follow_up_date')),
            $this->input->post('items') ?: array()
        );

        if (!$prescription_id) {
            $this->session->set_flashdata('error', 'Unable to create prescription for this appointment.');
            redirect('doctor/prescriptions/create/' . $appointment_id);
        }

        $this->session->set_flashdata('success', 'Prescription created successfully.');
        redirect('doctor/appointments/view/' . $appointment_id);
    }
}