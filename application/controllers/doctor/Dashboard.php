<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Doctor_Controller
{
    public function index()
    {
        $data = array(
            'page_title' => 'Doctor Dashboard',
            'active_menu' => 'dashboard',
            'stats' => $this->Doctor_model->get_dashboard_counts($this->doctor_user_id),
            'today_appointments' => $this->Doctor_model->get_appointments(
                $this->doctor_user_id,
                array('date' => date('Y-m-d')),
                10,
                0
            )
        );

        $this->render('doctor/dashboard/index', $data);
    }

    public function stats()
    {
        return $this->json_response(array(
            'success' => true,
            'data' => $this->Doctor_model->get_dashboard_counts($this->doctor_user_id)
        ));
    }
}