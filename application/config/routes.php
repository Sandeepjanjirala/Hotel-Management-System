<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'auth/index';
$route['auth/login'] = 'auth/login';
$route['logout'] = 'auth/logout';

$route['admin'] = 'admin/dashboard';
$route['doctor'] = 'doctor/doctor_dashboard';
$route['receptionist'] = 'receptionist/rec_dashboard';
$route['patient'] = 'patient/patient_dashboard';

$route['admin/departments'] = 'admin/departments/index';
$route['admin/departments/store'] = 'admin/departments/store';
$route['admin/departments/edit/(:num)'] = 'admin/departments/edit/$1';
$route['admin/departments/update/(:num)'] = 'admin/departments/update/$1';
$route['admin/departments/change-status/(:num)'] = 'admin/departments/change_status/$1';

$route['admin/users'] = 'admin/users/index';
$route['admin/users/ajax-list'] = 'admin/users/get_users_ajax';
$route['admin/users/store'] = 'admin/users/store';
$route['admin/users/edit/(:num)'] = 'admin/users/edit/$1';
$route['admin/users/update/(:num)'] = 'admin/users/update/$1';
$route['admin/users/change-status/(:num)'] = 'admin/users/change_status/$1';

$route['admin/doctors'] = 'admin/admin_doctors/index';
$route['admin/doctors/ajax-list'] = 'admin/admin_doctors/get_doctors_ajax';
$route['admin/doctors/options'] = 'admin/admin_doctors/get_setup_options';
$route['admin/doctors/store'] = 'admin/admin_doctors/store';
$route['admin/doctors/edit/(:num)'] = 'admin/admin_doctors/edit/$1';
$route['admin/doctors/update/(:num)'] = 'admin/admin_doctors/update/$1';
$route['admin/doctors/change-status/(:num)'] = 'admin/admin_doctors/change_status/$1';

$route['admin/medicines'] = 'admin/admin_medicines/index';
$route['admin/medicines/ajax-list'] = 'admin/admin_medicines/get_medicines_ajax';
$route['admin/medicines/store'] = 'admin/admin_medicines/store';
$route['admin/medicines/edit/(:num)'] = 'admin/admin_medicines/edit/$1';
$route['admin/medicines/update/(:num)'] = 'admin/admin_medicines/update/$1';
$route['admin/medicines/change-status/(:num)'] = 'admin/admin_medicines/change_status/$1';

$route['admin/prescriptions'] = 'admin/admin_prescriptions/index';
$route['admin/prescriptions/ajax-list'] = 'admin/admin_prescriptions/get_prescriptions_ajax';
$route['admin/prescriptions/create'] = 'admin/admin_prescriptions/create';
$route['admin/prescriptions/create/(:num)'] = 'admin/admin_prescriptions/create/$1';
$route['admin/prescriptions/store'] = 'admin/admin_prescriptions/store';
$route['admin/prescriptions/appointment/(:num)'] = 'admin/admin_prescriptions/get_appointment_ajax/$1';
$route['admin/prescriptions/medicine-row'] = 'admin/admin_prescriptions/medicine_row';

$route['admin/patients'] = 'admin/patients/index';
$route['admin/patients/ajax-list'] = 'admin/patients/get_patients_ajax';
$route['admin/patients/create'] = 'admin/patients/create';
$route['admin/patients/store'] = 'admin/patients/store';
$route['admin/patients/edit/(:num)'] = 'admin/patients/edit/$1';
$route['admin/patients/update/(:num)'] = 'admin/patients/update/$1';
$route['admin/patients/change-status/(:num)'] = 'admin/patients/change_status/$1';

$route['admin/appointments'] = 'admin/appointments/index';
$route['admin/appointments/ajax-list'] = 'admin/appointments/get_appointments_ajax';
$route['admin/appointments/create'] = 'admin/appointments/create';
$route['admin/appointments/store'] = 'admin/appointments/store';
$route['admin/appointments/edit/(:num)'] = 'admin/appointments/edit/$1';
$route['admin/appointments/update/(:num)'] = 'admin/appointments/update/$1';
$route['admin/appointments/change-status/(:num)'] = 'admin/appointments/change_status/$1';
