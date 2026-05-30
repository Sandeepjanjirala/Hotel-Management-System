<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_medicines extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin'));
        $this->load->model('Medicine_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Medicine Master',
            'active_menu' => 'medicines',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/medicines/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function get_medicines_ajax()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', TRUE)),
            'status' => trim((string) $this->input->get('status', TRUE))
        );

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Medicine_model->get_all($filters)
        ));
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $validated = $this->validate_medicine_form();

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $created = $this->Medicine_model->insert($validated['data']);

        if (!$created) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to create medicine at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Medicine created successfully.'
        ));
    }

    public function edit($id)
    {
        $medicine = $this->Medicine_model->get_by_id((int) $id);

        if (empty($medicine)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Medicine not found.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $medicine
        ));
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $medicine = $this->Medicine_model->get_by_id((int) $id);

        if (empty($medicine)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Medicine not found.'
            ));
        }

        $validated = $this->validate_medicine_form((int) $id);

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $updated = $this->Medicine_model->update((int) $id, $validated['data']);

        if (!$updated) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to update medicine at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Medicine updated successfully.'
        ));
    }

    public function change_status($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $medicine = $this->Medicine_model->toggle_status((int) $id);

        if (!$medicine) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to change medicine status.'
            ));
        }

        $message = ((int) $medicine['status'] === 1)
            ? 'Medicine activated successfully.'
            : 'Medicine deactivated successfully.';

        return $this->json_response(array(
            'status' => TRUE,
            'message' => $message,
            'data' => $medicine
        ));
    }

    protected function validate_medicine_form($medicine_id = NULL)
    {
        $post_data = $this->input->post(NULL, TRUE);

        $this->form_validation->reset_validation();
        $this->form_validation->set_data($post_data);

        $this->form_validation->set_rules('medicine_name', 'Medicine Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('generic_name', 'Generic Name', 'trim|max_length[150]');
        $this->form_validation->set_rules('unit', 'Unit', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('stock_quantity', 'Stock Quantity', 'trim|required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('unit_price', 'Unit Price', 'trim|required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'medicine_name' => form_error('medicine_name'),
                    'generic_name' => form_error('generic_name'),
                    'unit' => form_error('unit'),
                    'stock_quantity' => form_error('stock_quantity'),
                    'unit_price' => form_error('unit_price'),
                    'description' => form_error('description'),
                    'status' => form_error('status')
                )
            );
        }

        $medicine_name = trim((string) $this->input->post('medicine_name', TRUE));

        if ($this->Medicine_model->exists_by_name($medicine_name, $medicine_id)) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'medicine_name' => 'This medicine name already exists.'
                )
            );
        }

        $generic_name = trim((string) $this->input->post('generic_name', TRUE));
        $description = trim((string) $this->input->post('description', TRUE));

        return array(
            'status' => TRUE,
            'data' => array(
                'medicine_name' => $medicine_name,
                'generic_name' => ($generic_name === '') ? NULL : $generic_name,
                'unit' => trim((string) $this->input->post('unit', TRUE)),
                'stock_quantity' => (int) $this->input->post('stock_quantity', TRUE),
                'unit_price' => number_format((float) $this->input->post('unit_price', TRUE), 2, '.', ''),
                'description' => ($description === '') ? NULL : $description,
                'status' => (int) $this->input->post('status', TRUE)
            )
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
