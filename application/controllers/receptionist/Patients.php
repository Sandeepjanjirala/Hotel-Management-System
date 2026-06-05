<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends Receptionist_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receptionist_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        $this->render('receptionist/patients/index', array(
            'page_title' => 'Patients',
            'active_menu' => 'patients'
        ));
    }

    public function ajax_list()
    {
        $filters = array('search' => trim((string) $this->input->get('search', true)));

        return $this->json_response(array(
            'status' => true,
            'data' => $this->Receptionist_model->get_patients($filters, 50, 0)
        ));
    }

    public function create()
    {
        $this->render('receptionist/patients/form', array(
            'page_title' => 'Register Patient',
            'active_menu' => 'patients',
            'patient' => null,
            'form_action' => 'receptionist/patients/store'
        ));
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('receptionist/patients');
            return;
        }

        if (!$this->validate_patient()) {
            $this->create();
            return;
        }

        $created = $this->Receptionist_model->create_patient($this->patient_payload());

        $this->session->set_flashdata($created ? 'success' : 'error', $created ? 'Patient registered successfully.' : 'Unable to register patient.');
        redirect('receptionist/patients');
    }

    public function edit($id)
    {
        $patient = $this->Receptionist_model->get_patient((int) $id);
        if (!$patient) {
            show_404();
            return;
        }

        $this->render('receptionist/patients/form', array(
            'page_title' => 'Edit Patient',
            'active_menu' => 'patients',
            'patient' => $patient,
            'form_action' => 'receptionist/patients/update/' . (int) $id
        ));
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('receptionist/patients');
            return;
        }

        if (!$this->Receptionist_model->get_patient((int) $id)) {
            show_404();
            return;
        }

        if (!$this->validate_patient()) {
            $this->edit((int) $id);
            return;
        }

        $updated = $this->Receptionist_model->update_patient((int) $id, $this->patient_payload());

        $this->session->set_flashdata($updated ? 'success' : 'error', $updated ? 'Patient updated successfully.' : 'Unable to update patient.');
        redirect('receptionist/patients');
    }

    public function view($id)
    {
        $patient = $this->Receptionist_model->get_patient((int) $id);
        if (!$patient) {
            show_404();
            return;
        }

        $this->render('receptionist/patients/view', array(
            'page_title' => 'Patient Profile',
            'active_menu' => 'patients',
            'patient' => $patient,
            'appointments' => $this->Receptionist_model->get_appointments(array('search' => $patient['patient_code']), 25, 0)
        ));
    }

    protected function validate_patient()
    {
        $this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|in_list[male,female,other]');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim');
        $this->form_validation->set_rules('blood_group', 'Blood Group', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('emergency_contact', 'Emergency Contact', 'trim|max_length[30]');

        return $this->form_validation->run();
    }

    protected function patient_payload()
    {
        return array(
            'full_name' => trim((string) $this->input->post('full_name', true)),
            'gender' => trim((string) $this->input->post('gender', true)),
            'dob' => trim((string) $this->input->post('dob', true)) ?: null,
            'blood_group' => trim((string) $this->input->post('blood_group', true)),
            'phone' => trim((string) $this->input->post('phone', true)),
            'email' => trim((string) $this->input->post('email', true)) ?: null,
            'address' => trim((string) $this->input->post('address', true)) ?: null,
            'emergency_contact' => trim((string) $this->input->post('emergency_contact', true)) ?: null
        );
    }
}
