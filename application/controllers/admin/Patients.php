<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'receptionist'));
        $this->load->model('Patient_model');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Patient Management',
            'active_menu' => 'patients',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/patients/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function create()
    {
        $data = array(
            'page_title' => 'Register Patient',
            'active_menu' => 'patients',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'next_patient_code' => $this->Patient_model->generate_patient_code()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/patients/create', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/patients');
            return;
        }

        if ($this->validate_patient_form() === FALSE) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Please correct the highlighted errors.',
                    'errors' => $this->validation_errors_array()
                ), 422);
            }

            $this->create();
            return;
        }

        $patient_code = $this->Patient_model->generate_patient_code();

        if ($this->Patient_model->patient_code_exists($patient_code)) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to generate a unique patient code. Please try again.'
                ), 409);
            }

            $this->session->set_flashdata('error', 'Unable to generate a unique patient code. Please try again.');
            redirect('admin/patients/create');
            return;
        }

        $data = $this->patient_form_data();
        $data['patient_code'] = $patient_code;

        $created = $this->Patient_model->insert($data);

        if (!$created) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to register patient at the moment.'
                ), 500);
            }

            $this->session->set_flashdata('error', 'Unable to register patient at the moment.');
            redirect('admin/patients/create');
            return;
        }

        if ($this->input->is_ajax_request()) {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => 'Patient registered successfully.',
                'redirect' => site_url('admin/patients')
            ));
        }

        $this->session->set_flashdata('success', 'Patient registered successfully.');
        redirect('admin/patients');
    }

    public function edit($id)
    {
        $patient = $this->Patient_model->get_by_id((int) $id);

        if (empty($patient)) {
            show_404();
            return;
        }

        $data = array(
            'page_title' => 'Edit Patient',
            'active_menu' => 'patients',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'patient' => $patient
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/patients/edit', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/patients');
            return;
        }

        $patient = $this->Patient_model->get_by_id((int) $id);

        if (empty($patient)) {
            show_404();
            return;
        }

        if ($this->validate_patient_form() === FALSE) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Please correct the highlighted errors.',
                    'errors' => $this->validation_errors_array()
                ), 422);
            }

            $this->edit((int) $id);
            return;
        }

        $updated = $this->Patient_model->update((int) $id, $this->patient_form_data());

        if (!$updated) {
            if ($this->input->is_ajax_request()) {
                return $this->json_response(array(
                    'status' => FALSE,
                    'message' => 'Unable to update patient at the moment.'
                ), 500);
            }

            $this->session->set_flashdata('error', 'Unable to update patient at the moment.');
            redirect('admin/patients/edit/' . (int) $id);
            return;
        }

        if ($this->input->is_ajax_request()) {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => 'Patient updated successfully.',
                'redirect' => site_url('admin/patients')
            ));
        }

        $this->session->set_flashdata('success', 'Patient updated successfully.');
        redirect('admin/patients');
    }

    public function change_status($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $patient = $this->Patient_model->toggle_status((int) $id);

        if (!$patient) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to change patient status.'
            ));
        }

        $message = ((int) $patient['status'] === 1)
            ? 'Patient activated successfully.'
            : 'Patient deactivated successfully.';

        return $this->json_response(array(
            'status' => TRUE,
            'message' => $message,
            'data' => $patient,
            'row_html' => $this->render_patient_table(array($patient))
        ));
    }

    public function get_patients_ajax()
    {
        $search = trim((string) $this->input->get('search', TRUE));
        $patients = $this->Patient_model->get_all($search);

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $patients,
            'html' => $this->render_patient_table($patients)
        ));
    }

    protected function validate_patient_form()
    {
        $this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|in_list[male,female,other]');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim');
        $this->form_validation->set_rules('blood_group', 'Blood Group', 'trim|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('emergency_contact', 'Emergency Contact', 'trim|max_length[30]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        return $this->form_validation->run();
    }

    protected function patient_form_data()
    {
        $email = trim((string) $this->input->post('email', TRUE));
        $dob = trim((string) $this->input->post('dob', TRUE));
        $address = trim((string) $this->input->post('address', TRUE));
        $emergency_contact = trim((string) $this->input->post('emergency_contact', TRUE));
        $blood_group = trim((string) $this->input->post('blood_group', TRUE));

        return array(
            'full_name' => trim((string) $this->input->post('full_name', TRUE)),
            'gender' => trim((string) $this->input->post('gender', TRUE)),
            'dob' => ($dob === '') ? NULL : $dob,
            'blood_group' => ($blood_group === '') ? 'Unknown' : $blood_group,
            'phone' => trim((string) $this->input->post('phone', TRUE)),
            'email' => ($email === '') ? NULL : strtolower($email),
            'address' => ($address === '') ? NULL : $address,
            'emergency_contact' => ($emergency_contact === '') ? NULL : $emergency_contact,
            'status' => (int) $this->input->post('status', TRUE)
        );
    }

    protected function validation_errors_array()
    {
        $fields = array('full_name', 'gender', 'dob', 'blood_group', 'phone', 'email', 'address', 'emergency_contact', 'status');
        $errors = array();

        foreach ($fields as $field) {
            $error = form_error($field, '', '');
            if ($error !== '') {
                $errors[$field] = $error;
            }
        }

        return $errors;
    }

    protected function render_patient_table($patients)
    {
        return $this->load->view('admin/patients/_table', array(
            'patients' => $patients
        ), TRUE);
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
