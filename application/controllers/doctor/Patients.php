<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends Doctor_Controller
{
    public function index()
    {
        $this->render('doctor/patients/index', array(
            'page_title' => 'Assigned Patients',
            'active_menu' => 'patients'
        ));
    }

    public function ajax_list()
    {
        $search = $this->input->post('search');
        $filters = array(
            'search' => is_array($search) && isset($search['value']) ? $search['value'] : $this->input->post('search_text')
        );
        $limit = (int) $this->input->post('length');
        $offset = (int) $this->input->post('start');
        $draw = (int) $this->input->post('draw');

        if ($limit <= 0) {
            $limit = 25;
        }

        $patients = $this->Doctor_model->get_assigned_patients($this->doctor_user_id, $filters, $limit, $offset);
        $records_total = $this->Doctor_model->count_assigned_patients($this->doctor_user_id);
        $records_filtered = $this->Doctor_model->count_assigned_patients($this->doctor_user_id, $filters);

        return $this->json_response(array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_filtered,
            'data' => $this->patient_rows($patients)
        ));
    }

    public function view($patient_id)
    {
        $patient = $this->Doctor_model->get_assigned_patient($this->doctor_user_id, $patient_id);
        if (!$patient) {
            show_404();
        }

        $appointments = $this->Doctor_model->get_appointments($this->doctor_user_id, array(
            'search' => $patient->mobile
        ), 50, 0);

        $this->render('doctor/patients/view', array(
            'page_title' => 'Patient Profile',
            'active_menu' => 'patients',
            'patient' => $patient,
            'appointments' => $appointments
        ));
    }

    protected function patient_rows($patients)
    {
        $rows = array();

        foreach ($patients as $patient) {
            $rows[] = array(
                'patient_id' => (int) $patient->id,
                'name' => html_escape($patient->name),
                'mobile' => html_escape($patient->mobile),
                'email' => html_escape(isset($patient->email) ? $patient->email : ''),
                'latest_appointment_at' => html_escape($patient->latest_appointment_at),
                'appointment_count' => (int) $patient->appointment_count,
                'actions' => anchor(
                    'doctor/patients/view/' . (int) $patient->id,
                    '<i class="fa fa-eye"></i>',
                    array('class' => 'btn btn-xs btn-default', 'title' => 'View patient')
                )
            );
        }

        return $rows;
    }
}