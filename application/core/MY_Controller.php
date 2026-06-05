<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shared application controller.
 *
 * CI3 has no native middleware layer, so role panels use thin base
 * controllers as guard middleware. Future panels can extend Role_Controller
 * with their own role value and model.
 */
class MY_Controller extends CI_Controller
{
    protected $layout = 'doctor/layouts/main';
    protected $panel_config = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->helper(array('url', 'html', 'form'));
        $this->load->library('session');
        $this->config->load('doctor_panel', true);
        $this->panel_config = $this->config->item('doctor_panel');
    }

    protected function render($view, $data = array(), $layout = null)
    {
        $data['content_view'] = $this->load->view($view, $data, true);
        $data['page_title'] = isset($data['page_title']) ? $data['page_title'] : 'Doctor Panel';
        $data['active_menu'] = isset($data['active_menu']) ? $data['active_menu'] : '';

        $this->load->view($layout ?: $this->layout, $data);
    }

    protected function json_response($payload, $status_code = 200)
    {
        return $this->output
            ->set_status_header($status_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    protected function require_role($roles)
    {
        $roles = is_array($roles) ? $roles : array($roles);

        if (!$this->session->userdata('logged_in') || !$this->current_user_id()) {
            if ($this->input->is_ajax_request()) {
                $this->json_response(array(
                    'success' => false,
                    'message' => 'Authentication required.'
                ), 401);
                exit;
            }

            redirect('login');
            exit;
        }

        $current_role = strtolower((string) $this->current_user_role());
        foreach ($roles as $role) {
            if ($current_role === strtolower((string) $role)) {
                return true;
            }
        }

        if ($this->input->is_ajax_request()) {
            $this->json_response(array(
                'success' => false,
                'message' => 'You are not allowed to access this page.'
            ), 403);
            exit;
        }

        show_error('You are not allowed to access this page.', 403, 'Access denied');
        exit;
    }

    protected function current_user_id()
    {
        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        $keys = isset($session_config['user_id_keys'])
            ? $session_config['user_id_keys']
            : array('user_id', 'id', 'login_user_id');

        return $this->session_first_value($keys);
    }

    protected function current_user_role()
    {
        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        $keys = isset($session_config['role_keys'])
            ? $session_config['role_keys']
            : array('role', 'role_name', 'user_role', 'user_type');

        return $this->session_first_value($keys);
    }

    protected function current_role()
    {
        return $this->current_user_role();
    }

    protected function current_user_name()
    {
        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        $keys = isset($session_config['name_keys'])
            ? $session_config['name_keys']
            : array('name', 'full_name', 'username', 'user_name', 'email');

        $name = $this->session_first_value($keys);
        return $name ?: 'User';
    }
    protected function current_user_email()
    {
        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        $keys = isset($session_config['email_keys'])
            ? $session_config['email_keys']
            : array('email', 'user_email');

        return $this->session_first_value($keys);
    }

    protected function session_first_value(array $keys)
    {
        foreach ($keys as $key) {
            $value = $this->session->userdata($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}

abstract class Role_Controller extends MY_Controller
{
    protected $panel_role = '';
    protected $auth_user_id;
    protected $auth_role;

    public function __construct()
    {
        parent::__construct();

        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        $user_id_keys = isset($session_config['user_id_keys']) ? $session_config['user_id_keys'] : array('user_id');
        $role_keys = isset($session_config['role_keys']) ? $session_config['role_keys'] : array('role');

        $this->auth_user_id = $this->session_first_value($user_id_keys);
        $this->auth_role = $this->session_first_value($role_keys);

        $this->validate_role_access();
    }

    protected function validate_role_access()
    {
        if (!$this->auth_user_id) {
            return $this->deny_access('Authentication required.', 401);
        }

        if (!$this->role_matches($this->auth_role)) {
            return $this->deny_access('Doctor role required.', 403);
        }
    }

    protected function role_matches($session_role)
    {
        if (!$this->panel_role) {
            return false;
        }

        $allowed = array($this->panel_role);
        $session_config = isset($this->panel_config['session']) ? $this->panel_config['session'] : array();
        if ($this->panel_role === 'doctor' && isset($session_config['doctor_role_values'])) {
            $allowed = $session_config['doctor_role_values'];
        }

        foreach ($allowed as $role) {
            if (strtolower((string) $session_role) === strtolower((string) $role)) {
                return true;
            }
        }

        return false;
    }

    protected function deny_access($message, $status_code)
    {
        if ($this->input->is_ajax_request()) {
            $this->json_response(array(
                'success' => false,
                'message' => $message
            ), $status_code);
            exit;
        }

        $uris = isset($this->panel_config['uris']) ? $this->panel_config['uris'] : array();
        $login_uri = isset($uris['login']) ? $uris['login'] : 'auth/login';

        if ($status_code !== 401) {
            $this->session->unset_userdata(array(
                'logged_in',
                'user_id',
                'id',
                'login_user_id',
                'user_name',
                'user_email',
                'name',
                'email',
                'role',
                'role_name',
                'user_role',
                'user_type'
            ));
            $this->session->set_flashdata('error', $message);
        }

        redirect($login_uri);
        exit;
    }
}

abstract class Doctor_Controller extends Role_Controller
{
    protected $panel_role = 'doctor';
    protected $doctor_user_id;
    protected $doctor_profile;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Doctor_model');
        $this->doctor_user_id = (int) $this->auth_user_id;
        $this->doctor_profile = $this->Doctor_model->get_doctor_by_user_id($this->doctor_user_id);

        if (!$this->doctor_profile) {
            log_message('debug', '[AUTH DEBUG] Doctor profile row missing for user_id=' . $this->doctor_user_id . '. Role-authenticated access allowed.');
            $this->doctor_profile = (object) array(
                'id' => null,
                'user_id' => $this->doctor_user_id,
                'name' => $this->current_user_name(),
                'user_name' => $this->current_user_name(),
                'email' => $this->current_user_email(),
                'user_email' => $this->current_user_email(),
                'department' => ''
            );
        }
    }

    protected function render($view, $data = array(), $layout = null)
    {
        $data['doctor_user_id'] = $this->doctor_user_id;
        $data['doctor_profile'] = $this->doctor_profile;
        $data['logout_uri'] = isset($this->panel_config['uris']['logout'])
            ? $this->panel_config['uris']['logout']
            : 'auth/logout';

        parent::render($view, $data, $layout);
    }
}
abstract class Receptionist_Controller extends MY_Controller
{
    protected $layout = 'receptionist/layouts/main';
    protected $receptionist_user_id;

    public function __construct()
    {
        parent::__construct();

        $this->require_receptionist_role();
        $this->receptionist_user_id = (int) $this->current_user_id();
    }

    protected function require_receptionist_role()
    {
        if (!$this->session->userdata('logged_in') || !$this->current_user_id()) {
            if ($this->input->is_ajax_request()) {
                $this->json_response(array(
                    'success' => false,
                    'message' => 'Authentication required.'
                ), 401);
                exit;
            }

            redirect('login');
            exit;
        }

        $current_role = strtolower((string) $this->current_user_role());
        if ($current_role === 'receptionist') {
            return true;
        }

        if ($this->input->is_ajax_request()) {
            $this->json_response(array(
                'success' => false,
                'message' => 'Receptionist role required.'
            ), 403);
            exit;
        }

        $this->session->set_flashdata('error', 'Receptionist role required.');
        redirect($this->role_home_uri($current_role));
        exit;
    }

    protected function role_home_uri($role)
    {
        switch (strtolower((string) $role)) {
            case 'admin':
                return 'admin';
            case 'doctor':
                return 'doctor';
            case 'patient':
                return 'patient';
            case 'receptionist':
                return 'receptionist';
            default:
                return 'login';
        }
    }

    protected function render($view, $data = array(), $layout = null)
    {
        $data['receptionist_user_id'] = $this->receptionist_user_id;
        $data['current_user_name'] = $this->current_user_name();
        $data['current_user_role'] = $this->current_role();
        $data['logout_uri'] = 'logout';

        parent::render($view, $data, $layout ?: $this->layout);
    }
}
