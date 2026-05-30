<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin'));
        $this->load->model('Department_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Department Management',
            'active_menu' => 'departments',
            'current_user_name' => $this->current_user_name()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/departments/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function get_departments_ajax()
    {
        $departments = $this->Department_model->get_all();

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $departments
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

        $validated = $this->validate_department_form();

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $insert_id = $this->Department_model->insert($validated['data']);

        if (!$insert_id) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to create department at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Department created successfully.'
        ));
    }

    public function edit($id)
    {
        $department = $this->Department_model->get_by_id((int) $id);

        if (empty($department)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Department not found.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $department
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

        $department = $this->Department_model->get_by_id((int) $id);

        if (empty($department)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Department not found.'
            ));
        }

        $validated = $this->validate_department_form((int) $id);

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $updated = $this->Department_model->update((int) $id, $validated['data']);

        if (!$updated) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to update department at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Department updated successfully.'
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

        $department = $this->Department_model->toggle_status((int) $id);

        if (!$department) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to change department status.'
            ));
        }

        $message = ((int) $department['status'] === 1)
            ? 'Department activated successfully.'
            : 'Department deactivated successfully.';

        return $this->json_response(array(
            'status' => TRUE,
            'message' => $message,
            'data' => $department
        ));
    }

    protected function validate_department_form($department_id = NULL)
    {
        $post_data = $this->input->post(NULL, TRUE);

        $this->form_validation->reset_validation();
        $this->form_validation->set_data($post_data);

        $this->form_validation->set_rules('department_name', 'Department Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('description', 'Description', 'trim');

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'department_name' => form_error('department_name'),
                    'description' => form_error('description')
                )
            );
        }

        $department_name = trim((string) $this->input->post('department_name', TRUE));

        if ($this->Department_model->exists_by_name($department_name, $department_id)) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'department_name' => 'This department name already exists.'
                )
            );
        }

        return array(
            'status' => TRUE,
            'data' => array(
                'department_name' => $department_name,
                'description' => $this->normalize_description($this->input->post('description', TRUE))
            )
        );
    }

    protected function normalize_description($description)
    {
        $description = trim((string) $description);

        return ($description === '') ? NULL : $description;
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
