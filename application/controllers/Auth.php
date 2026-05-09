<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect($this->dashboard_uri($this->session->userdata('user_role')));
            return;
        }

        $data = array(
            'page_title' => 'Login'
        );

        $this->load->view('auth/login', $data);
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect($this->dashboard_uri($this->session->userdata('user_role')));
            return;
        }

        if ($this->input->method() !== 'post') {
            redirect('login');
            return;
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = array(
                'page_title' => 'Login'
            );

            $this->load->view('auth/login', $data);
            return;
        }

        $email = strtolower(trim($this->input->post('email', TRUE)));
        $password = (string) $this->input->post('password', FALSE);

        $user = $this->User_model->get_by_email($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('login');
            return;
        }

        if ((int) $user['status'] !== 1) {
            $this->session->set_flashdata('error', 'Your account is disabled. Please contact the administrator.');
            redirect('login');
            return;
        }

        $this->session->sess_regenerate(TRUE);

        $session_data = array(
            'user_id' => (int) $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
            'logged_in' => TRUE
        );

        $this->session->set_userdata($session_data);
        $this->User_model->update_last_login($user['id']);

        redirect($this->dashboard_uri($user['role']));
    }

    public function logout()
    {
        if ($this->session->userdata('logged_in')) {
            $this->session->sess_destroy();
        }

        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        redirect('login');
    }

    private function dashboard_uri($role)
    {
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
                return 'login';
        }
    }
}
// echo '<pre>';
// print_r($user);
// exit;