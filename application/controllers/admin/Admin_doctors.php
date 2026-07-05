<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Admin_doctors extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->require_role(array('admin'));
        $this->load->model('Doctor_model'); // Make sure this model exists
        $this->form_validation->set_error_delimiters('', '');
    }

    public function index()
    {
        $data = array(
            'page_title' => 'Doctor Profile Setup',
            'active_menu' => 'doctors',
            'current_user_name' => $this->current_user_name(),
            'current_user_role' => $this->current_role()
        );

        $this->load->view('admin/layouts/header', $data);
        $this->load->view('admin/layouts/navbar', $data);
        $this->load->view('admin/layouts/sidebar', $data);
        $this->load->view('admin/doctors/index', $data); // This should be the doctor view
        $this->load->view('admin/layouts/footer', $data);
    }

    public function get_doctors_ajax()
    {
        return $this->json_response(array(
            'status' => TRUE,
            'data' => $this->Admin_doctor_model->get_all()
        ));
    }

    public function get_setup_options()
    {
        return $this->json_response(array(
            'status' => TRUE,
            'doctor_users' => $this->Doctor_model->get_unprofiled_doctor_users(),
            'departments' => $this->Doctor_model->get_active_departments()
        ));
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $validated = $this->validate_doctor_form('create');

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $created = $this->Doctor_model->create_profile($validated['data']);

        if (!$created) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to create doctor profile at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Doctor profile created successfully.'
        ));
    }

    public function edit($id)
    {
        $doctor = $this->Doctor_model->get_by_id((int) $id);

        if (empty($doctor)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Doctor profile not found.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'data' => $doctor,
            'departments' => $this->Doctor_model->get_active_departments()
        ));
    }

    public function update($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $doctor = $this->Doctor_model->get_by_id((int) $id);

        if (empty($doctor)) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Doctor profile not found.'
            ));
        }

        $validated = $this->validate_doctor_form('update');

        if ($validated['status'] === FALSE) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validated['errors']
            ));
        }

        $updated = $this->Doctor_model->update_profile((int) $id, $validated['data']);

        if (!$updated) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to update doctor profile at the moment.'
            ));
        }

        return $this->json_response(array(
            'status' => TRUE,
            'message' => 'Doctor profile updated successfully.'
        ));
    }

    public function change_status($id)
    {
        if ($this->input->method() !== 'post') {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Invalid request method.'
            ));
        }

        $doctor = $this->Doctor_model->toggle_status((int) $id);

        if (!$doctor) {
            return $this->json_response(array(
                'status' => FALSE,
                'message' => 'Unable to change doctor profile status.'
            ));
        }

        $message = ((int) $doctor['status'] === 1)
            ? 'Doctor profile activated successfully.'
            : 'Doctor profile deactivated successfully.';

        return $this->json_response(array(
            'status' => TRUE,
            'message' => $message,
            'data' => $doctor
        ));
    }

    protected function validate_doctor_form($mode)
    {
        $post_data = $this->input->post(NULL, TRUE);

        $this->form_validation->reset_validation();
        $this->form_validation->set_data($post_data);

        if ($mode === 'create') {
            $this->form_validation->set_rules('user_id', 'Doctor User', 'trim|required|integer');
        }

        $this->form_validation->set_rules('department_id', 'Department', 'trim|required|integer');
        $this->form_validation->set_rules('specialization', 'Specialization', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('qualification', 'Qualification', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('consultation_fee', 'Consultation Fee', 'trim|required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === FALSE) {
            return array(
                'status' => FALSE,
                'errors' => array(
                    'user_id' => form_error('user_id'),
                    'department_id' => form_error('department_id'),
                    'specialization' => form_error('specialization'),
                    'qualification' => form_error('qualification'),
                    'consultation_fee' => form_error('consultation_fee'),
                    'available_days' => '',
                    'status' => form_error('status')
                )
            );
        }

        $errors = array();
        $department_id = (int) $this->input->post('department_id', TRUE);
        $available_days = $this->input->post('available_days', TRUE);
        $allowed_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

        if (!$this->Doctor_model->department_exists($department_id)) {
            $errors['department_id'] = 'Please select a valid active department.';
        }

        if (empty($available_days) || !is_array($available_days)) {
            $errors['available_days'] = 'Please select at least one available day.';
        } else {
            foreach ($available_days as $day) {
                if (!in_array($day, $allowed_days, TRUE)) {
                    $errors['available_days'] = 'Please select valid available days.';
                    break;
                }
            }
        }

        if ($mode === 'create') {
            $user_id = (int) $this->input->post('user_id', TRUE);

            if (!$this->Doctor_model->user_is_available_doctor($user_id)) {
                $errors['user_id'] = 'Please select a valid doctor user without an existing profile.';
            }
        }

        if (!empty($errors)) {
            return array(
                'status' => FALSE,
                'errors' => $errors
            );
        }

        $data = array(
            'department_id' => $department_id,
            'specialization' => trim((string) $this->input->post('specialization', TRUE)),
            'qualification' => trim((string) $this->input->post('qualification', TRUE)),
            'consultation_fee' => number_format((float) $this->input->post('consultation_fee', TRUE), 2, '.', ''),
            'available_days' => implode(',', $available_days),
            'status' => (int) $this->input->post('status', TRUE)
        );

        if ($mode === 'create') {
            $data['user_id'] = (int) $this->input->post('user_id', TRUE);
        }

        return array(
            'status' => TRUE,
            'data' => $data
        );
    }

    protected function json_response($response, $status_code = 200)
    {
        $response['csrfHash'] = $this->security->get_csrf_hash();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
