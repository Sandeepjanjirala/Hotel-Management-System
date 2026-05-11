<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin'));
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Admin Dashboard',
            'active_menu' => 'dashboard',
            'current_user_name' => $this->current_user_name(),
            'statistics' => $this->Dashboard_model->get_statistics(),
            'status_summary' => $this->Dashboard_model->get_status_summary(),
            'recent_users' => $this->Dashboard_model->get_recent_users(5),
            'recent_departments' => $this->Dashboard_model->get_recent_departments(5)
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/dashboard/index', $data);
        $this->load->view('admin/layouts/footer', $data);
    }
}
