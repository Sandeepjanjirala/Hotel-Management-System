<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rec_dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role('receptionist');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Receptionist Dashboard',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => ucfirst($this->current_role())
        );

        $this->load->view('layouts/header', $data);
        $this->load->view('receptionist/dashboard', $data);
        $this->load->view('layouts/footer');
    }
}
