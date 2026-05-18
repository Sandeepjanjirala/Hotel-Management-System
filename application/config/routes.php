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

$route['admin/lab-tests'] = 'admin/admin_lab/tests';
$route['admin/lab-tests/ajax-list'] = 'admin/admin_lab/get_tests_ajax';
$route['admin/lab-tests/store'] = 'admin/admin_lab/store_test';
$route['admin/lab-tests/edit/(:num)'] = 'admin/admin_lab/edit_test/$1';
$route['admin/lab-tests/update/(:num)'] = 'admin/admin_lab/update_test/$1';
$route['admin/lab-tests/change-status/(:num)'] = 'admin/admin_lab/change_test_status/$1';

$route['admin/lab-requests'] = 'admin/admin_lab/requests';
$route['admin/lab-requests/ajax-list'] = 'admin/admin_lab/get_requests_ajax';
$route['admin/lab-requests/create'] = 'admin/admin_lab/create_request';
$route['admin/lab-requests/store'] = 'admin/admin_lab/store_request';
$route['admin/lab-requests/appointment/(:num)'] = 'admin/admin_lab/get_appointment_ajax/$1';
$route['admin/lab-requests/change-status/(:num)'] = 'admin/admin_lab/change_request_status/$1';
$route['admin/lab-requests/results/(:num)'] = 'admin/admin_lab/results/$1';
$route['admin/lab-requests/save-results/(:num)'] = 'admin/admin_lab/save_results/$1';


/*
|--------------------------------------------------------------------------
| Medicines
|--------------------------------------------------------------------------
*/

$route['admin/medicines'] = 'admin/admin_prescriptions/medicines';
$route['admin/medicines/ajax-list'] = 'admin/admin_prescriptions/get_medicines_ajax';
$route['admin/medicines/store'] = 'admin/admin_prescriptions/store_medicine';
$route['admin/medicines/edit/(:num)'] = 'admin/admin_prescriptions/edit_medicine/$1';
$route['admin/medicines/update/(:num)'] = 'admin/admin_prescriptions/update_medicine/$1';
$route['admin/medicines/change-status/(:num)'] = 'admin/admin_prescriptions/change_medicine_status/$1';

/*
|--------------------------------------------------------------------------
| Prescriptions
|--------------------------------------------------------------------------
*/

$route['admin/prescriptions'] = 'admin/admin_prescriptions/index';
$route['admin/prescriptions/ajax-list'] = 'admin/admin_prescriptions/get_prescriptions_ajax';
$route['admin/prescriptions/create'] = 'admin/admin_prescriptions/create';
$route['admin/prescriptions/store'] = 'admin/admin_prescriptions/store';

/*
|--------------------------------------------------------------------------
| Pharmacy Billing
|--------------------------------------------------------------------------
*/

$route['admin/pharmacy-billing'] = 'admin/admin_pharmacy/index';
$route['admin/pharmacy-billing/ajax-list'] = 'admin/admin_pharmacy/get_bills_ajax';
$route['admin/pharmacy-billing/create'] = 'admin/admin_pharmacy/create';
$route['admin/pharmacy-billing/store'] = 'admin/admin_pharmacy/store';

