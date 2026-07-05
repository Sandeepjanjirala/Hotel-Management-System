<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $allowed_roles = array('admin', 'doctor', 'receptionist', 'patient');

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            $target = $this->dashboard_uri($this->current_session_role());
            if ($target) {
                $this->auth_debug('Active session found. Redirect target: ' . $target);
                redirect($target);
                return;
            }

            $this->clear_login_session();
            $this->session->set_flashdata('error', 'Login failed: role mismatch.');
            redirect('login');
            return;
        }

        $data = array(
            'page_title' => 'Login'
        );

        $this->load->view('auth/login', $data);
    }

    public function login(){
    // Check if request is AJAX
    $is_ajax = $this->input->is_ajax_request();
    
    // Check if user is already logged in
    if ($this->session->userdata('logged_in')) {
        $target = $this->dashboard_uri($this->current_session_role());
        if ($target) {
            $this->auth_debug('Active session found. Redirect target: ' . $target);
            if ($is_ajax) {
                echo json_encode(['status' => 'success', 'redirect' => $target]);
                return;
            }
            redirect($target);
            return;
        }

        $this->clear_login_session();
        $this->session->set_flashdata('error', 'Login failed: role mismatch.');
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Login failed: role mismatch.']);
            return;
        }
        redirect('login');
        return;
    }

    // Check if request method is POST
    if ($this->input->method() !== 'post') {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Invalid request method.']);
            return;
        }
        redirect('login');
        return;
    }

    // Set validation rules
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required');

    // Check if form validation passed
    if ($this->form_validation->run() === FALSE) {
        $errors = $this->form_validation->error_array();
        $error_message = implode(' ', $errors);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => $error_message]);
            return;
        }
        
        $data = array(
            'page_title' => 'Login'
        );
        $this->load->view('auth/login', $data);
        return;
    }

    // Get and sanitize input
    $email = strtolower(trim($this->input->post('email', TRUE)));
    $password = (string) $this->input->post('password', FALSE);

    $this->auth_debug('Email submitted: ' . $email);

    // Get user by email
    $user = $this->User_model->get_by_email($email);

    // Check if user exists
    if (!$user) {
        $this->auth_debug('Login failed: user not found for email ' . $email);
        $this->session->set_flashdata('error', 'Email address not found. Please check your email or create an account.');
        $this->session->set_flashdata('email', $email);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Email address not found. Please check your email or create an account.']);
            return;
        }
        redirect('login');
        return;
    }

    $this->auth_debug('User found: id=' . $user['id']);
    $this->auth_debug('Role detected: ' . $user['role']);

    // Verify password
    if (!password_verify($password, $user['password'])) {
        $this->auth_debug('Login failed: password mismatch for user_id=' . $user['id']);
        $this->session->set_flashdata('error', 'Incorrect password. Please try again.');
        $this->session->set_flashdata('email', $email);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Incorrect password. Please try again.']);
            return;
        }
        redirect('login');
        return;
    }

    // Check if account is active
    if ((int) $user['status'] !== 1) {
        $this->auth_debug('Login failed: disabled account for user_id=' . $user['id']);
        $this->session->set_flashdata('error', 'Your account is disabled. Please contact the administrator.');
        $this->session->set_flashdata('email', $email);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Your account is disabled. Please contact the administrator.']);
            return;
        }
        redirect('login');
        return;
    }

    // Validate user role
    $role = strtolower(trim((string) $user['role']));
    $redirect_target = $this->dashboard_uri($role);

    if (!$redirect_target || !in_array($role, $this->allowed_roles, TRUE)) {
        $this->auth_debug('Login failed: role mismatch for user_id=' . $user['id'] . ', role=' . $user['role']);
        $this->session->set_flashdata('error', 'Login failed: role mismatch.');
        $this->session->set_flashdata('email', $email);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Login failed: role mismatch.']);
            return;
        }
        redirect('login');
        return;
    }

    // Regenerate session ID for security
    $this->session->sess_regenerate(TRUE);

    // Set session data
    $session_data = array(
        'user_id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $role,
        'user_name' => $user['name'],
        'user_email' => $user['email'],
        'user_role' => $role,
        'logged_in' => TRUE
    );

    $this->session->set_userdata($session_data);

    // Verify session was set correctly
    if (!$this->session->userdata('logged_in')
        || (int) $this->session->userdata('user_id') !== (int) $user['id']
        || strtolower((string) $this->current_session_role()) !== $role
    ) {
        $this->auth_debug('Login failed: session failure for user_id=' . $user['id']);
        $this->clear_login_session();
        $this->session->set_flashdata('error', 'Login failed: session failure.');
        $this->session->set_flashdata('email', $email);
        
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'error' => 'Login failed: session failure.']);
            return;
        }
        redirect('login');
        return;
    }

    // Update last login timestamp
    $this->User_model->update_last_login($user['id']);

    $this->auth_debug('Login success: user_id=' . $user['id'] . ', role=' . $role);
    $this->auth_debug('Redirect target: ' . $redirect_target);

    // Return success response
    if ($is_ajax) {
        echo json_encode(['status' => 'success', 'redirect' => $redirect_target, 'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $role
        ]]);
        return;
    }

    redirect($redirect_target);
}

    public function logout()
    {
        $this->clear_login_session();
        $this->session->sess_destroy();

        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        redirect('login');
    }

    private function dashboard_uri($role)
    {
        $role = strtolower(trim((string) $role));

        switch ($role) {
            case 'admin':
                return 'admin';

            case 'doctor':
                return 'doctor';

            case 'receptionist':
                return 'receptionist';

            case 'patient':
                return 'patient';

            default:
                return false;
        }
    }

    private function current_session_role()
    {
        return $this->session->userdata('role') ?: $this->session->userdata('user_role');
    }

    private function clear_login_session()
    {
        $this->session->unset_userdata(array(
            'logged_in',
            'user_id',
            'name',
            'email',
            'role',
            'user_name',
            'user_email',
            'user_role'
        ));
    }

    private function auth_debug($message)
    {
        log_message('debug', '[AUTH DEBUG] ' . $message);
    }
}