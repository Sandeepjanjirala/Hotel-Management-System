<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'receptionist'));
        $this->load->model('Appointment_model');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Appointment Management',
            'active_menu' => 'appointments',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/appointments/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function create()
    {
        $data = array(
            'page_title' => 'Book Appointment',
            'active_menu' => 'appointments',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'next_appointment_code' => $this->Appointment_model->generate_appointment_code(),
            'patients' => $this->Appointment_model->get_active_patients(),
            'doctors' => $this->Appointment_model->get_active_doctors()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/appointments/create', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/appointments');
            return;
        }

        if ($this->validate_appointment_form() === FALSE) {
            $this->create();
            return;
        }

        $appointment_code = $this->Appointment_model->generate_appointment_code();

        if ($this->Appointment_model->appointment_code_exists($appointment_code)) {
            $this->session->set_flashdata('error', 'Unable to generate a unique appointment code. Please try again.');
            redirect('admin/appointments/create');
            return;
        }

        $data = $this->appointment_form_data();
        $data['appointment_code'] = $appointment_code;
        $data['booked_by'] = $this->current_user_id();

        $created = $this->Appointment_model->insert($data);

        if (!$created) {
            $this->session->set_flashdata('error', 'Unable to book appointment at the moment.');
            redirect('admin/appointments/create');
            return;
        }

        $this->session->set_flashdata('success', 'Appointment booked successfully.');
        redirect('admin/appointments');
    }

    public function edit($id)
    {
        $appointment = $this->Appointment_model->get_by_id((int) $id);

        if (empty($appointment)) {
            show_404();
            return;
        }

        $data = array(
            'page_title' => 'Edit Appointment',
            'active_menu' => 'appointments',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'appointment' => $appointment,
            'patients' => $this->Appointment_model->get_active_patients(),
            'doctors' => $this->Appointment_model->get_active_doctors()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/appointments/edit', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/appointments');
            return;
        }

        $appointment = $this->Appointment_model->get_by_id((int) $id);

        if (empty($appointment)) {
            show_404();
            return;
        }

        if ($this->validate_appointment_form(TRUE) === FALSE) {
            $this->edit((int) $id);
            return;
        }

        $updated = $this->Appointment_model->update((int) $id, $this->appointment_form_data());

        if (!$updated) {
            $this->session->set_flashdata('error', 'Unable to update appointment at the moment.');
            redirect('admin/appointments/edit/' . (int) $id);
            return;
        }

        $this->session->set_flashdata('success', 'Appointment updated successfully.');
        redirect('admin/appointments');
    }

    public function change_status($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $status = trim((string) $this->input->post('status', TRUE));
        $allowed_statuses = array('scheduled', 'completed', 'cancelled');

        if (!in_array($status, $allowed_statuses, TRUE)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid appointment status.'
            ));
        }

        $appointment = $this->Appointment_model->get_by_id((int) $id);

        if (empty($appointment)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Appointment not found.'
            ));
        }

        $updated = $this->Appointment_model->change_status((int) $id, $status);

        if (!$updated) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to update appointment status.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Appointment status updated successfully.'
        ));
    }

    public function get_appointments_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'status' => trim((string) $this->input->get('status', TRUE)),
            'appointment_date' => trim((string) $this->input->get('appointment_date', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Appointment_model->get_all($filters)
        ));
    }

    protected function validate_appointment_form($is_update = FALSE)
    {
        $this->form_validation->set_rules('patient_id', 'Patient', 'trim|required|integer');
        $this->form_validation->set_rules('doctor_id', 'Doctor', 'trim|required|integer');
        $this->form_validation->set_rules('appointment_date', 'Appointment Date', 'trim|required');
        $this->form_validation->set_rules('appointment_time', 'Appointment Time', 'trim|required');
        $this->form_validation->set_rules('reason', 'Reason', 'trim|max_length[255]');
        $this->form_validation->set_rules('notes', 'Notes', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[scheduled,completed,cancelled]');

        if ($this->form_validation->run() === FALSE) {
            return FALSE;
        }

        $patient_id = (int) $this->input->post('patient_id', TRUE);
        $doctor_id = (int) $this->input->post('doctor_id', TRUE);

        if (!$this->Appointment_model->patient_exists($patient_id)) {
            $this->form_validation->set_message('patient_id', 'Please select a valid active patient.');
            $this->session->set_flashdata('error', 'Please select a valid active patient.');
            return FALSE;
        }

        if (!$this->Appointment_model->doctor_exists($doctor_id)) {
            $this->session->set_flashdata('error', 'Please select a valid active doctor.');
            return FALSE;
        }

        return TRUE;
    }

    protected function appointment_form_data()
    {
        $reason = trim((string) $this->input->post('reason', TRUE));
        $notes = trim((string) $this->input->post('notes', TRUE));

        return array(
            'patient_id' => (int) $this->input->post('patient_id', TRUE),
            'doctor_id' => (int) $this->input->post('doctor_id', TRUE),
            'appointment_date' => trim((string) $this->input->post('appointment_date', TRUE)),
            'appointment_time' => trim((string) $this->input->post('appointment_time', TRUE)),
            'reason' => ($reason === '') ? NULL : $reason,
            'notes' => ($notes === '') ? NULL : $notes,
            'status' => trim((string) $this->input->post('status', TRUE))
        );
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
