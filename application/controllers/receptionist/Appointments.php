<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointments extends Receptionist_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receptionist_model');
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="text-danger small">', '</div>');
    }

    public function index()
    {
        $this->render('receptionist/appointments/index', array(
            'page_title' => 'Appointments',
            'active_menu' => 'appointments',
            'departments' => $this->Receptionist_model->get_departments()
        ));
    }

    public function ajax_list()
    {
        $filters = array(
            'search' => trim((string) $this->input->get('search', true)),
            'status' => trim((string) $this->input->get('status', true)),
            'appointment_date' => trim((string) $this->input->get('appointment_date', true)),
            'doctor_id' => trim((string) $this->input->get('doctor_id', true))
        );

        return $this->json_response(array(
            'status' => true,
            'data' => $this->Receptionist_model->get_appointments($filters, 75, 0)
        ));
    }

    public function create()
    {
        $this->render('receptionist/appointments/form', array(
            'page_title' => 'Book Appointment',
            'active_menu' => 'appointments',
            'appointment' => null,
            'form_action' => 'receptionist/appointments/store',
            'patients' => $this->Receptionist_model->get_patients(array(), 500, 0),
            'departments' => $this->Receptionist_model->get_departments(),
            'doctors' => $this->Receptionist_model->get_doctors()
        ));
    }

    public function store()
    {
        if (!$this->validate_appointment()) {
            $this->create();
            return;
        }

        $created = $this->Receptionist_model->create_appointment($this->appointment_payload(), $this->receptionist_user_id);

        $this->session->set_flashdata($created ? 'success' : 'error', $created ? 'Appointment booked successfully.' : 'Slot unavailable or appointment could not be booked.');
        redirect('receptionist/appointments');
    }

    public function edit($id)
    {
        $appointment = $this->Receptionist_model->get_appointment((int) $id);
        if (!$appointment) {
            show_404();
            return;
        }

        $this->render('receptionist/appointments/form', array(
            'page_title' => 'Edit Appointment',
            'active_menu' => 'appointments',
            'appointment' => $appointment,
            'form_action' => 'receptionist/appointments/update/' . (int) $id,
            'patients' => $this->Receptionist_model->get_patients(array(), 500, 0),
            'departments' => $this->Receptionist_model->get_departments(),
            'doctors' => $this->Receptionist_model->get_doctors()
        ));
    }

    public function update($id)
    {
        if (!$this->Receptionist_model->get_appointment((int) $id)) {
            show_404();
            return;
        }

        if (!$this->validate_appointment()) {
            $this->edit((int) $id);
            return;
        }

        $updated = $this->Receptionist_model->update_appointment((int) $id, $this->appointment_payload());

        $this->session->set_flashdata($updated ? 'success' : 'error', $updated ? 'Appointment updated successfully.' : 'Slot unavailable or appointment could not be updated.');
        redirect('receptionist/appointments');
    }

    public function cancel($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('receptionist/appointments');
            return;
        }

        $cancelled = $this->Receptionist_model->cancel_appointment((int) $id);

        return $this->json_response(array(
            'status' => (bool) $cancelled,
            'message' => $cancelled ? 'Appointment cancelled.' : 'Unable to cancel appointment.'
        ));
    }

    public function doctors()
    {
        return $this->json_response(array(
            'status' => true,
            'data' => $this->Receptionist_model->get_doctors($this->input->get('department_id', true))
        ));
    }

    public function slots()
    {
        $doctor_id = (int) $this->input->get('doctor_id', true);
        $date = trim((string) $this->input->get('appointment_date', true));
        $appointment_id = $this->input->get('appointment_id', true);

        return $this->json_response(array(
            'status' => true,
            'data' => ($doctor_id && $date) ? $this->Receptionist_model->get_available_slots($doctor_id, $date, $appointment_id ?: null) : array()
        ));
    }

    protected function validate_appointment()
    {
        $this->form_validation->set_rules('patient_id', 'Patient', 'trim|required|integer');
        $this->form_validation->set_rules('doctor_id', 'Doctor', 'trim|required|integer');
        $this->form_validation->set_rules('appointment_date', 'Appointment Date', 'trim|required');
        $this->form_validation->set_rules('appointment_time', 'Appointment Time', 'trim|required');
        $this->form_validation->set_rules('reason', 'Reason', 'trim|max_length[255]');
        $this->form_validation->set_rules('notes', 'Notes', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[scheduled,completed,cancelled]');

        return $this->form_validation->run();
    }

    protected function appointment_payload()
    {
        return array(
            'patient_id' => (int) $this->input->post('patient_id', true),
            'doctor_id' => (int) $this->input->post('doctor_id', true),
            'appointment_date' => trim((string) $this->input->post('appointment_date', true)),
            'appointment_time' => trim((string) $this->input->post('appointment_time', true)),
            'reason' => trim((string) $this->input->post('reason', true)) ?: null,
            'notes' => trim((string) $this->input->post('notes', true)) ?: null,
            'status' => trim((string) $this->input->post('status', true))
        );
    }
}