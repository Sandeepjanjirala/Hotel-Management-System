<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'receptionist'));
        $this->load->model('Appointment_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Appointment Management',
            'active_menu' => 'appointments',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'doctors' => $this->Appointment_model->get_active_doctors()
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

        if ($this->input->is_ajax_request()) {
            $html = $this->load->view('admin/appointments/_form', array_merge($data, array('is_modal' => TRUE)), TRUE);
            return $this->json_response(array(
                'status' => TRUE,
                'html' => $html
            ));
        }

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/appointments/create', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Invalid request method.'
                ));
            }
            redirect('admin/appointments');
            return;
        }

        if ($this->validate_appointment_form() === FALSE) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Please correct the highlighted errors.',
                    'errors' => $this->validation_errors_array()
                ));
            }
            $this->create();
            return;
        }

        $appointment_code = $this->Appointment_model->generate_appointment_code();

        if ($this->Appointment_model->appointment_code_exists($appointment_code)) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to generate a unique appointment code. Please try again.'
                ));
            }
            $this->session->set_flashdata('error', 'Unable to generate a unique appointment code. Please try again.');
            redirect('admin/appointments/create');
            return;
        }

        $data = $this->appointment_form_data();
        $data['appointment_code'] = $appointment_code;
        $data['booked_by'] = $this->current_user_id();

        $created = $this->Appointment_model->insert($data);

        if (!$created) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to book appointment at the moment.'
                ));
            }
            $this->session->set_flashdata('error', 'Unable to book appointment at the moment.');
            redirect('admin/appointments/create');
            return;
        }

        if ($this->input->is_ajax_request()) {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => 'Appointment booked successfully.'
            ));
        }

        $this->session->set_flashdata('success', 'Appointment booked successfully.');
        redirect('admin/appointments');
    }

    public function edit($id)
    {
        $appointment = $this->Appointment_model->get_by_id((int) $id);

        if (empty($appointment)) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Appointment not found.'
                ));
            }
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

        if ($this->input->is_ajax_request()) {
            $html = $this->load->view('admin/appointments/_form', array_merge($data, array('is_modal' => TRUE)), TRUE);
            return $this->json_response(array(
                'status' => TRUE,
                'html' => $html
            ));
        }

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/appointments/edit', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Invalid request method.'
                ));
            }
            redirect('admin/appointments');
            return;
        }

        $appointment = $this->Appointment_model->get_by_id((int) $id);

        if (empty($appointment)) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Appointment not found.'
                ));
            }
            show_404();
            return;
        }

        if ($this->validate_appointment_form() === FALSE) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Please correct the highlighted errors.',
                    'errors' => $this->validation_errors_array()
                ));
            }
            $this->edit((int) $id);
            return;
        }

        $updated = $this->Appointment_model->update((int) $id, $this->appointment_form_data());

        if (!$updated) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to update appointment at the moment.'
                ));
            }
            $this->session->set_flashdata('error', 'Unable to update appointment at the moment.');
            redirect('admin/appointments/edit/' . (int) $id);
            return;
        }

        if ($this->input->is_ajax_request()) {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => 'Appointment updated successfully.'
            ));
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
            'appointment_date' => trim((string) $this->input->get('appointment_date', TRUE)),
            'doctor_id' => trim((string) $this->input->get('doctor_id', TRUE))
        );

        $page = max(1, (int) $this->input->get('page', TRUE));
        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        $result = $this->Appointment_model->get_filtered($filters, $per_page, $offset);

        // Generate HTML for the table
        $html = $this->load->view('admin/appointments/_table', array(
            'appointments' => $result['rows']
        ), TRUE);

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $result['rows'],
            'html' => $html,
            'total' => (int) $result['total'],
            'page' => (int) $page,
            'per_page' => (int) $per_page,
            'total_pages' => (int) ceil($result['total'] / $per_page)
        ));
    }

    protected function validate_appointment_form()
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
            return FALSE;
        }

        if (!$this->Appointment_model->doctor_exists($doctor_id)) {
            $this->form_validation->set_message('doctor_id', 'Please select a valid active doctor.');
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

    protected function validation_errors_array()
    {
        $fields = array('patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'reason', 'notes', 'status');
        $errors = array();

        foreach ($fields as $field) {
            $error = form_error($field, '', '');
            if ($error !== '') {
                $errors[$field] = $error;
            }
        }

        return $errors;
    }


}