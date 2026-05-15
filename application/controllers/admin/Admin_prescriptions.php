<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_prescriptions extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'doctor'));
        $this->load->model('Prescription_model');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Prescriptions',
            'active_menu' => 'prescriptions',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/prescriptions/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function create($appointment_id = NULL)
    {
        $medicines = $this->Prescription_model->get_active_medicines();

        $data = array(
            'page_title' => 'Create Prescription',
            'active_menu' => 'prescriptions',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'appointments' => $this->Prescription_model->get_eligible_appointments($this->current_role(), $this->current_user_id()),
            'medicines' => $medicines,
            'selected_appointment' => NULL
        );

        if ($appointment_id !== NULL) {
            $data['selected_appointment'] = $this->Prescription_model->get_appointment_details(
                (int) $appointment_id,
                $this->current_role(),
                $this->current_user_id()
            );

            if (empty($data['selected_appointment'])) {
                $this->session->set_flashdata('error', 'Selected appointment is not available for prescription.');
                redirect('admin/prescriptions/create');
                return;
            }

            if ($this->Prescription_model->appointment_has_prescription((int) $appointment_id)) {
                $this->session->set_flashdata('error', 'A prescription already exists for this appointment.');
                redirect('admin/prescriptions/create');
                return;
            }
        }

        $data['initial_medicine_row'] = $this->load->view(
            'admin/prescriptions/partials/medicine_row',
            array(
                'row_index' => 0,
                'medicines' => $medicines
            ),
            TRUE
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/prescriptions/create', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            redirect('admin/prescriptions');
            return;
        }

        $appointment = NULL;
        $items = array();

        if ($this->validate_prescription_form($appointment, $items) === FALSE) {
            $this->create();
            return;
        }

        $notes = trim((string) $this->input->post('notes', TRUE));

        $prescription_data = array(
            'appointment_id' => (int) $appointment['id'],
            'patient_id' => (int) $appointment['patient_id'],
            'doctor_id' => (int) $appointment['doctor_id'],
            'symptoms' => trim((string) $this->input->post('symptoms', TRUE)),
            'diagnosis' => trim((string) $this->input->post('diagnosis', TRUE)),
            'notes' => ($notes === '') ? NULL : $notes,
            'prescription_date' => trim((string) $this->input->post('prescription_date', TRUE))
        );

        $created = $this->Prescription_model->create_prescription($prescription_data, $items);

        if (!$created) {
            $this->session->set_flashdata('error', 'Unable to save prescription at the moment.');
            redirect('admin/prescriptions/create');
            return;
        }

        $this->session->set_flashdata('success', 'Prescription saved successfully.');
        redirect('admin/prescriptions');
    }

    public function get_prescriptions_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'prescription_date' => trim((string) $this->input->get('prescription_date', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Prescription_model->get_all(
                $filters,
                $this->current_role(),
                $this->current_user_id()
            )
        ));
    }

    public function get_appointment_ajax($id)
    {
        $appointment = $this->Prescription_model->get_appointment_details(
            (int) $id,
            $this->current_role(),
            $this->current_user_id()
        );

        if (empty($appointment)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Appointment not found or not available.'
            ));
        }

        if ($this->Prescription_model->appointment_has_prescription((int) $id)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'A prescription already exists for this appointment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $appointment
        ));
    }

    public function medicine_row()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $row_index = (int) $this->input->post('row_index', TRUE);

        $html = $this->load->view(
            'admin/prescriptions/partials/medicine_row',
            array(
                'row_index' => $row_index,
                'medicines' => $this->Prescription_model->get_active_medicines()
            ),
            TRUE
        );

        return $this->json_response(array(
            'status' => TRUE,
            'html' => $html
        ));
    }

    private function validate_prescription_form(&$appointment, &$validated_items)
    {
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'trim|required|integer');
        $this->form_validation->set_rules('symptoms', 'Symptoms', 'trim|required');
        $this->form_validation->set_rules('diagnosis', 'Diagnosis', 'trim|required');
        $this->form_validation->set_rules('notes', 'Notes', 'trim');
        $this->form_validation->set_rules('prescription_date', 'Prescription Date', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            return FALSE;
        }

        $appointment_id = (int) $this->input->post('appointment_id', TRUE);

        $appointment = $this->Prescription_model->get_appointment_details(
            $appointment_id,
            $this->current_role(),
            $this->current_user_id()
        );

        if (empty($appointment)) {
            $this->session->set_flashdata('error', 'Please select a valid appointment.');
            return FALSE;
        }

        if ($this->Prescription_model->appointment_has_prescription($appointment_id)) {
            $this->session->set_flashdata('error', 'A prescription already exists for this appointment.');
            return FALSE;
        }

        $items = $this->input->post('items', TRUE);

        if (empty($items) || !is_array($items)) {
            $this->session->set_flashdata('error', 'Please add at least one medicine.');
            return FALSE;
        }

        foreach ($items as $item) {
            $medicine_id = isset($item['medicine_id']) ? (int) $item['medicine_id'] : 0;
            $dosage = isset($item['dosage']) ? trim((string) $item['dosage']) : '';
            $days = isset($item['days']) ? (int) $item['days'] : 0;
            $instructions = isset($item['instructions']) ? trim((string) $item['instructions']) : '';

            if ($medicine_id <= 0 || !$this->Prescription_model->medicine_exists($medicine_id)) {
                $this->session->set_flashdata('error', 'Please select a valid active medicine in every medicine row.');
                return FALSE;
            }

            if ($dosage === '') {
                $this->session->set_flashdata('error', 'Dosage is required in every medicine row.');
                return FALSE;
            }

            if ($days <= 0) {
                $this->session->set_flashdata('error', 'Days must be greater than zero in every medicine row.');
                return FALSE;
            }

            $validated_items[] = array(
                'medicine_id' => $medicine_id,
                'dosage' => $dosage,
                'days' => $days,
                'morning' => !empty($item['morning']) ? 1 : 0,
                'afternoon' => !empty($item['afternoon']) ? 1 : 0,
                'night' => !empty($item['night']) ? 1 : 0,
                'instructions' => ($instructions === '') ? NULL : $instructions
            );
        }

        return TRUE;
    }

    private function json_response($response)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
