<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Patient Panel – Patients Controller
 *
 * Handles patient self-service list view and all AJAX endpoints.
 * Role: patient only.
 *
 * AJAX Endpoints (all return JSON):
 *   GET  patient/ajax/list          – paginated list
 *   POST patient/ajax/add           – create patient record
 *   POST patient/ajax/edit/:id      – update patient record
 *   POST patient/ajax/toggle/:id    – toggle active / inactive
 *   POST patient/ajax/delete/:id    – soft-delete (sets status = 2)
 */
class Patients extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role('patient');
        $this->load->model('Patient_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
    }

    // ---------------------------------------------------------------
    // LIST VIEW  (standalone full-page HTML — no shared header/footer)
    // ---------------------------------------------------------------

    public function index()
    {
        $data = array(
            'page_title'        => 'My Patient Records',
            'active_menu'       => 'patients',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
        );

        // list.php is a complete standalone HTML document.
        $this->load->view('patient/patients/list', $data);
    }


    // ---------------------------------------------------------------
    // AJAX – LIST (GET)
    // ---------------------------------------------------------------

    public function ajax_list()
    {
        if (!$this->input->is_ajax_request()) {
            return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
        }

        $search  = trim((string) $this->input->get('search', TRUE));
        $status  = trim((string) $this->input->get('status', TRUE)); // all | 1 | 0
        $page    = max(1, (int) $this->input->get('page', TRUE));
        $per_page = 10;
        $offset   = ($page - 1) * $per_page;

        $result = $this->Patient_model->get_filtered($search, $status, $per_page, $offset);

        return $this->_json(array(
            'status'      => TRUE,
            'data'        => $result['rows'],
            'total'       => $result['total'],
            'page'        => $page,
            'per_page'    => $per_page,
            'total_pages' => (int) ceil($result['total'] / $per_page),
        ));
    }

    // ---------------------------------------------------------------
    // AJAX – ADD (POST)
    // ---------------------------------------------------------------

    public function ajax_add()
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
        }

        if ($this->_validate_patient_form() === FALSE) {
            return $this->_json(array(
                'status'  => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors'  => $this->_validation_errors_array(),
            ), 422);
        }

        $patient_code = $this->Patient_model->generate_patient_code();
        $data         = $this->_patient_form_data();
        $data['patient_code'] = $patient_code;

        $id = $this->Patient_model->insert($data);

        if (!$id) {
            return $this->_json(array('status' => FALSE, 'message' => 'Unable to register patient. Please try again.'), 500);
        }

        return $this->_json(array(
            'status'  => TRUE,
            'message' => 'Patient registered successfully.',
            'id'      => $id,
        ));
    }

    // ---------------------------------------------------------------
    // AJAX – EDIT (POST)
    // ---------------------------------------------------------------

    public function ajax_edit($id)
    {
        $id = (int) $id;

        if ($this->input->method() === 'get') {
            if (!$this->input->is_ajax_request()) {
                return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
            }
            // Fetch patient data to pre-fill the modal form
            $patient = $this->Patient_model->get_by_id($id);
            if (empty($patient)) {
                return $this->_json(array('status' => FALSE, 'message' => 'Patient not found.'), 404);
            }
            return $this->_json(array('status' => TRUE, 'data' => $patient));
        }


        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
        }

        $patient = $this->Patient_model->get_by_id($id);
        if (empty($patient)) {
            return $this->_json(array('status' => FALSE, 'message' => 'Patient not found.'), 404);
        }

        if ($this->_validate_patient_form() === FALSE) {
            return $this->_json(array(
                'status'  => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors'  => $this->_validation_errors_array(),
            ), 422);
        }

        $updated = $this->Patient_model->update($id, $this->_patient_form_data());

        if (!$updated) {
            return $this->_json(array('status' => FALSE, 'message' => 'Unable to update patient. Please try again.'), 500);
        }

        return $this->_json(array('status' => TRUE, 'message' => 'Patient updated successfully.'));
    }

    // ---------------------------------------------------------------
    // AJAX – TOGGLE STATUS (POST)
    // ---------------------------------------------------------------

    public function ajax_toggle_status($id)
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
        }

        $patient = $this->Patient_model->toggle_status((int) $id);

        if (!$patient) {
            return $this->_json(array('status' => FALSE, 'message' => 'Unable to change status. Patient not found.'), 404);
        }

        $message = ((int) $patient['status'] === 1)
            ? 'Patient activated successfully.'
            : 'Patient deactivated successfully.';

        return $this->_json(array(
            'status'  => TRUE,
            'message' => $message,
            'data'    => $patient,
        ));
    }

    // ---------------------------------------------------------------
    // AJAX – DELETE (POST)
    // ---------------------------------------------------------------

    public function ajax_delete($id)
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            return $this->_json(array('status' => FALSE, 'message' => 'Invalid request.'), 400);
        }

        $patient = $this->Patient_model->get_by_id((int) $id);
        if (empty($patient)) {
            return $this->_json(array('status' => FALSE, 'message' => 'Patient not found.'), 404);
        }

        $deleted = $this->Patient_model->update((int) $id, array('status' => 2));

        if (!$deleted) {
            return $this->_json(array('status' => FALSE, 'message' => 'Unable to delete patient.'), 500);
        }

        return $this->_json(array('status' => TRUE, 'message' => 'Patient removed successfully.'));
    }

    // ---------------------------------------------------------------
    // PRIVATE HELPERS
    // ---------------------------------------------------------------

    private function _validate_patient_form()
    {
        $this->form_validation->set_rules('full_name',        'Full Name',        'trim|required|max_length[150]');
        $this->form_validation->set_rules('gender',           'Gender',           'trim|required|in_list[male,female,other]');
        $this->form_validation->set_rules('dob',              'Date of Birth',    'trim');
        $this->form_validation->set_rules('blood_group',      'Blood Group',      'trim|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown]');
        $this->form_validation->set_rules('phone',            'Phone',            'trim|required|max_length[30]');
        $this->form_validation->set_rules('email',            'Email',            'trim|valid_email|max_length[150]');
        $this->form_validation->set_rules('address',          'Address',          'trim');
        $this->form_validation->set_rules('emergency_contact','Emergency Contact','trim|max_length[30]');
        $this->form_validation->set_rules('status',           'Status',           'trim|required|in_list[0,1]');

        return $this->form_validation->run();
    }

    private function _patient_form_data()
    {
        $email             = trim((string) $this->input->post('email', TRUE));
        $dob               = trim((string) $this->input->post('dob', TRUE));
        $address           = trim((string) $this->input->post('address', TRUE));
        $emergency_contact = trim((string) $this->input->post('emergency_contact', TRUE));
        $blood_group       = trim((string) $this->input->post('blood_group', TRUE));

        return array(
            'full_name'         => trim((string) $this->input->post('full_name', TRUE)),
            'gender'            => trim((string) $this->input->post('gender', TRUE)),
            'dob'               => ($dob === '')              ? NULL          : $dob,
            'blood_group'       => ($blood_group === '')      ? 'Unknown'     : $blood_group,
            'phone'             => trim((string) $this->input->post('phone', TRUE)),
            'email'             => ($email === '')            ? NULL          : strtolower($email),
            'address'           => ($address === '')          ? NULL          : $address,
            'emergency_contact' => ($emergency_contact === '') ? NULL         : $emergency_contact,
            'status'            => (int) $this->input->post('status', TRUE),
        );
    }

    private function _validation_errors_array()
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

    private function _json($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
