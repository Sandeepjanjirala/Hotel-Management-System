<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role(array('admin'));
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Admin Dashboard',
            'active_menu' => 'dashboard',
            'current_user_name' => $this->current_user_name()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }
}
