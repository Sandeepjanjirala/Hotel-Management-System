<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $current_user_id = NULL;
    protected $current_user_name = NULL;
    protected $current_user_role = NULL;

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            exit;
        }

        $this->current_user_id = (int) $this->session->userdata('user_id');
        $this->current_user_name = $this->session->userdata('user_name');
        $this->current_user_role = $this->session->userdata('user_role');
    }

    protected function is_role($role)
    {
        return $this->current_user_role === $role;
    }

    protected function require_role($roles)
    {
        $roles = (array) $roles;

        if (!in_array($this->current_user_role, $roles, TRUE)) {
            show_error('You are not authorized to access this page.', 403, 'Forbidden');
            exit;
        }
    }

    protected function current_user_id()
    {
        return $this->current_user_id;
    }

    protected function current_user_name()
    {
        return $this->current_user_name;
    }

    protected function current_role()
    {
        return $this->current_user_role;
    }
}
