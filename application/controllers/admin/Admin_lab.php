<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_lab extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin', 'doctor', 'receptionist'));
        $this->load->model('Lab_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function tests()
    {
        $this->require_role(array('admin'));

        $data = array(
            'page_title' => 'Lab Test Master',
            'active_menu' => 'lab_tests',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load_admin_view('admin/lab/tests', $data);
    }

    public function get_tests_ajax()
    {
        $this->require_role(array('admin'));

        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'status' => trim((string) $this->input->get('status', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Lab_model->get_tests($filters)
        ));
    }

    public function store_test()
    {
        $this->require_role(array('admin'));

        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        $validated = $this->validate_test_form();

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $created = $this->Lab_model->insert_test($validated['data']);

        return $this->json_response(array(
            'status' => (bool) $created,
            'message' => $created ? 'Lab test created successfully.' : 'Unable to create lab test.'
        ));
    }

    public function edit_test($id)
    {
        $this->require_role(array('admin'));

        $test = $this->Lab_model->get_test_by_id((int) $id);

        if (empty($test)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Lab test not found.'));
        }

        return $this->json_response(array('status' => TRUE, 'data' => $test));
    }

    public function update_test($id)
    {
        $this->require_role(array('admin'));

        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        if (empty($this->Lab_model->get_test_by_id((int) $id))) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Lab test not found.'));
        }

        $validated = $this->validate_test_form((int) $id);

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $updated = $this->Lab_model->update_test((int) $id, $validated['data']);

        return $this->json_response(array(
            'status' => (bool) $updated,
            'message' => $updated ? 'Lab test updated successfully.' : 'Unable to update lab test.'
        ));
    }

    public function change_test_status($id)
    {
        $this->require_role(array('admin'));

        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        $test = $this->Lab_model->toggle_test_status((int) $id);

        if (!$test) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Unable to change lab test status.'));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => ((int) $test['status'] === 1) ? 'Lab test activated successfully.' : 'Lab test deactivated successfully.'
        ));
    }

    public function requests()
    {
        $data = array(
            'page_title' => 'Lab Requests',
            'active_menu' => 'lab_requests',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'counters' => $this->Lab_model->get_lab_counters($this->current_role(), $this->current_user_id())
        );

        $this->load_admin_view('admin/lab/requests', $data);
    }

    public function create_request()
    {
        $data = array(
            'page_title' => 'Create Lab Request',
            'active_menu' => 'lab_requests',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'appointments' => $this->Lab_model->get_appointments_for_request($this->current_role(), $this->current_user_id()),
            'lab_tests' => $this->Lab_model->get_active_tests()
        );

        $this->load_admin_view('admin/lab/create_request', $data);
    }

    public function store_request()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        $appointment = NULL;
        $lab_test_ids = array();

        if ($this->validate_request_form($appointment, $lab_test_ids) === FALSE) {
            return;
        }

        $request_code = $this->Lab_model->generate_request_code();

        $request_data = array(
            'patient_id' => (int) $appointment['patient_id'],
            'appointment_id' => (int) $appointment['id'],
            'doctor_id' => (int) $appointment['doctor_id'],
            'request_code' => $request_code,
            'request_date' => trim((string) $this->input->post('request_date', TRUE)),
            'status' => 'pending',
            'requested_by' => $this->current_user_id()
        );

        $created = $this->Lab_model->create_lab_request($request_data, $lab_test_ids);

        return $this->json_response(array(
            'status' => (bool) $created,
            'message' => $created ? 'Lab request created successfully.' : 'Unable to create lab request.',
            'redirectUrl' => site_url('admin/lab-requests')
        ));
    }

    public function get_requests_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'status' => trim((string) $this->input->get('status', TRUE)),
            'request_date' => trim((string) $this->input->get('request_date', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Lab_model->get_requests($filters, $this->current_role(), $this->current_user_id())
        ));
    }

    public function get_appointment_ajax($id)
    {
        $appointment = $this->Lab_model->get_appointment_details((int) $id, $this->current_role(), $this->current_user_id());

        if (empty($appointment)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Appointment not found or unavailable.'));
        }

        return $this->json_response(array('status' => TRUE, 'data' => $appointment));
    }

    public function change_request_status($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        $status = trim((string) $this->input->post('status', TRUE));
        $allowed = array('pending', 'processing', 'completed');

        if (!in_array($status, $allowed, TRUE)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid lab request status.'));
        }

        if (empty($this->Lab_model->get_request_by_id((int) $id, $this->current_role(), $this->current_user_id()))) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Lab request not found.'));
        }

        $updated = $this->Lab_model->update_request_status((int) $id, $status);

        return $this->json_response(array(
            'status' => (bool) $updated,
            'message' => $updated ? 'Lab request status updated successfully.' : 'Unable to update lab request status.'
        ));
    }

    public function results($id)
    {
        $request = $this->Lab_model->get_request_by_id((int) $id, $this->current_role(), $this->current_user_id());

        if (empty($request)) {
            show_404();
            return;
        }

        $data = array(
            'page_title' => 'Lab Results',
            'active_menu' => 'lab_requests',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role(),
            'request' => $request,
            'items' => $this->Lab_model->get_request_items((int) $id)
        );

        $this->load_admin_view('admin/lab/results', $data);
    }

    public function save_results($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request method.'));
        }

        if (empty($this->Lab_model->get_request_by_id((int) $id, $this->current_role(), $this->current_user_id()))) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Lab request not found.'));
        }

        $items = $this->input->post('items', TRUE);
        $request_status = trim((string) $this->input->post('status', TRUE));
        $allowed_request_statuses = array('pending', 'processing', 'completed');
        $allowed_result_statuses = array('pending', 'normal', 'abnormal');
        $validated_items = array();

        if (!in_array($request_status, $allowed_request_statuses, TRUE)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Invalid request status.'));
        }

        if (empty($items) || !is_array($items)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'No result rows found.'));
        }

        foreach ($items as $item) {
            $result_status = isset($item['result_status']) ? trim((string) $item['result_status']) : 'pending';

            if (!in_array($result_status, $allowed_result_statuses, TRUE)) {
                return $this->json_response(array('status' => FALSE, 'message' => 'Invalid result status detected.'));
            }

            $result_value = isset($item['result_value']) ? trim((string) $item['result_value']) : '';
            $remarks = isset($item['remarks']) ? trim((string) $item['remarks']) : '';

            $validated_items[] = array(
                'id' => (int) $item['item_id'],
                'result_value' => ($result_value === '') ? NULL : $result_value,
                'remarks' => ($remarks === '') ? NULL : $remarks,
                'result_status' => $result_status
            );
        }

        $saved = $this->Lab_model->save_results((int) $id, $validated_items, $request_status);

        return $this->json_response(array(
            'status' => (bool) $saved,
            'message' => $saved ? 'Lab results saved successfully.' : 'Unable to save lab results.',
            'redirectUrl' => site_url('admin/lab-requests')
        ));
    }

    protected function validate_test_form($test_id = NULL)
    {
        $post_data = $this->input->post(NULL, TRUE);

        $this->form_validation->reset_validation();
        $this->form_validation->set_data($post_data);

        $this->form_validation->set_rules('test_name', 'Test Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('test_code', 'Test Code', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('normal_range', 'Normal Range', 'trim|max_length[150]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'test_name' => form_error('test_name'),
                    'test_code' => form_error('test_code'),
                    'category' => form_error('category'),
                    'price' => form_error('price'),
                    'normal_range' => form_error('normal_range'),
                    'status' => form_error('status')
                )
            );
        }

        $test_code = strtoupper(trim((string) $this->input->post('test_code', TRUE)));

        if ($this->Lab_model->test_code_exists($test_code, $test_id)) {
            return array(
                'status' => FALSE,
                'errors' => array('test_code' => 'This test code already exists.')
            );
        }

        $normal_range = trim((string) $this->input->post('normal_range', TRUE));

        return array(
            'status' => TRUE,
            'data' => array(
                'test_name' => trim((string) $this->input->post('test_name', TRUE)),
                'test_code' => $test_code,
                'category' => trim((string) $this->input->post('category', TRUE)),
                'price' => number_format((float) $this->input->post('price', TRUE), 2, '.', ''),
                'normal_range' => ($normal_range === '') ? NULL : $normal_range,
                'status' => (int) $this->input->post('status', TRUE)
            )
        );
    }

    protected function validate_request_form(&$appointment, &$lab_test_ids)
    {
        $this->form_validation->set_rules('appointment_id', 'Appointment', 'trim|required|integer');
        $this->form_validation->set_rules('request_date', 'Request Date', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            return $this->json_response(array('status' => FALSE, 'message' => strip_tags(validation_errors())));
        }

        $appointment = $this->Lab_model->get_appointment_details(
            (int) $this->input->post('appointment_id', TRUE),
            $this->current_role(),
            $this->current_user_id()
        );

        if (empty($appointment)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Please select a valid appointment.'));
        }

        $posted_tests = $this->input->post('lab_tests', TRUE);

        if (empty($posted_tests) || !is_array($posted_tests)) {
            return $this->json_response(array('status' => FALSE, 'message' => 'Please select at least one lab test.'));
        }

        foreach ($posted_tests as $lab_test_id) {
            if (!$this->Lab_model->active_test_exists((int) $lab_test_id)) {
                return $this->json_response(array('status' => FALSE, 'message' => 'Invalid lab test selected.'));
            }

            $lab_test_ids[] = (int) $lab_test_id;
        }

        $lab_test_ids = array_values(array_unique($lab_test_ids));
        return TRUE;
    }

    protected function load_admin_view($view, $data)
    {
        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view($view, $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
