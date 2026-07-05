<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin'));
        $this->load->model('User_management_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'User Management',
            'active_menu' => 'users',
            'current_user_name' => $this->current_user_name(),
            'departments' => $this->User_management_model->get_active_departments()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/users/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }

    public function get_users_ajax()
    {
        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->User_management_model->get_all_users()
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

        $validated = $this->validate_user_form('create');

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $created = $this->User_management_model->insert_user($validated['data']);

        if (!$created) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to create user at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'User created successfully.'
        ));
    }

    public function edit($id)
    {
        $user = $this->User_management_model->get_user_by_id((int) $id);

        if (empty($user)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'User not found.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $user
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

        $user = $this->User_management_model->get_user_by_id((int) $id);

        if (empty($user)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'User not found.'
            ));
        }

        $validated = $this->validate_user_form('update', (int) $id);

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $updated = $this->User_management_model->update_user((int) $id, $validated['data']);

        if (!$updated) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to update user at the moment.'
            ));
        }

        if ((int) $id === (int) $this->current_user_id()) {
            $this->session->set_userdata('user_name', $validated['data']['name']);
            $this->session->set_userdata('user_role', $validated['data']['role']);
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'User updated successfully.'
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

        if ((int) $id === (int) $this->current_user_id()) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'You cannot change your own account status.'
            ));
        }

        $user = $this->User_management_model->toggle_status((int) $id);

        if (!$user) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to change user status.'
            ));
        }

        $message = ((int) $user['status'] === 1)
            ? 'User activated successfully.'
            : 'User deactivated successfully.';

        return $this->json_response(array(
            'status' => TRUE,
            'message' => $message,
            'data' => $user
        ));
    }

    protected function validate_user_form($mode, $user_id = NULL)
    {
        $post_data = $this->input->post(NULL, TRUE);

        $this->form_validation->reset_validation();
        $this->form_validation->set_data($post_data);

        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|in_list[admin,doctor,receptionist]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[30]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        if ($mode === 'create') {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
        }

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'name' => form_error('name'),
                    'email' => form_error('email'),
                    'password' => form_error('password'),
                    'role' => form_error('role'),
                    'department_id' => '',
                    'phone' => form_error('phone'),
                    'status' => form_error('status')
                )
            );
        }

        $name = trim((string) $this->input->post('name', TRUE));
        $email = strtolower(trim((string) $this->input->post('email', TRUE)));
        $password = (string) $this->input->post('password', FALSE);
        $role = trim((string) $this->input->post('role', TRUE));
        $department_id = $this->input->post('department_id', TRUE);
        $phone = trim((string) $this->input->post('phone', TRUE));
        $status = (int) $this->input->post('status', TRUE);

        $errors = array();

        if ($this->User_management_model->exists_by_email($email, $user_id)) {
            $errors['email'] = 'This email address is already registered.';
        }

        if (in_array($role, array('doctor', 'receptionist'), TRUE)) {
            if ($department_id === NULL || $department_id === '') {
                $errors['department_id'] = 'Department is required for this role.';
            } elseif (!$this->User_management_model->active_department_exists((int) $department_id)) {
                $errors['department_id'] = 'Please select a valid active department.';
            }
        }

        if ($role === 'admin') {
            $department_id = NULL;
        } elseif ($department_id !== NULL && $department_id !== '') {
            $department_id = (int) $department_id;
        }

        if (!empty($errors)) {
            return array(
                'status' => FALSE,
                'errors' => $errors
            );
        }

        $data = array(
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'department_id' => $department_id,
            'phone' => ($phone === '') ? NULL : $phone,
            'status' => $status
        );

        if ($mode === 'create' || $password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($mode === 'create') {
            $data['profile_photo'] = NULL;
        }

        return array(
            'status' => TRUE,
            'data' => $data
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
