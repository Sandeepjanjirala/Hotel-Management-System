<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Receptionist_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receptionist_model');
    }

    public function index()
    {
        $this->render('receptionist/dashboard/index', array(
            'page_title' => 'Receptionist Dashboard',
            'active_menu' => 'dashboard',
            'counts' => $this->Receptionist_model->dashboard_counts(),
            'today_appointments' => $this->Receptionist_model->todays_appointments(10),
            'recent_patients' => $this->Receptionist_model->recent_patients(8),
            'payment_summary' => $this->Receptionist_model->pending_payment_summary()
        ));
    }
}
