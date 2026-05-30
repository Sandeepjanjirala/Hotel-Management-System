<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Doctor Panel Configuration
|--------------------------------------------------------------------------
|
| These values keep the Phase 4A doctor panel portable across HMS installs.
| Rename table/column values here if your existing database uses different
| names, without changing controller or view code.
|
*/

$config['session'] = array(
    'user_id_keys' => array('user_id', 'id', 'login_user_id'),
    'role_keys' => array('role', 'role_name', 'user_role', 'user_type'),
    'name_keys' => array('name', 'full_name', 'username', 'user_name', 'email'),
    'email_keys' => array('email', 'user_email'),
    'doctor_role_values' => array('doctor', 'Doctor', 'DOCTOR')
);

$config['uris'] = array(
    'login' => 'auth/login',
    'logout' => 'auth/logout'
);

$config['tables'] = array(
    'users' => 'users',
    'doctors' => 'doctors',
    'patients' => 'patients',
    'appointments' => 'appointments',
    'prescriptions' => 'prescriptions',
    'prescription_items' => 'prescription_items',
    'lab_requests' => 'patient_lab_tests',
    'lab_request_tests' => 'patient_lab_test_items'
);
